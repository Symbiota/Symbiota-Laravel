// @ts-check
const { test, expect } = require('@playwright/test');
const { login } = require('./helpers/auth.cjs');

/** Taxon used for the edit page — must exist in the database. */
const TEST_TID = 1; // "Organism"

/** Navigate to the taxon edit page and activate the "Taxonomic Status" tab. */
async function goToSynonymEditTab(page) {
    await page.goto(`/taxon/${TEST_TID}/edit`, { waitUntil: 'domcontentloaded' });

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

        const acceptedField = page.locator('#synonym-acceptedstr');
        const requestPromise = page.waitForRequest(
            (request) => request.url().includes('/api/taxa/search') && request.url().includes('taxa=Plantae'),
            { timeout: 10_000 },
        );

        await acceptedField.click();
        await acceptedField.fill('Plantae');
        await acceptedField.press('ArrowDown');

        const request = await requestPromise;
        const requestUrl = new URL(request.url());
        expect(requestUrl.searchParams.get('taxa')).toBe('Plantae');
    });

    test('acceptedstr autocomplete shows results when user types', async ({ page }) => {
        await login(page);
        await goToSynonymEditTab(page);

        const acceptedField = page.locator('#synonym-acceptedstr');
        await acceptedField.click();
        await acceptedField.fill('Plantae');

        // Dropdown should be visible with results
        const dropdown = page.locator('#search-results-synonym-acceptedstr');
        await expect(dropdown.locator('> *').first()).toBeVisible({ timeout: 10_000 });
    });

    test('acceptedstr autocomplete results are filtered by input (not showing all taxa)', async ({ page }) => {
        await login(page);
        await goToSynonymEditTab(page);

        const acceptedField = page.locator('#synonym-acceptedstr');
        await acceptedField.click();
        await acceptedField.fill('Plantae');

        const dropdown = page.locator('#search-results-synonym-acceptedstr');
        await expect(dropdown.locator('> *').first()).toBeVisible({ timeout: 10_000 });

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
