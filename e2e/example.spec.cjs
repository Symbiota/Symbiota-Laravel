// @ts-check
const { test, expect } = require('@playwright/test');

test('Has Login', async ({ page }) => {
  await page.goto('./');

  // Click Sign In and wait for the login route to finish loading.
  await Promise.all([
    page.waitForURL(/\/login$/, { waitUntil: 'domcontentloaded' }),
    page.getByRole('link', { name: 'Sign In' }).click(),
  ]);

  // Expects the login form to be visible on the login page.
  await expect(page.getByRole('group', { name: 'Portal Login' })).toBeVisible();
});
