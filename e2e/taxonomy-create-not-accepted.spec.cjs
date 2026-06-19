// @ts-check
const { test, expect } = require('@playwright/test');

const TEST_EMAIL = process.env.TEST_USER_EMAIL || 'mark.fisher@ku.edu';
const TEST_PASSWORD = process.env.TEST_USER_PASSWORD || 'tomcat123';
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

test.describe('taxonomy create not accepted flow', () => {
    test.setTimeout(180_000);

    test('enables submit when parent text resolves and not-accepted fields are filled', async ({ page }) => {
        await login(page);

        const suffix = Date.now();
        const randomToken = Math.random().toString(36).slice(2, 8);
        const speciesEpithet = `copilotna${suffix}-${randomToken}`;

        await page.goto('/taxon/create');
        await expect(page.locator('#taxon-form')).toBeVisible({ timeout: 15_000 });

        const quickParserValue = `Acer ${speciesEpithet}`;
        await page.locator('#quickparser').fill(quickParserValue);
        await page.getByRole('button', { name: /Parse/i }).click();

        await expect(page.locator('#unitname1')).toHaveValue('Acer', { timeout: 10_000 });
        await expect(page.locator('#unitname2')).toHaveValue(speciesEpithet, { timeout: 10_000 });

        await page.locator('#parentname').fill('Acer');
        await page.waitForResponse((resp) => resp.url().includes('/api/taxa/search'), { timeout: 5000 });

        await page.locator('input[name="acceptstatus"][value="0"]').check();
        await page.locator('#acceptedstr').evaluate((el) => {
            el.value = 'Acer';
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
        await expect(submitButton).toBeEnabled({ timeout: 10_000 });
        await expect(page.locator('#validationMessage')).toHaveText('');
    });
});
