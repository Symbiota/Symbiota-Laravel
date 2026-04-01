// @ts-check
const { test, expect } = require('@playwright/test');

test('Has Login', async ({ page }) => {
  await page.goto('./', {timeout: 100000});

  //Click Sign In button
  await page.getByTestId('login-btn').click({timeout: 100000});

  // Expects page to have a heading with the name of Installation.
  // await expect(page.getByTestId('login-form-fieldset')).toBeVisible({timeout: 20000});
  await expect(page.getByRole('group', { name: 'Portal Login' })).toBeVisible({timeout: 100000});
});
