// @ts-check
const { test, expect } = require('@playwright/test');

const TEST_EMAIL = process.env.TEST_USER_EMAIL || 'mark.fisher@ku.edu';
const TEST_PASSWORD = process.env.TEST_USER_PASSWORD || 'tomcat123';
let cachedCookies = null;

/** Taxon used for the edit page — must exist in the database. */
const TEST_TID = 1; // "Organism"

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

/** Navigate to the taxon edit page and activate the "Taxonomic Status" tab. */
async function goToSynonymEditTab(page) {
    await page.goto(`/taxon/${TEST_TID}/edit`);
    await page.waitForLoadState('networkidle');

    // The tabs are radio inputs labelled "Editor", "Taxonomic Status", "Hierarchy", "Child Taxa", "Delete".
    // "Taxonomic Status" is index 1 (tab-1).
    await page.locator('#tab-1').click();

    // Wait for the tab content to become visible
    const synonymContainer = page.locator('#taxonomy-synonym-container');
    await expect(synonymContainer).toBeVisible({ timeout: 3000 });
}

test.describe('x-taxa-search in taxonomy-synonym-edit (Taxonomic Status tab)', () => {

    test('acceptedstr search sends taxa= query param matching user input', async ({ page }) => {
        await login(page);
        await goToSynonymEditTab(page);

        const apiRequests = [];
        page.on('request', (request) => {
            if (request.url().includes('/api/taxa/search')) {
                apiRequests.push(new URL(request.url()));
            }
        });

        const acceptedField = page.locator('#synonym-acceptedstr');
        await acceptedField.click();
        await acceptedField.fill('Plantae');

        // Wait for HTMX debounce (700 ms) + buffer
        await page.waitForTimeout(1200);

        expect(apiRequests.length).toBeGreaterThan(0);

        const lastRequest = apiRequests[apiRequests.length - 1];
        expect(lastRequest.searchParams.get('taxa')).toBe('Plantae');
    });

    test('acceptedstr autocomplete shows results when user types', async ({ page }) => {
        await login(page);
        await goToSynonymEditTab(page);

        const acceptedField = page.locator('#synonym-acceptedstr');
        await acceptedField.click();
        await acceptedField.fill('Plantae');

        // Wait for an API response
        await page.waitForResponse((resp) => resp.url().includes('/api/taxa/search'), { timeout: 5000 });

        // Dropdown should be visible with results
        const dropdown = page.locator('#search-results-synonym-acceptedstr');
        await expect(dropdown).toBeVisible({ timeout: 3000 });
    });

    test('acceptedstr autocomplete results are filtered by input (not showing all taxa)', async ({ page }) => {
        await login(page);
        await goToSynonymEditTab(page);

        const acceptedField = page.locator('#synonym-acceptedstr');
        await acceptedField.click();
        await acceptedField.fill('Plantae');

        await page.waitForResponse((resp) => resp.url().includes('/api/taxa/search'), { timeout: 5000 });

        const dropdown = page.locator('#search-results-synonym-acceptedstr');
        await expect(dropdown).toBeVisible({ timeout: 3000 });

        // Every visible result should contain the search term (case-insensitive).
        // If the results are unfiltered, this assertion will fail because unrelated taxa will appear.
        const resultItems = dropdown.locator('> *');
        const count = await resultItems.count();
        expect(count).toBeGreaterThan(0);

        for (let i = 0; i < count; i++) {
            const text = await resultItems.nth(i).textContent();
            expect(text?.toLowerCase()).toContain('plantae');
        }
    });

    test('acceptedstr field is present in the Taxonomic Status tab', async ({ page }) => {
        await login(page);
        await goToSynonymEditTab(page);

        await expect(page.locator('#synonym-acceptedstr')).toBeVisible();
    });
});
