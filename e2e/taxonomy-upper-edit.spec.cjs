    // @ts-check
const { test, expect } = require('@playwright/test');

const TEST_EMAIL = process.env.TEST_USER_EMAIL || 'mark.fisher@ku.edu';
const TEST_PASSWORD = process.env.TEST_USER_PASSWORD || 'tomcat123';

const TEST_TID = 19;
const EXPECTED_PARENT_TID = '12';
const SUBMIT_ENABLED_TEXT = 'Submit Upper Taxonomy Edits';
const SUBMIT_DISABLED_TEXT = 'Submission Disabled';
let cachedCookies = null;

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

// test.describe('taxonomy upper edit parent submission', () => { // @TODO make this generalizable or mock or stub out dummy data
//     test('taxon/19 hierarchy submits selected newparenttid exactly once', async ({ page }) => {
//         await login(page);
//         await page.goto(`/taxon/${TEST_TID}/edit`);
//         await page.waitForLoadState('networkidle');

//         // Open the Hierarchy tab.
//         await page.locator('#tab-2').click();

//         const hierarchyForm = page.locator('form[name="taxstatusform"]');
//         await expect(hierarchyForm).toBeVisible({ timeout: 5000 });

//         const parentField = hierarchyForm.locator('#new-parent-taxon');
//         await parentField.click();
//         await parentField.fill('Drosera');

//         await page.waitForResponse((resp) => resp.url().includes('/api/taxa/search'), { timeout: 5000 });

//         // Pick the Drosera result with known tid 12.
//         await page.locator('#search-results-new-parent-taxon > [id="12"]').click();

//         await expect(parentField).toHaveValue('Drosera');
//         await expect(hierarchyForm.locator('#tid-new-parent-taxon')).toHaveValue(EXPECTED_PARENT_TID);

//         const updateRequestPromise = page.waitForRequest(
//             (request) => request.method() === 'POST' && request.url().includes('/updateUpperTaxonomy'),
//             { timeout: 5000 },
//         );

//         await hierarchyForm.locator('button[name="taxstatuseditsubmit"]').click();

//         const updateRequest = await updateRequestPromise;
//         const body = updateRequest.postData() || '';
//         const params = new URLSearchParams(body);

//         // Regression assertion: only one value should be submitted for newparenttid.
//         expect(params.getAll('newparenttid')).toEqual([EXPECTED_PARENT_TID]);
//     });
// });

test.describe('taxonomy upper edit submit button labels', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
        await page.goto(`/taxon/${TEST_TID}/edit`);
        await page.waitForLoadState('networkidle');
        await page.locator('#tab-2').click();
        await expect(page.locator('form[name="taxstatusform"]')).toBeAttached({ timeout: 5000 });
    });

    test('shows enabled submit label when a valid parent taxon is selected', async ({ page }) => {
        const hierarchyForm = page.locator('form[name="taxstatusform"]');
        const parentField = hierarchyForm.locator('#new-parent-taxon');
        const submitButton = hierarchyForm.locator('#taxstatuseditsubmit');

        await parentField.evaluate((el) => {
            el.value = 'Drosera';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
        await hierarchyForm.locator('#tid-new-parent-taxon').evaluate((el) => {
            el.value = '12';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });

        await expect(hierarchyForm.locator('#tid-new-parent-taxon')).toHaveValue(EXPECTED_PARENT_TID);
        await expect(submitButton).toBeEnabled();
        await expect(submitButton).toContainText(SUBMIT_ENABLED_TEXT);
    });

    test('shows disabled submit label when parent taxon does not resolve to a tid', async ({ page }) => {
        const hierarchyForm = page.locator('form[name="taxstatusform"]');
        const parentField = hierarchyForm.locator('#new-parent-taxon');
        const submitButton = hierarchyForm.locator('#taxstatuseditsubmit');

        await parentField.evaluate((el) => {
            el.value = 'this taxon does not exist';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
        await parentField.blur();

        await expect(hierarchyForm.locator('#tid-new-parent-taxon')).toHaveValue('');
        await expect(submitButton).toBeDisabled();
        await expect(submitButton).toContainText(SUBMIT_DISABLED_TEXT);
    });
});
