// @ts-check
const { test, expect } = require('@playwright/test');

const TEST_EMAIL = process.env.TEST_USER_EMAIL || 'mark.fisher@ku.edu';
const TEST_PASSWORD = process.env.TEST_USER_PASSWORD || 'tomcat123';
let cachedCookies = null;

/**
 * Log in using the portal login form.
 * Note: x-input renders labels without a `for` attribute, so we use #id selectors.
 */
async function login(page) {
    if (cachedCookies) {
        await page.context().addCookies(cachedCookies);
        await page.goto('/');
        await page.waitForLoadState('networkidle');
        return;
    }

    await page.goto('/login');
    await page.locator('#email').fill(TEST_EMAIL);
    await page.locator('#password').fill(TEST_PASSWORD);
    await Promise.all([
        page.waitForURL((url) => !url.pathname.includes('login'), { timeout: 20_000 }),
        page.locator('form').first().evaluate((form) => form.submit()),
    ]);
    await page.waitForLoadState('networkidle');
    cachedCookies = await page.context().cookies();
}

test.describe('x-taxa-search autocomplete', () => {
    test('taxon/create: parent taxon search sends taxa= query param', async ({ page }) => {
        await login(page);
        await page.goto('/taxon/create');

        // Intercept requests to the taxa search API
        const apiRequests = [];
        page.on('request', (request) => {
            if (request.url().includes('/api/taxa/search')) {
                apiRequests.push(new URL(request.url()));
            }
        });

        // The parent taxon field has id="parentname"
        const parentField = page.locator('#parentname');
        await parentField.click();
        await parentField.fill('Drosera');

        // Wait for the HTMX debounce (700 ms) + a bit of buffer
        await page.waitForTimeout(1200);

        // Verify a request was made
        expect(apiRequests.length).toBeGreaterThan(0);

        const lastRequest = apiRequests[apiRequests.length - 1];

        // The request must contain taxa=Drosera so the API can filter results.
        // (The input's own name param may also be present; the API ignores it.)
        expect(lastRequest.searchParams.get('taxa')).toBe('Drosera');
    });

    test('taxon/create: parent taxon autocomplete shows results when user types', async ({ page }) => {
        await login(page);
        await page.goto('/taxon/create');

        const parentField = page.locator('#parentname');
        // Click first to trigger Alpine's open=true via the focus/click handler
        await parentField.click();
        await parentField.fill('Drosera');

        // Wait for an API response so we know the request completed
        await page.waitForResponse((resp) => resp.url().includes('/api/taxa/search'), { timeout: 5000 });

        // Dropdown container is sometimes hidden by Alpine, so assert on the rendered results count.
        const dropdown = page.locator('#search-results-parentname');
        const resultCount = await dropdown.locator('> *').count();
        expect(resultCount).toBeGreaterThan(0);
    });

    test('taxon create page: parent taxon field is present', async ({ page }) => {
        await login(page);
        await page.goto('/taxon/create');

        await expect(page.locator('#parentname')).toBeVisible();
    });

    test('taxon/create: quick parser populates parent taxon input', async ({ page }) => {
        await login(page);
        await page.goto('/taxon/create');

        const quickParser = page.locator('#quickparser');
        await quickParser.fill('Drosera rotundifolia');
        await quickParser.press('Enter');

        await expect(page.locator('#parentname')).toHaveValue('Drosera');
    });

    test('taxon/create: quick parser one-word name updates rank and hides unit2', async ({ page }) => {
        await login(page);
        await page.goto('/taxon/create');

        const quickParser = page.locator('#quickparser');
        await quickParser.fill('Borp');
        await quickParser.press('Enter');

        await expect(page.locator('#rankid')).toHaveValue('180');
        await expect(page.locator('#unit2')).toBeHidden();
    });

    test('taxon/create: quick parser family name updates rank and hides unit2', async ({ page }) => {
        await login(page);
        await page.goto('/taxon/create');

        const quickParser = page.locator('#quickparser');
        await quickParser.fill('Asteraceae');
        await quickParser.press('Enter');

        await expect(page.locator('#rankid')).toHaveValue('140');
        await expect(page.locator('#unit2')).toBeHidden();
    });

    test('taxon tree viewer: submitting through UI returns one or more matches', async ({ page }) => {
        await page.goto('/taxon');

        const searchField = page.locator('input[name="taxa"]');
        const displayButton = page.getByRole('button', { name: 'Display Taxon Tree' });
        await expect(searchField).toBeVisible();
        await expect(displayButton).toBeVisible();

        const searchInputId = await searchField.getAttribute('id');
        expect(searchInputId).toBeTruthy();

        const parentTidField = page.locator(`#tid-${searchInputId}`);

        // Use only the UI and try common taxa terms to reduce fixture brittleness.
        const searchTerms = ['Acer', 'Drosera'];
        let foundRenderedTree = false;

        for (const searchTerm of searchTerms) {
            await searchField.fill(searchTerm);

            const results = page.locator(`#search-results-${searchInputId} > div`);

            // If no menu item appears for this term, try the next fallback term.
            try {
                await expect(results.first()).toBeVisible({ timeout: 5_000 });
            } catch {
                continue;
            }

            // Select the top autocomplete option through the UI so hidden parenttid is populated.
            await searchField.press('Enter');
            await expect(parentTidField).toHaveValue(/^[0-9]+$/, { timeout: 5_000 });
            const parentTid = await parentTidField.inputValue();

            await Promise.all([
                page.waitForURL(
                    (url) => url.pathname === '/taxon' && url.searchParams.get('parenttid') === parentTid,
                    { timeout: 10_000 },
                ),
                displayButton.click(),
            ]);

            const renderedNodes = page.locator('#taxon-tree li');
            try {
                await expect(renderedNodes.first()).toBeVisible({ timeout: 5_000 });
                foundRenderedTree = true;
                break;
            } catch {
                // Keep trying fallback terms if the selected taxon does not render tree nodes.
            }
        }

        expect(foundRenderedTree).toBeTruthy();
    });


    // @TODO this is currently brittle and depends on the genus existing and having a particular tid (12). Improve and make less brittle
    // test('taxon/edit hierarchy: typing Drosera sets taxa value and parent tid', async ({ page }) => {
    //     await login(page);
    //     await page.goto('/taxon/1/edit');

    //     // Hierarchy tab is index 2.
    //     await page.locator('#tab-2').click();

    //     const hierarchyForm = page.locator('form[name="taxstatusform"]');
    //     await expect(hierarchyForm).toBeVisible({ timeout: 3000 });

    //     const parentField = hierarchyForm.locator('input#upper-parentname');
    //     await parentField.click();
    //     await parentField.fill('Drosera');

    //     await page.waitForResponse(
    //         (resp) => resp.url().includes('/api/taxa/search') && resp.request().url().includes('taxa=Drosera'),
    //         { timeout: 5000 },
    //     );

    //     await expect(parentField).toHaveValue('Drosera');

    //     const parentTidField = hierarchyForm.locator('input#tid-upper-parentname');
    //     await expect(parentTidField).toHaveValue(/^[0-9]+$/);
    //     await expect(parentTidField).toHaveValue('12');
    // });
});
