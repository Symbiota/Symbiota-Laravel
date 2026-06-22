// @ts-check
const { expect } = require('@playwright/test');

const TEST_EMAIL = process.env.TEST_USER_EMAIL || 'mark.fisher@ku.edu';
const TEST_PASSWORD = process.env.TEST_USER_PASSWORD || 'tomcat123';
/** @type {Array<any> | null} */
let cachedCookies = null;

/** @param {import('@playwright/test').Page} page */
async function login(page) {
    if (cachedCookies) {
        await page.context().addCookies(cachedCookies);
        await page.goto('/', { waitUntil: 'domcontentloaded' });
        if (!page.url().includes('/login')) {
            return;
        }
        cachedCookies = null;
    }

    await page.goto('/login', { waitUntil: 'domcontentloaded' });

    if (page.url().includes('/login')) {
        await page.locator('#email').fill(TEST_EMAIL);
        await page.locator('#password').fill(TEST_PASSWORD);
        await page.locator('form').first().evaluate((form) => form.submit());

        await expect
            .poll(() => new URL(page.url()).pathname, { timeout: 20_000 })
            .not.toContain('login');
    }

    await page.waitForLoadState('domcontentloaded');
    cachedCookies = await page.context().cookies();
}

module.exports = { login };
