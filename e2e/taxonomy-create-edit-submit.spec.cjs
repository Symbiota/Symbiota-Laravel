// @ts-check
const { test, expect } = require('@playwright/test');
const { login } = require('./helpers/auth.cjs');

async function createTaxon(page) {
    const suffix = Date.now();
    const randomToken = Math.random().toString(36).slice(2, 8);
    const speciesEpithet = `testing${suffix}-${randomToken}`;

    await page.goto('/taxon/create', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#taxon-form')).toBeVisible({ timeout: 15_000 });

    const quickParserValue = `Drosera ${speciesEpithet}`;
    await page.locator('#quickparser').fill(quickParserValue);
    await page.getByRole('button', { name: /Parse/i }).click();

    await expect(page.locator('#unitname1')).toHaveValue('Drosera', { timeout: 10_000 });
    await expect(page.locator('#unitname2')).toHaveValue(speciesEpithet, { timeout: 10_000 });
    await page.locator('#parentname').fill('Organism');
    await page.locator('#tid-parentname').evaluate((el) => {
        el.value = '1';
        el.dispatchEvent(new Event('input', { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    });

    await page.locator('#parentname').blur();
    await expect(page.locator('#tid-parentname')).toHaveValue(/\d+/);
    await expect(page.locator('#submitButton')).toBeEnabled({ timeout: 10_000 });

    await Promise.all([
        page.waitForURL(/\/taxon\/\d+$/, { timeout: 30_000, waitUntil: 'domcontentloaded' }),
        page.locator('#taxon-form').evaluate((form) => form.submit()),
    ]);

    const match = page.url().match(/\/taxon\/(\d+)$/);
    expect(match).not.toBeNull();

    return Number(match[1]);
}

test.describe('taxonomy create then edit submit flow', () => {
    test.setTimeout(180_000);

    test('creates a taxon, edits notes, and submits edits successfully', async ({ page }) => {
        await login(page);

        const createdTid = await createTaxon(page);

        await page.getByRole('link', { name: /Edit Taxon$/ }).click();
        await page.waitForURL(new RegExp(`/taxon/${createdTid}/edit$`), { timeout: 10_000 });

        const notesField = page.locator('#taxon-form #notes');
        const submitEditsButton = page.locator('#submitButton');
        const newNotes = `updated notes ${Date.now()}`;
        await notesField.evaluate((el, value) => {
            el.value = value;
        }, newNotes);
        await expect(submitEditsButton).toBeEnabled({ timeout: 5000 });

        const updateResponsePromise = page.waitForResponse(
            (response) => response.request().method() === 'POST' && response.url().includes('/taxon/update'),
            { timeout: 15_000 },
        );

        await submitEditsButton.click();

        const updateResponse = await updateResponsePromise;
        expect(updateResponse.status()).toBeLessThan(400);

        await page.waitForURL(new RegExp(`/taxon/${createdTid}$`), { timeout: 15_000 });
    });
});

test.describe.serial('taxonomy edit without changing unit names', () => {
    test.setTimeout(180_000);

    /** @type {number | null} */
    let createdTid = null;

    test('creates a taxon to reuse in a later test', async ({ page }) => {
        await login(page);
        createdTid = await createTaxon(page);
        expect(createdTid).not.toBeNull();
    });

    test('editing author keeps submit enabled and avoids duplicate-name validation', async ({ page }) => {
        await login(page);
        expect(createdTid).not.toBeNull();

        await page.goto(`/taxon/${createdTid}/edit`);
        const taxonForm = page.locator('#taxon-form');
        await expect(taxonForm).toBeVisible({ timeout: 15_000 });

        const unitname1 = page.locator('#unitname1');
        const unitname2 = page.locator('#unitname2');
        const originalUnitname1 = await unitname1.inputValue();
        const originalUnitname2 = await unitname2.inputValue();

        const authorField = page.locator('#author');
        const submitButton = taxonForm.locator('#submitButton');
        const validationMessage = taxonForm.locator('#validationMessage');
        const newAuthorValue = `Test Author ${Date.now()}`;

        await authorField.fill(newAuthorValue);
        await authorField.blur();

        await expect(unitname1).toHaveValue(originalUnitname1);
        await expect(unitname2).toHaveValue(originalUnitname2);
        await expect(submitButton).toBeEnabled({ timeout: 10_000 });
        await expect(validationMessage).not.toContainText(/already exists in the database/i);
    });
});
