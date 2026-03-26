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

test("debug parse button", async ({ page }) => {
    // Capture all browser console messages and errors
    const consoleMsgs = [];
    page.on("console", (msg) =>
        consoleMsgs.push("[" + msg.type() + "] " + msg.text()),
    );
    page.on("pageerror", (err) =>
        consoleMsgs.push("[pageerror] " + err.message),
    );

    await page.goto("/taxon/create");
    await page.waitForFunction(
        () =>
            document.getElementById("validationMessage")?.textContent?.length >
            0,
    );

    // Test 1: Regular Playwright .click()
    consoleMsgs.length = 0;
    await page.locator("#quickparser").fill("Acer rubrum");
    await page.getByRole("button", { name: "Parse" }).click();
    await page.waitForTimeout(2000);
    const r1 = await page.evaluate(() => ({
        quickparser: document.querySelector('[name="quickparser"]')?.value,
        unitname1: document.querySelector('[name="unitname1"]')?.value,
        validationMessage:
            document.getElementById("validationMessage")?.textContent,
    }));
    console.log("REGULAR CLICK console:", JSON.stringify(consoleMsgs.slice()));
    console.log("REGULAR CLICK state:", JSON.stringify(r1));

    // Test 2: Playwright dispatchEvent
    consoleMsgs.length = 0;
    await page.locator("#quickparser").fill("Acer rubrum");
    await page.getByRole("button", { name: "Parse" }).dispatchEvent("click");
    await page.waitForTimeout(2000);
    const r2 = await page.evaluate(() => ({
        quickparser: document.querySelector('[name="quickparser"]')?.value,
        unitname1: document.querySelector('[name="unitname1"]')?.value,
        validationMessage:
            document.getElementById("validationMessage")?.textContent,
    }));
    console.log("DISPATCH EVENT console:", JSON.stringify(consoleMsgs.slice()));
    console.log("DISPATCH EVENT state:", JSON.stringify(r2));
});
