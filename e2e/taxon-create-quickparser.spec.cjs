// @ts-check
const { test, expect } = require("@playwright/test");

if (!process.env.CI && process.loadEnvFile) process.loadEnvFile();

const TEST_EMAIL = process.env.TEST_EMAIL ?? "";
const TEST_PASSWORD = process.env.TEST_PASSWORD ?? "";

test.beforeEach(async ({ page }) => {
    await page.goto("/login");
    await page.locator("#email").fill(TEST_EMAIL);
    await page.locator("#password").fill(TEST_PASSWORD);
    await page.getByRole("button", { name: "Sign In", exact: true }).click();
    await page.waitForURL("**/");
});

test.setTimeout(60000);

test('quickparser sets valid parent taxon for "Acer rubrum"', async ({
    page,
}) => {
    await page.goto("/taxon/create");
    // Wait for Alpine to fully initialize: x-init calls validate(), which
    // reactively populates #validationMessage. Once that has text, all @click
    // bindings have also been attached.
    await page.waitForFunction(
        () =>
            document.getElementById("validationMessage")?.textContent?.length >
            0,
    );

    await page.locator("#quickparser").fill("Acer rubrum");
    await page.getByRole("button", { name: "Parse" }).click();

    const validationMessage = page.locator("#validationMessage");
    await expect(validationMessage).toHaveText(
        "Acer rubrum already exists in the database.",
        { timeout: 10000 },
    );
});
