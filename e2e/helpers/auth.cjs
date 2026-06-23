// @ts-check
const { expect } = require('@playwright/test');

/** @param {string} name */
function requireEnv(name) {
    const value = process.env[name];
    if (!value) {
        throw new Error(`Missing required environment variable: ${name}`);
    }
    return value;
}

const TEST_EMAIL = requireEnv('TEST_USER_EMAIL');
const TEST_PASSWORD = requireEnv('TEST_USER_PASSWORD');
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
        await page.locator('form').first().evaluate((form) => /** @type {HTMLFormElement} */ (form).submit());

        await expect
            .poll(() => new URL(page.url()).pathname, { timeout: 20_000 })
            .not.toContain('login');
    }

    await page.waitForLoadState('domcontentloaded');
    cachedCookies = await page.context().cookies();
}

module.exports = { login };
