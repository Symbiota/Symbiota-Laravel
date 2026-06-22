// @ts-check
const { test, expect } = require('@playwright/test');
const { login } = require('./helpers/auth.cjs');

test.describe('taxonomy create not accepted flow', () => {
    test.setTimeout(180_000);

    test('enables submit when parent text resolves and not-accepted fields are filled', async ({ page }) => {
        await login(page);

        const suffix = Date.now();
        const randomToken = Math.random().toString(36).slice(2, 8);
        const speciesEpithet = `copilotna${suffix}-${randomToken}`;

        await page.goto('/taxon/create', { waitUntil: 'domcontentloaded' });
        await expect(page.locator('#taxon-form')).toBeVisible({ timeout: 15_000 });

        const quickParserValue = `Acer ${speciesEpithet}`;
        await page.locator('#quickparser').fill(quickParserValue);
        await page.getByRole('button', { name: /Parse/i }).click();

        await expect(page.locator('#unitname1')).toHaveValue('Acer', { timeout: 10_000 });
        await expect(page.locator('#unitname2')).toHaveValue(speciesEpithet, { timeout: 10_000 });

        await page.locator('#parentname').fill('Organism');
        await page.locator('#tid-parentname').evaluate((el) => {
            el.value = '1';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });

        await page.locator('input[name="acceptstatus"][value="0"]').check();
        await page.locator('#acceptedstr').evaluate((el) => {
            el.value = 'Organism';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
        await page.locator('#tid-acceptedstr').evaluate((el) => {
            el.value = '1';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
        await page.locator('#unacceptabilityreason').evaluate((el) => {
            el.value = 'homotypic synonym';
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });

        await page.locator('#parentname').blur();
        const submitButton = page.locator('#submitButton');
        await expect(page.locator('#tid-parentname')).not.toHaveValue('');
        await expect(page.locator('#tid-acceptedstr')).toHaveValue('1');
        await expect(submitButton).toBeVisible();
        await expect(page.locator('#validationMessage')).not.toContainText(/parent taxon required|accepted taxon needs value|missing required/i);
    });
});
