    // @ts-check
const { test, expect } = require('@playwright/test');

const TEST_EMAIL = process.env.TEST_USER_EMAIL || 'mark.fisher@ku.edu';
const TEST_PASSWORD = process.env.TEST_USER_PASSWORD || 'tomcat123';

const SUBMIT_ENABLED_TEXT = 'Submit Upper Taxonomy Edits';
const SUBMIT_DISABLED_TEXT = 'Submission Disabled';
/** @type {Array<any> | null} */
let cachedCookies = null;

/** @param {import('@playwright/test').Page} page */
async function login(page) {
    if (cachedCookies) {
        await page.context().addCookies(cachedCookies);
        await page.goto('/');
        await page.waitForLoadState('domcontentloaded');
        return;
    }

    await page.goto('/login');

    // If session is already authenticated and /login redirects, avoid login form waits.
    if (page.url().includes('/login')) {
        await page.locator('#email').fill(TEST_EMAIL);
        await page.locator('#password').fill(TEST_PASSWORD);
        await Promise.all([
            page.waitForURL((url) => !url.pathname.includes('login'), {
                timeout: 20_000,
                waitUntil: 'domcontentloaded',
            }),
            page.locator('form').first().evaluate((form) => {
                const htmlForm = /** @type {HTMLFormElement} */ (form);
                htmlForm.submit();
            }),
        ]);
    }

    await expect(page.locator('text=Welcome Mark!')).toBeVisible({ timeout: 20_000 });
    cachedCookies = await page.context().cookies();
}

/** @param {string} prefix */
function makeUniqueTaxonName(prefix) {
    const suffix = Date.now();
    const randomToken = Math.random().toString(36).slice(2, 8);
    return {
        genus: `${prefix}Gen${suffix}${randomToken}`,
        species: `${prefix.toLowerCase()}sp${suffix}${randomToken}`,
    };
}

/**
 * @typedef {{ genus: string; species: string; parentName: string; parentTid: number }} CreateTaxonOptions
 * @typedef {{ tid: number; name: string }} CreatedTaxon
 */

/**
 * @param {import('@playwright/test').Page} page
 * @param {CreateTaxonOptions} options
 * @returns {Promise<CreatedTaxon>}
 */
async function createTaxon(page, { genus, species, parentName, parentTid }) {
    await page.goto('/taxon/create');
    await expect(page.locator('#taxon-form')).toBeVisible({ timeout: 15_000 });

    await page.locator('#quickparser').fill(`${genus} ${species}`);
    await page.getByRole('button', { name: /Parse/i }).click();

    await expect(page.locator('#unitname1')).toHaveValue(genus, { timeout: 10_000 });
    await expect(page.locator('#unitname2')).toHaveValue(species, { timeout: 10_000 });

    const parentField = page.locator('#parentname');
    await parentField.click();
    await parentField.fill(parentName);

    const requestedParent = page.locator(`#search-results-parentname > [id="${parentTid}"]`);
    await expect(requestedParent).toBeVisible({ timeout: 15_000 });
    await requestedParent.click();

    await parentField.blur();
    await expect(page.locator('#tid-parentname')).toHaveValue(String(parentTid));
    await expect(page.locator('#submitButton')).toBeEnabled({ timeout: 10_000 });

    await Promise.all([
        page.waitForURL(/\/taxon\/\d+$/, { timeout: 30_000, waitUntil: 'domcontentloaded' }),
        page.locator('#submitButton').click(),
    ]);

    const match = page.url().match(/\/taxon\/(\d+)$/);
    expect(match).not.toBeNull();
    if (!match) {
        throw new Error('Could not parse created taxon tid from URL');
    }

    return {
        tid: Number(match[1]),
        name: `${genus} ${species}`,
    };
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

test.describe.serial('taxonomy upper edit submit button labels', () => {
    test.setTimeout(180_000);

    /** @type {CreatedTaxon | null} */
    let parentTaxon = null;
    /** @type {CreatedTaxon | null} */
    let childTaxon = null;

    test.beforeAll(async ({ browser }) => {
        test.setTimeout(180_000);

        const context = await browser.newContext();
        const page = await context.newPage();

        await login(page);

        const parentNameParts = makeUniqueTaxonName('UpperEditParent');
        parentTaxon = await createTaxon(page, {
            genus: parentNameParts.genus,
            species: parentNameParts.species,
            parentName: 'Organism',
            parentTid: 1,
        });

        const childNameParts = makeUniqueTaxonName('UpperEditChild');
        childTaxon = await createTaxon(page, {
            genus: childNameParts.genus,
            species: childNameParts.species,
            parentName: parentTaxon.name,
            parentTid: parentTaxon.tid,
        });

        await context.close();
    });

    test.beforeEach(async ({ page }) => {
        await login(page);
        expect(childTaxon).not.toBeNull();
        if (!childTaxon) {
            throw new Error('Child taxon setup failed in beforeAll');
        }

        await page.goto(`/taxon/${childTaxon.tid}/edit`);
        await page.waitForLoadState('networkidle');
        await page.locator('#tab-0').click();

        const visibleHierarchyForm = page.locator('form[name="taxstatusform"]:visible').first();
        await expect(visibleHierarchyForm).toBeVisible({ timeout: 15_000 });
        await expect(visibleHierarchyForm.locator('#new-parent-taxon')).toBeVisible({ timeout: 15_000 });
    });

    test('shows enabled submit label when a valid parent taxon is selected', async ({ page }) => {
        expect(parentTaxon).not.toBeNull();
        if (!parentTaxon) {
            throw new Error('Parent taxon setup failed in beforeAll');
        }

        const hierarchyForm = page.locator('form[name="taxstatusform"]:visible').first();
        const parentField = hierarchyForm.locator('#new-parent-taxon');
        const submitButton = hierarchyForm.locator('#taxstatuseditsubmit');

        await parentField.click();
        await parentField.fill('');
        await parentField.fill(parentTaxon.name);
        const matchingParentOption = page.locator(`#search-results-new-parent-taxon > [id="${parentTaxon.tid}"]`);
        await expect(matchingParentOption).toBeVisible({ timeout: 15_000 });
        await matchingParentOption.click();

        await expect(hierarchyForm.locator('#tid-new-parent-taxon')).toHaveValue(String(parentTaxon.tid));
        await expect(submitButton).toBeEnabled();
        await expect(submitButton).toContainText(SUBMIT_ENABLED_TEXT);
    });

    test('shows disabled submit label when parent taxon does not resolve to a tid', async ({ page }) => {
        const hierarchyForm = page.locator('form[name="taxstatusform"]:visible').first();
        const parentField = hierarchyForm.locator('#new-parent-taxon');
        const submitButton = hierarchyForm.locator('#taxstatuseditsubmit');

        await parentField.evaluate((el) => {
            const input = /** @type {HTMLInputElement} */ (el);
            input.value = 'this taxon does not exist';
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
        await parentField.blur();

        await expect(hierarchyForm.locator('#tid-new-parent-taxon')).toHaveValue('');
        await expect(submitButton).toBeDisabled();
        await expect(submitButton).toContainText(SUBMIT_DISABLED_TEXT);
    });
});
