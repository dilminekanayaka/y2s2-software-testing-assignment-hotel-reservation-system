package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

/**
 * Test class for user login functionality
 * Tests the complete user login flow on the Flower Garden Hotel Booking website.
 */
public class UserLoginTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Locators for login page
    private static final By EMAIL_INPUT = By.id("email");
    private static final By PASSWORD_INPUT = By.id("password");
    private static final By LOGIN_BUTTON = By.xpath("//button[contains(text(), 'Sign In')] | //button[@type='submit']");
    private static final By ERROR_MESSAGE = By.xpath("//div[contains(@class, 'alert-error')] | //div[contains(@class, 'error')] | //div[contains(@class, 'alert')]");
    private static final By LOGIN_LINK = By.xpath("//a[@href='login.php'] | //a[contains(text(), 'login')]");
    private static final By USER_AVATAR = By.xpath("//div[contains(@class, 'user-avatar')] | //div[contains(@class, 'user-info')] | //span[contains(@class, 'user-name')]");
    private static final By LOGOUT_LINK = By.xpath("//a[contains(text(), 'Logout')] | //a[contains(text(), 'Sign Out')]");
    private static final By REMEMBER_ME_CHECKBOX = By.id("remember");
    private static final By PASSWORD_TOGGLE = By.xpath("//button[@class='password-toggle']");
    
    @Test(description = "Test successful user login with valid credentials")
    public void testSuccessfulUserLogin() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting successful user login test...");
            
            // Navigate to login page
            System.out.println("📍 Step 1: Navigating to login page...");
            helper.clickElement(LOGIN_LINK);
            helper.waitForTitleToContain("Login");
            helper.mediumDelay();
            
            // Verify login page loaded
            System.out.println("📍 Step 2: Verifying login page loaded...");
            if (!helper.isElementDisplayed(EMAIL_INPUT) || !helper.isElementDisplayed(PASSWORD_INPUT)) {
                Assert.fail("Login page did not load correctly");
            }
            helper.shortDelay();
            
            // First, create a test user if needed
            System.out.println("📍 Step 3: Ensuring test user exists...");
            String testEmail = generateUniqueEmail();
            String testPassword = "TestPassword123!";
            
            // Navigate to registration to create user
            helper.clickElement(By.xpath("//a[@href='register.php']"));
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Fill registration form
            helper.clearAndSendKeys(By.id("first_name"), "Test");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("last_name"), "User");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("email"), testEmail);
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("phone"), "+1234567890");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("date_of_birth"), "1990-01-01");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("address"), "123 Test Street");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("password"), testPassword);
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("confirm_password"), testPassword);
            helper.mediumDelay();
            
            // Submit registration
            System.out.println("   🔘 Creating test user...");
            boolean registered = helper.smartFormSubmit(By.xpath("//button[contains(text(), 'Create Account')] | //button[@type='submit']"));
            if (registered) {
                helper.longDelay(); // Wait for registration to complete
                System.out.println("   ✅ Test user created successfully");
            }
            
            // Navigate back to login page
            System.out.println("📍 Step 4: Navigating back to login page...");
            helper.clickElement(LOGIN_LINK);
            helper.waitForTitleToContain("Login");
            helper.mediumDelay();
            
            // Fill login form with valid credentials
            System.out.println("📍 Step 5: Filling login form...");
            System.out.println("   📧 Using email: " + testEmail);
            System.out.println("   🔒 Using password: " + testPassword);
            
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit login form
            System.out.println("📍 Step 6: Submitting login form...");
            boolean submitted = helper.smartFormSubmit(LOGIN_BUTTON);
            
            if (!submitted) {
                System.out.println("❌ Login form submission failed");
                Assert.fail("Could not submit login form");
            }
            
            // Wait for response
            System.out.println("📍 Step 7: Waiting for login response...");
            helper.longDelay();
            
            // Check for successful login
            System.out.println("📍 Step 8: Checking for successful login...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            boolean loginSuccessful = false;
            
            // Check for redirect to home page
            if (currentUrl.contains("home.php") || pageTitle.toLowerCase().contains("home")) {
                loginSuccessful = true;
                System.out.println("✅ Login successful - Redirected to home page");
            }
            
            // Check for user avatar or logged-in indicators
            if (!loginSuccessful && helper.isElementDisplayed(USER_AVATAR)) {
                loginSuccessful = true;
                System.out.println("✅ Login successful - User avatar displayed");
            }
            
            // Check for logout link (indicates logged in)
            if (!loginSuccessful && helper.isElementDisplayed(LOGOUT_LINK)) {
                loginSuccessful = true;
                System.out.println("✅ Login successful - Logout link displayed");
            }
            
            // Check if still on login page (indicates failure)
            if (!loginSuccessful && currentUrl.contains("login.php")) {
                // Check for error message
                if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                    String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                    System.out.println("❌ Login failed with error: " + errorText);
                    Assert.fail("Login failed with error: " + errorText);
                } else {
                    System.out.println("❌ Login failed - Still on login page without error message");
                    Assert.fail("Login failed - Still on login page without error message");
                }
            }
            
            Assert.assertTrue(loginSuccessful, "User should be successfully logged in");
            
            System.out.println("🎉 Successful login test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Login test failed: " + e.getMessage());
            helper.mediumDelay();
            Assert.fail("Login test failed: " + e.getMessage());
        }
    }
    
    /**
     * Generate unique email for testing
     */
    private String generateUniqueEmail() {
        return "testuser" + System.currentTimeMillis() + "@example.com";
    }
    
    @Test(description = "Test login with invalid email")
    public void testLoginWithInvalidEmail() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting invalid email login test...");
            
            // Navigate to login page
            System.out.println("📍 Step 1: Navigating to login page...");
            helper.clickElement(LOGIN_LINK);
            helper.waitForTitleToContain("Login");
            helper.mediumDelay();
            
            // Verify login page loaded
            if (!helper.isElementDisplayed(EMAIL_INPUT) || !helper.isElementDisplayed(PASSWORD_INPUT)) {
                Assert.fail("Login page did not load correctly");
            }
            helper.shortDelay();
            
            // Fill login form with invalid email
            System.out.println("📍 Step 2: Filling form with invalid email...");
            System.out.println("   📧 Using invalid email: invalid@example.com");
            
            helper.clearAndSendKeys(EMAIL_INPUT, "invalid@example.com");
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, "TestPassword123!");
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit login form
            System.out.println("📍 Step 3: Submitting login form...");
            boolean submitted = helper.smartFormSubmit(LOGIN_BUTTON);
            
            if (!submitted) {
                System.out.println("❌ Login form submission failed");
                Assert.fail("Could not submit login form");
            }
            
            // Wait for response
            System.out.println("📍 Step 4: Waiting for login response...");
            helper.longDelay();
            
            // Check for error message
            System.out.println("📍 Step 5: Checking for error message...");
            if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                System.out.println("✅ Invalid email test passed!");
                System.out.println("📝 Error message: " + errorText);
                helper.mediumDelay(); // Wait to see the error message
                
                Assert.assertTrue(errorText.toLowerCase().contains("invalid") || 
                                errorText.toLowerCase().contains("incorrect") ||
                                errorText.toLowerCase().contains("credentials"), 
                    "Error message should indicate invalid credentials");
            } else {
                // Check if still on login page (which indicates an error)
                String currentUrl = driver.getCurrentUrl();
                if (currentUrl.contains("login.php")) {
                    System.out.println("✅ Invalid email test passed - Still on login page");
                    System.out.println("   This indicates login was rejected");
                    helper.mediumDelay(); // Wait to see the current state
                } else {
                    System.out.println("❌ No error message found for invalid email");
                    System.out.println("Current URL: " + currentUrl);
                    helper.mediumDelay(); // Wait to see the current state
                    Assert.fail("No error message found for invalid email");
                }
            }
            
            System.out.println("🎉 Invalid email login test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Invalid email login test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Invalid email login test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test login with invalid password")
    public void testLoginWithInvalidPassword() {
        helper = new PageObjectHelper(driver);
        
        // Navigate to login page
        helper.clickElement(LOGIN_LINK);
        helper.waitForTitleToContain("Login");
        
        // Fill login form with invalid password
        String testEmail = getProperty("test.user.email", "testuser@example.com");
        helper.sendKeysToElement(EMAIL_INPUT, testEmail);
        helper.sendKeysToElement(PASSWORD_INPUT, "WrongPassword123!");
        
        // Submit login form
        helper.clickElement(LOGIN_BUTTON);
        
        // Verify error message
        Assert.assertTrue(helper.isElementDisplayed(ERROR_MESSAGE), 
            "Should show error message for invalid password");
        
        String errorText = helper.getTextFromElement(ERROR_MESSAGE);
        Assert.assertTrue(errorText.toLowerCase().contains("invalid") || 
                         errorText.toLowerCase().contains("incorrect") ||
                         errorText.toLowerCase().contains("credentials"), 
            "Error message should indicate invalid credentials");
        
        System.out.println("Invalid password login test completed");
    }
    
    @Test(description = "Test login with empty credentials")
    public void testLoginWithEmptyCredentials() {
        helper = new PageObjectHelper(driver);
        
        // Navigate to login page
        helper.clickElement(LOGIN_LINK);
        helper.waitForTitleToContain("Login");
        
        // Leave credentials empty
        helper.sendKeysToElement(EMAIL_INPUT, "");
        helper.sendKeysToElement(PASSWORD_INPUT, "");
        
        // Submit login form
        helper.clickElement(LOGIN_BUTTON);
        
        // Verify that form validation prevents submission
        // Check if we're still on login page or if validation messages appear
        boolean stillOnLoginPage = helper.waitForTitleToContain("Login");
        boolean hasValidationErrors = helper.isElementDisplayed(ERROR_MESSAGE);
        
        Assert.assertTrue(stillOnLoginPage || hasValidationErrors, 
            "Should prevent submission with empty credentials");
        
        System.out.println("Empty credentials login test completed");
    }
    
    
    @Test(description = "Test logout functionality")
    public void testLogoutFunctionality() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting logout functionality test...");
            
            // Step 1: Create and login a test user
            System.out.println("📍 Step 1: Creating and logging in test user...");
            String testEmail = generateUniqueEmail();
            String testPassword = "TestPassword123!";
            
            // Navigate to registration to create user
            helper.clickElement(By.xpath("//a[@href='register.php']"));
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Fill registration form
            helper.clearAndSendKeys(By.id("first_name"), "Test");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("last_name"), "User");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("email"), testEmail);
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("phone"), "+1234567890");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("date_of_birth"), "1990-01-01");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("address"), "123 Test Street");
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("password"), testPassword);
            helper.shortDelay();
            helper.clearAndSendKeys(By.id("confirm_password"), testPassword);
            helper.mediumDelay();
            
            // Submit registration
            System.out.println("   🔘 Creating test user...");
            boolean registered = helper.smartFormSubmit(By.xpath("//button[contains(text(), 'Create Account')] | //button[@type='submit']"));
            if (registered) {
                helper.longDelay(); // Wait for registration to complete
                System.out.println("   ✅ Test user created successfully");
            }
            
            // Navigate to login page
            System.out.println("📍 Step 2: Logging in test user...");
            helper.clickElement(LOGIN_LINK);
            helper.waitForTitleToContain("Login");
            helper.mediumDelay();
            
            // Fill login form
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
            helper.mediumDelay();
            
            // Submit login form
            boolean loginSubmitted = helper.smartFormSubmit(LOGIN_BUTTON);
            if (loginSubmitted) {
                helper.longDelay(); // Wait for login to complete
                System.out.println("   ✅ User logged in successfully");
            }
            
            // Step 2: Verify user is logged in
            System.out.println("📍 Step 3: Verifying user is logged in...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            boolean isLoggedIn = false;
            
            // Check for redirect to home page
            if (currentUrl.contains("home.php") || pageTitle.toLowerCase().contains("home")) {
                isLoggedIn = true;
                System.out.println("   ✅ User is logged in - on home page");
            }
            
            // Check for user avatar or logged-in indicators
            if (!isLoggedIn && helper.isElementDisplayed(USER_AVATAR)) {
                isLoggedIn = true;
                System.out.println("   ✅ User is logged in - user avatar displayed");
            }
            
            // Check for logout link (indicates logged in)
            if (!isLoggedIn && helper.isElementDisplayed(LOGOUT_LINK)) {
                isLoggedIn = true;
                System.out.println("   ✅ User is logged in - logout link displayed");
            }
            
            if (!isLoggedIn) {
                System.out.println("❌ User is not logged in - cannot test logout");
                Assert.fail("User is not logged in - cannot test logout");
            }
            
            // Step 3: Perform logout
            System.out.println("📍 Step 4: Performing logout...");
            
            // Try multiple logout methods
            boolean logoutSuccessful = false;
            
            // Method 1: Look for user dropdown and logout link
            By[] logoutLocators = {
                By.xpath("//a[@href='logout.php']"),
                By.xpath("//a[contains(@class, 'logout')]"),
                By.xpath("//a[contains(text(), 'Logout')]"),
                By.xpath("//a[contains(text(), 'Sign Out')]"),
                By.xpath("//a[contains(text(), 'Log out')]"),
                By.xpath("//button[contains(text(), 'Logout')]"),
                By.xpath("//button[contains(text(), 'Sign Out')]"),
                By.xpath("//a[@href*='logout']"),
                By.xpath("//a[@href*='signout']")
            };
            
            // First try to click on user dropdown if it exists
            By userDropdown = By.xpath("//div[contains(@class, 'user-dropdown')] | //div[contains(@class, 'dropdown')] | //div[contains(@class, 'user-info')]");
            if (helper.isElementDisplayed(userDropdown)) {
                System.out.println("   Found user dropdown, clicking to open...");
                try {
                    helper.clickElement(userDropdown);
                    helper.shortDelay(); // Wait for dropdown to open
                    System.out.println("   ✅ User dropdown opened");
                } catch (Exception e) {
                    System.out.println("   Failed to open user dropdown: " + e.getMessage());
                }
            }
            
            for (By logoutLocator : logoutLocators) {
                if (helper.isElementDisplayed(logoutLocator)) {
                    System.out.println("   Found logout link: " + logoutLocator);
                    try {
                        helper.clickElement(logoutLocator);
                        logoutSuccessful = true;
                        System.out.println("   ✅ Logout link clicked successfully");
                        break;
                    } catch (Exception e) {
                        System.out.println("   Failed to click logout link: " + e.getMessage());
                    }
                }
            }
            
            // Method 2: Try JavaScript logout if available
            if (!logoutSuccessful) {
                System.out.println("   Trying JavaScript logout...");
                try {
                    ((org.openqa.selenium.JavascriptExecutor) driver)
                        .executeScript("window.location.href = 'logout.php';");
                    logoutSuccessful = true;
                    System.out.println("   ✅ JavaScript logout executed");
                } catch (Exception e) {
                    System.out.println("   JavaScript logout failed: " + e.getMessage());
                }
            }
            
            // Method 3: Navigate to logout URL directly
            if (!logoutSuccessful) {
                System.out.println("   Trying direct logout URL...");
                try {
                    driver.navigate().to(getProperty("base.url", "http://localhost/SereneTripsLK") + "/logout.php");
                    logoutSuccessful = true;
                    System.out.println("   ✅ Navigated to logout URL");
                } catch (Exception e) {
                    System.out.println("   Direct logout URL failed: " + e.getMessage());
                }
            }
            
            if (!logoutSuccessful) {
                System.out.println("❌ All logout methods failed");
                Assert.fail("All logout methods failed");
            }
            
            // Step 4: Wait for logout to complete
            System.out.println("📍 Step 5: Waiting for logout to complete...");
            helper.longDelay();
            
            // Step 5: Verify logout was successful
            System.out.println("📍 Step 6: Verifying logout was successful...");
            
            // Check current URL and page title
            currentUrl = driver.getCurrentUrl();
            pageTitle = driver.getTitle();
            
            System.out.println("   Current URL after logout: " + currentUrl);
            System.out.println("   Page title after logout: " + pageTitle);
            
            boolean logoutVerified = false;
            
            // Check if redirected to login page
            if (currentUrl.contains("login.php") || pageTitle.toLowerCase().contains("login")) {
                logoutVerified = true;
                System.out.println("   ✅ Logout successful - redirected to login page");
            }
            
            // Check if redirected to home page (as guest)
            if (!logoutVerified && (currentUrl.contains("home.php") || pageTitle.toLowerCase().contains("home"))) {
                // Check if login link is visible (indicates logged out)
                if (helper.isElementDisplayed(LOGIN_LINK)) {
                    logoutVerified = true;
                    System.out.println("   ✅ Logout successful - on home page with login link visible");
                }
            }
            
            // Check if user avatar is no longer visible
            if (!logoutVerified && !helper.isElementDisplayed(USER_AVATAR)) {
                logoutVerified = true;
                System.out.println("   ✅ Logout successful - user avatar no longer visible");
            }
            
            // Check if logout link is no longer visible
            if (!logoutVerified && !helper.isElementDisplayed(LOGOUT_LINK)) {
                logoutVerified = true;
                System.out.println("   ✅ Logout successful - logout link no longer visible");
            }
            
            Assert.assertTrue(logoutVerified, "Logout should be successful");
            
            System.out.println("🎉 Logout functionality test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Logout test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Logout test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test login page navigation links")
    public void testLoginPageNavigationLinks() {
        helper = new PageObjectHelper(driver);
        
        // Navigate to login page
        helper.clickElement(LOGIN_LINK);
        helper.waitForTitleToContain("Login");
        
        // Test navigation to registration page
        By registerLink = By.xpath("//a[contains(text(), 'Create Account')]");
        if (helper.isElementDisplayed(registerLink)) {
            helper.clickElement(registerLink);
            helper.waitForTitleToContain("Register");
            
            // Navigate back to login
            helper.clickElement(LOGIN_LINK);
            helper.waitForTitleToContain("Login");
        }
        
        // Test navigation to home page
        By homeLink = By.xpath("//a[contains(text(), 'home')]");
        if (helper.isElementDisplayed(homeLink)) {
            helper.clickElement(homeLink);
            helper.waitForTitleToContain("Home");
        }
        
        System.out.println("Login page navigation links test completed");
    }
    
    @Test(description = "Test password show/hide toggle functionality")
    public void testPasswordToggleFunctionality() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting password toggle test...");
            
            // Navigate to login page
            System.out.println("📍 Step 1: Navigating to login page...");
            helper.clickElement(LOGIN_LINK);
            helper.waitForTitleToContain("Login");
            helper.mediumDelay();
            
            // Verify login page loaded
            if (!helper.isElementDisplayed(EMAIL_INPUT) || !helper.isElementDisplayed(PASSWORD_INPUT)) {
                Assert.fail("Login page did not load correctly");
            }
            helper.shortDelay();
            
            // Fill password field
            System.out.println("📍 Step 2: Filling password field...");
            String testPassword = "TestPassword123!";
            helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
            helper.mediumDelay(); // Wait to see the password entered
            
            // Verify password is hidden initially
            System.out.println("📍 Step 3: Verifying password is hidden initially...");
            String passwordType = helper.getAttributeFromElement(PASSWORD_INPUT, "type");
            Assert.assertEquals(passwordType, "password", "Password should be hidden initially");
            System.out.println("✅ Password is hidden initially");
            
            // Click password toggle button
            System.out.println("📍 Step 4: Clicking password toggle button...");
            if (helper.isElementDisplayed(PASSWORD_TOGGLE)) {
                helper.clickElement(PASSWORD_TOGGLE);
                helper.shortDelay();
                
                // Verify password is now visible
                System.out.println("📍 Step 5: Verifying password is now visible...");
                passwordType = helper.getAttributeFromElement(PASSWORD_INPUT, "type");
                Assert.assertEquals(passwordType, "text", "Password should be visible after toggle");
                System.out.println("✅ Password is now visible");
                
                // Click toggle again
                System.out.println("📍 Step 6: Clicking password toggle button again...");
                helper.clickElement(PASSWORD_TOGGLE);
                helper.shortDelay();
                
                // Verify password is hidden again
                System.out.println("📍 Step 7: Verifying password is hidden again...");
                passwordType = helper.getAttributeFromElement(PASSWORD_INPUT, "type");
                Assert.assertEquals(passwordType, "password", "Password should be hidden again");
                System.out.println("✅ Password is hidden again");
                
            } else {
                System.out.println("⚠️ Password toggle button not found - feature may not be implemented");
            }
            
            System.out.println("🎉 Password toggle test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Password toggle test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Password toggle test failed: " + e.getMessage());
        }
    }
}
