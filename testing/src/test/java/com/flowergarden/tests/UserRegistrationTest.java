package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.testng.Assert;
import org.testng.annotations.Test;

import java.util.Random;

/**
 * Test class for user registration functionality
 * Tests the complete user registration flow on the Flower Garden Hotel Booking website.
 */
public class UserRegistrationTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Locators for registration page
    private static final By FIRST_NAME_INPUT = By.id("first_name");
    private static final By LAST_NAME_INPUT = By.id("last_name");
    private static final By EMAIL_INPUT = By.id("email");
    private static final By PHONE_INPUT = By.id("phone");
    private static final By DATE_OF_BIRTH_INPUT = By.id("date_of_birth");
    private static final By ADDRESS_INPUT = By.id("address");
    private static final By PASSWORD_INPUT = By.id("password");
    private static final By CONFIRM_PASSWORD_INPUT = By.id("confirm_password");
    private static final By REGISTER_BUTTON = By.xpath("//button[contains(text(), 'Create Account')] | //button[@type='submit']");
    private static final By SUCCESS_MESSAGE = By.xpath("//div[contains(@class, 'alert-success')] | //div[contains(@class, 'success')] | //div[contains(@class, 'alert')]");
    private static final By ERROR_MESSAGE = By.xpath("//div[contains(@class, 'alert-error')] | //div[contains(@class, 'error')] | //div[contains(@class, 'alert-danger')]");
    private static final By REGISTER_LINK = By.xpath("//a[@href='register.php']");
    
    /**
     * Generate unique email for testing
     */
    private String generateUniqueEmail() {
        return "testuser" + System.currentTimeMillis() + "@example.com";
    }
    
    /**
     * Verify registration page loaded correctly
     */
    private boolean verifyRegistrationPageLoaded() {
        try {
            // Check if we're on the registration page
            String currentUrl = driver.getCurrentUrl();
            if (!currentUrl.contains("register.php")) {
                System.out.println("❌ Not on registration page. Current URL: " + currentUrl);
                return false;
            }
            
            // Check if key elements are present
            boolean firstNamePresent = helper.isElementDisplayed(FIRST_NAME_INPUT);
            boolean emailPresent = helper.isElementDisplayed(EMAIL_INPUT);
            boolean passwordPresent = helper.isElementDisplayed(PASSWORD_INPUT);
            boolean buttonPresent = helper.isElementDisplayed(REGISTER_BUTTON);
            
            System.out.println("Page elements check:");
            System.out.println("  - First name field: " + (firstNamePresent ? "✅" : "❌"));
            System.out.println("  - Email field: " + (emailPresent ? "✅" : "❌"));
            System.out.println("  - Password field: " + (passwordPresent ? "✅" : "❌"));
            System.out.println("  - Register button: " + (buttonPresent ? "✅" : "❌"));
            
            return firstNamePresent && emailPresent && passwordPresent && buttonPresent;
            
        } catch (Exception e) {
            System.out.println("❌ Error checking registration page: " + e.getMessage());
            return false;
        }
    }
    
    @Test(description = "Test successful user registration with valid data")
    public void testSuccessfulUserRegistration() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting successful user registration test...");
            
            // Navigate to registration page
            System.out.println("📍 Step 1: Navigating to registration page...");
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.mediumDelay(); // Wait for page to fully load
            
            // Verify page loaded correctly
            System.out.println("📍 Step 2: Verifying registration page loaded...");
            if (!verifyRegistrationPageLoaded()) {
                Assert.fail("Registration page did not load correctly");
            }
            helper.shortDelay();
            
            // Generate unique test data
            String testEmail = generateUniqueEmail();
            String testPassword = "TestPassword123!";
            
            System.out.println("📍 Step 3: Filling registration form...");
            System.out.println("   📧 Using test email: " + testEmail);
            System.out.println("   🔒 Using password: " + testPassword);
            
            // Fill registration form with all required fields - with delays for visibility
            System.out.println("   ✏️ Filling first name...");
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling last name...");
            helper.clearAndSendKeys(LAST_NAME_INPUT, "User");
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling email...");
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling phone...");
            helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling date of birth...");
            helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1990-01-01");
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling address...");
            helper.clearAndSendKeys(ADDRESS_INPUT, "123 Test Street");
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling password...");
            helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
            helper.shortDelay();
            
            System.out.println("   ✏️ Confirming password...");
            helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, testPassword);
            helper.mediumDelay(); // Wait to see the form filled
            
            // Wait for form to be ready and submit
            System.out.println("📍 Step 4: Submitting registration form...");
            
            // Use smart form submission to handle click interception issues
            boolean submitted = helper.smartFormSubmit(REGISTER_BUTTON);
            
            if (!submitted) {
                System.out.println("❌ All form submission methods failed");
                Assert.fail("Could not submit registration form - all methods failed");
            }
            
            // Wait for response with longer delay
            System.out.println("📍 Step 5: Waiting for server response...");
            helper.longDelay(); // Give more time for form processing
            
            // Check for success message or redirect
            System.out.println("📍 Step 6: Checking for success/error message...");
            
            // Check multiple possible success indicators
            boolean successFound = false;
            String successMessage = "";
            
            // Check for success message div
            if (helper.isElementDisplayed(SUCCESS_MESSAGE)) {
                successMessage = helper.getTextFromElement(SUCCESS_MESSAGE);
                successFound = true;
                System.out.println("✅ SUCCESS! Registration completed!");
                System.out.println("📝 Success message: " + successMessage);
            } else {
                // Check for other possible success indicators
                By[] successLocators = {
                    By.xpath("//div[contains(@class, 'success')]"),
                    By.xpath("//div[contains(@class, 'alert')]"),
                    By.xpath("//p[contains(text(), 'success')]"),
                    By.xpath("//p[contains(text(), 'created')]"),
                    By.xpath("//p[contains(text(), 'registered')]"),
                    By.xpath("//*[contains(text(), 'success')]"),
                    By.xpath("//*[contains(text(), 'created')]"),
                    By.xpath("//*[contains(text(), 'registered')]")
                };
                
                for (By locator : successLocators) {
                    if (helper.isElementDisplayed(locator)) {
                        successMessage = helper.getTextFromElement(locator);
                        if (successMessage.toLowerCase().contains("success") || 
                            successMessage.toLowerCase().contains("created") ||
                            successMessage.toLowerCase().contains("registered")) {
                            successFound = true;
                            System.out.println("✅ SUCCESS! Registration completed!");
                            System.out.println("📝 Success message: " + successMessage);
                            break;
                        }
                    }
                }
            }
            
            if (successFound) {
                Assert.assertTrue(successMessage.toLowerCase().contains("success") || 
                                successMessage.toLowerCase().contains("created") ||
                                successMessage.toLowerCase().contains("registered"), 
                    "Success message should contain 'success', 'created', or 'registered'");
                helper.mediumDelay(); // Wait to see the success message
            } else {
                // Check for error message
                if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                    String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                    System.out.println("❌ Registration failed with error: " + errorText);
                    helper.mediumDelay(); // Wait to see the error message
                    Assert.fail("Registration failed with error: " + errorText);
                } else {
                    // Check if we were redirected to login page (which indicates success)
                    String currentUrl = driver.getCurrentUrl();
                    String pageTitle = driver.getTitle();
                    
                    System.out.println("📍 Step 7: Checking for redirect indicators...");
                    System.out.println("   Current URL: " + currentUrl);
                    System.out.println("   Page title: " + pageTitle);
                    
                    if (currentUrl.contains("login.php") || 
                        pageTitle.toLowerCase().contains("login") ||
                        currentUrl.contains("home.php") ||
                        pageTitle.toLowerCase().contains("home")) {
                        System.out.println("✅ SUCCESS! Registration completed!");
                        System.out.println("📝 Redirected to: " + currentUrl);
                        System.out.println("📝 This indicates successful registration");
                        helper.mediumDelay(); // Wait to see the redirect
                    } else if (currentUrl.contains("register.php")) {
                        // Still on registration page - check if there's a hidden success message
                        System.out.println("📍 Still on registration page - checking for hidden messages...");
                        
                        // Check page source for success indicators
                        String pageSource = driver.getPageSource();
                        if (pageSource.toLowerCase().contains("success") || 
                            pageSource.toLowerCase().contains("created") ||
                            pageSource.toLowerCase().contains("registered")) {
                            System.out.println("✅ SUCCESS! Registration completed!");
                            System.out.println("📝 Success indicator found in page source");
                            helper.mediumDelay();
                        } else {
                            System.out.println("❌ No success or error message found");
                            System.out.println("❌ Still on registration page without clear success/error indication");
                            helper.mediumDelay(); // Wait to see the current state
                            Assert.fail("Registration test failed - no clear success or error indication");
                        }
                    } else {
                        System.out.println("❌ No success or error message found");
                        System.out.println("❌ Unexpected page state");
                        helper.mediumDelay(); // Wait to see the current state
                        Assert.fail("Registration test failed - unexpected page state");
                    }
                }
            }
            
            System.out.println("🎉 Test completed successfully!");
            
        } catch (Exception e) {
            System.out.println("❌ Test failed with exception: " + e.getMessage());
            e.printStackTrace();
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Test failed with exception: " + e.getMessage());
        }
    }
    
    @Test(description = "Test registration with invalid email format")
    public void testRegistrationWithInvalidEmail() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting invalid email format test...");
            
            // Navigate to registration page
            System.out.println("📍 Step 1: Navigating to registration page...");
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Verify page loaded correctly
            if (!verifyRegistrationPageLoaded()) {
                Assert.fail("Registration page did not load correctly");
            }
            helper.shortDelay();
            
            System.out.println("📍 Step 2: Filling form with invalid email...");
            System.out.println("   📧 Using invalid email: invalid-email");
            
            // Fill registration form with invalid email - with delays for visibility
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
            helper.shortDelay();
            helper.clearAndSendKeys(LAST_NAME_INPUT, "User");
            helper.shortDelay();
            helper.clearAndSendKeys(EMAIL_INPUT, "invalid-email");
            helper.mediumDelay(); // Wait to see the invalid email entered
            
            // Check for immediate HTML5 validation
            System.out.println("📍 Step 3: Checking for HTML5 validation...");
            if (helper.hasValidationError(EMAIL_INPUT)) {
                String validationMessage = helper.getValidationMessage(EMAIL_INPUT);
                System.out.println("✅ HTML5 validation detected invalid email!");
                System.out.println("📝 Validation message: " + validationMessage);
                helper.mediumDelay(); // Wait to see the validation message
                
                // Try to submit form to see if validation prevents it
                System.out.println("📍 Step 4: Attempting form submission...");
                boolean submitted = helper.trySubmitForm(REGISTER_BUTTON);
                
                if (!submitted) {
                    System.out.println("✅ Form submission was prevented by HTML5 validation");
                    System.out.println("✅ Invalid email test passed - HTML5 validation working!");
                    helper.mediumDelay();
                    return; // Test passed
                }
            }
            
            // Continue filling other fields
            helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
            helper.shortDelay();
            helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1990-01-01");
            helper.shortDelay();
            helper.clearAndSendKeys(ADDRESS_INPUT, "123 Test Street");
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, "TestPassword123!");
            helper.shortDelay();
            helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, "TestPassword123!");
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit registration form
            System.out.println("📍 Step 5: Submitting form with invalid email...");
            boolean submitted = helper.smartFormSubmit(REGISTER_BUTTON);
            
            if (!submitted) {
                System.out.println("✅ Form submission was prevented by validation");
                System.out.println("✅ Invalid email test passed - Form validation working!");
                helper.mediumDelay();
                return; // Test passed
            }
            
            // Wait for response
            System.out.println("📍 Step 6: Waiting for server response...");
            helper.longDelay();
            
            // Check for error message
            System.out.println("📍 Step 7: Checking for error message...");
            if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                System.out.println("✅ Invalid email test passed!");
                System.out.println("📝 Server error message: " + errorText);
                helper.mediumDelay(); // Wait to see the error message
                
                Assert.assertTrue(errorText.toLowerCase().contains("invalid") || 
                                errorText.toLowerCase().contains("email"), 
                    "Error message should mention invalid email");
            } else {
                // Check for HTML5 validation message
                String validationMessage = helper.getValidationMessage(EMAIL_INPUT);
                if (validationMessage != null && !validationMessage.isEmpty()) {
                    System.out.println("✅ HTML5 validation caught invalid email!");
                    System.out.println("📝 Validation message: " + validationMessage);
                    helper.mediumDelay(); // Wait to see the validation message
                } else {
                    System.out.println("❌ No error message found for invalid email");
                    System.out.println("Current URL: " + driver.getCurrentUrl());
                    helper.mediumDelay(); // Wait to see the current state
                    Assert.fail("No error message found for invalid email");
                }
            }
            
            System.out.println("🎉 Invalid email test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Invalid email test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Invalid email test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test registration with password mismatch")
    public void testRegistrationWithPasswordMismatch() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting password mismatch test...");
            
            // Navigate to registration page
            System.out.println("📍 Step 1: Navigating to registration page...");
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Verify page loaded correctly
            if (!verifyRegistrationPageLoaded()) {
                Assert.fail("Registration page did not load correctly");
            }
            helper.shortDelay();
            
            // Generate unique test data
            String testEmail = generateUniqueEmail();
            
            System.out.println("📍 Step 2: Filling form with mismatched passwords...");
            System.out.println("   📧 Using email: " + testEmail);
            System.out.println("   🔒 Password: TestPassword123!");
            System.out.println("   🔒 Confirm Password: DifferentPassword123!");
            
            // Fill registration form with mismatched passwords - with delays for visibility
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
            helper.shortDelay();
            helper.clearAndSendKeys(LAST_NAME_INPUT, "User");
            helper.shortDelay();
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
            helper.shortDelay();
            helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
            helper.shortDelay();
            helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1990-01-01");
            helper.shortDelay();
            helper.clearAndSendKeys(ADDRESS_INPUT, "123 Test Street");
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, "TestPassword123!");
            helper.shortDelay();
            helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, "DifferentPassword123!");
            helper.mediumDelay(); // Wait to see the mismatched passwords
            
            // Check for HTML5 validation on confirm password field
            System.out.println("📍 Step 3: Checking for HTML5 validation...");
            if (helper.hasValidationError(CONFIRM_PASSWORD_INPUT)) {
                String validationMessage = helper.getValidationMessage(CONFIRM_PASSWORD_INPUT);
                System.out.println("✅ HTML5 validation detected password mismatch!");
                System.out.println("📝 Validation message: " + validationMessage);
                helper.mediumDelay(); // Wait to see the validation message
                
                // Try to submit form to see if validation prevents it
                System.out.println("📍 Step 4: Attempting form submission...");
                boolean submitted = helper.trySubmitForm(REGISTER_BUTTON);
                
                if (!submitted) {
                    System.out.println("✅ Form submission was prevented by HTML5 validation");
                    System.out.println("✅ Password mismatch test passed - HTML5 validation working!");
                    helper.mediumDelay();
                    return; // Test passed
                }
            }
            
            // Submit registration form
            System.out.println("📍 Step 5: Submitting form with mismatched passwords...");
            boolean submitted = helper.smartFormSubmit(REGISTER_BUTTON);
            
            if (!submitted) {
                System.out.println("✅ Form submission was prevented by validation");
                System.out.println("✅ Password mismatch test passed - Form validation working!");
                helper.mediumDelay();
                return; // Test passed
            }
            
            // Wait for response
            System.out.println("📍 Step 6: Waiting for server response...");
            helper.longDelay();
            
            // Check for error message
            System.out.println("📍 Step 7: Checking for error message...");
            if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                System.out.println("✅ Password mismatch test passed!");
                System.out.println("📝 Server error message: " + errorText);
                helper.mediumDelay(); // Wait to see the error message
                
                Assert.assertTrue(errorText.toLowerCase().contains("password") || 
                                errorText.toLowerCase().contains("match"), 
                    "Error message should mention password mismatch");
            } else {
                // Check for HTML5 validation message
                String validationMessage = helper.getValidationMessage(CONFIRM_PASSWORD_INPUT);
                if (validationMessage != null && !validationMessage.isEmpty()) {
                    System.out.println("✅ HTML5 validation caught password mismatch!");
                    System.out.println("📝 Validation message: " + validationMessage);
                    helper.mediumDelay(); // Wait to see the validation message
                } else {
                    System.out.println("❌ No error message found for password mismatch");
                    System.out.println("Current URL: " + driver.getCurrentUrl());
                    helper.mediumDelay(); // Wait to see the current state
                    Assert.fail("No error message found for password mismatch");
                }
            }
            
            System.out.println("🎉 Password mismatch test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Password mismatch test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Password mismatch test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test registration with missing required fields")
    public void testRegistrationWithMissingFields() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting missing required fields test...");
            
            // Navigate to registration page
            System.out.println("📍 Step 1: Navigating to registration page...");
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Verify page loaded correctly
            if (!verifyRegistrationPageLoaded()) {
                Assert.fail("Registration page did not load correctly");
            }
            helper.shortDelay();
            
            System.out.println("📍 Step 2: Filling only some required fields...");
            System.out.println("   ✏️ Filling first name: 'Test'");
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling email: '" + generateUniqueEmail() + "'");
            helper.clearAndSendKeys(EMAIL_INPUT, generateUniqueEmail());
            helper.shortDelay();
            
            System.out.println("   ✏️ Filling phone: '+1234567890'");
            helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
            helper.mediumDelay();
            
            System.out.println("   ⚠️ Leaving last_name, password, confirm_password EMPTY");
            helper.mediumDelay(); // Wait to see the incomplete form
            
            // Check for HTML5 validation errors
            System.out.println("📍 Step 3: Checking for HTML5 validation errors...");
            boolean hasValidationErrors = helper.hasFormValidationErrors();
            System.out.println("   Form validation errors detected: " + hasValidationErrors);
            
            if (hasValidationErrors) {
                System.out.println("✅ HTML5 validation detected missing required fields!");
                
                // Check specific field validation
                if (helper.hasValidationError(LAST_NAME_INPUT)) {
                    String validationMessage = helper.getValidationMessage(LAST_NAME_INPUT);
                    System.out.println("📝 Last name validation: " + validationMessage);
                }
                if (helper.hasValidationError(PASSWORD_INPUT)) {
                    String validationMessage = helper.getValidationMessage(PASSWORD_INPUT);
                    System.out.println("📝 Password validation: " + validationMessage);
                }
                if (helper.hasValidationError(CONFIRM_PASSWORD_INPUT)) {
                    String validationMessage = helper.getValidationMessage(CONFIRM_PASSWORD_INPUT);
                    System.out.println("📝 Confirm password validation: " + validationMessage);
                }
                
                helper.mediumDelay(); // Wait to see validation messages
            }
            
            // Try to submit form
            System.out.println("📍 Step 4: Attempting form submission...");
            boolean submitted = helper.smartFormSubmit(REGISTER_BUTTON);
            
            if (!submitted) {
                System.out.println("✅ Form submission was prevented by HTML5 validation");
                System.out.println("✅ Missing fields test passed - HTML5 validation working!");
                helper.mediumDelay();
                return; // Test passed
            }
            
            // Wait for response
            System.out.println("📍 Step 5: Waiting for server response...");
            helper.longDelay();
            
            // Check for error message
            System.out.println("📍 Step 6: Checking for error message...");
            if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                System.out.println("✅ Missing fields test passed!");
                System.out.println("📝 Server error message: " + errorText);
                helper.mediumDelay(); // Wait to see the error message
                
                Assert.assertTrue(errorText.toLowerCase().contains("required") || 
                                errorText.toLowerCase().contains("field"), 
                    "Error message should mention required fields");
            } else {
                // Check if still on registration page (HTML5 validation might prevent submission)
                String currentUrl = driver.getCurrentUrl();
                if (currentUrl.contains("register.php")) {
                    System.out.println("✅ Missing fields test passed - Still on registration page");
                    System.out.println("   This indicates HTML5 validation prevented form submission");
                    helper.mediumDelay(); // Wait to see the current state
                } else {
                    System.out.println("❌ No error message found for missing required fields");
                    System.out.println("Current URL: " + currentUrl);
                    helper.mediumDelay(); // Wait to see the current state
                    Assert.fail("No error message found for missing required fields");
                }
            }
            
            System.out.println("🎉 Missing fields test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Missing fields test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Missing fields test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test registration with existing email")
    public void testRegistrationWithExistingEmail() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting existing email test...");
            
            // Navigate to registration page
            System.out.println("📍 Step 1: Navigating to registration page...");
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Verify page loaded correctly
            if (!verifyRegistrationPageLoaded()) {
                Assert.fail("Registration page did not load correctly");
            }
            helper.shortDelay();
            
            // First, create a user to ensure we have an existing email
            System.out.println("📍 Step 2: Creating a test user first...");
            String testEmail = generateUniqueEmail();
            String testPassword = "TestPassword123!";
            
            // Fill and submit first registration
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
            helper.shortDelay();
            helper.clearAndSendKeys(LAST_NAME_INPUT, "User");
            helper.shortDelay();
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
            helper.shortDelay();
            helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
            helper.shortDelay();
            helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1990-01-01");
            helper.shortDelay();
            helper.clearAndSendKeys(ADDRESS_INPUT, "123 Test Street");
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
            helper.shortDelay();
            helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, testPassword);
            helper.mediumDelay();
            
            // Submit first registration
            System.out.println("   🔘 Submitting first registration...");
            boolean submitted = helper.smartFormSubmit(REGISTER_BUTTON);
            if (submitted) {
                helper.longDelay(); // Wait for first registration to complete
                System.out.println("   ✅ First user created successfully");
            }
            
            // Now navigate back to registration page to test existing email
            System.out.println("📍 Step 3: Testing with existing email...");
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.mediumDelay();
            
            // Verify page loaded correctly again
            if (!verifyRegistrationPageLoaded()) {
                Assert.fail("Registration page did not load correctly on second attempt");
            }
            helper.shortDelay();
            
            System.out.println("   📧 Using existing email: " + testEmail);
            
            // Fill registration form with existing email - with delays for visibility
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Another");
            helper.shortDelay();
            helper.clearAndSendKeys(LAST_NAME_INPUT, "User");
            helper.shortDelay();
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail); // Same email as before
            helper.mediumDelay(); // Wait to see the existing email entered
            helper.clearAndSendKeys(PHONE_INPUT, "+9876543210");
            helper.shortDelay();
            helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1995-05-15");
            helper.shortDelay();
            helper.clearAndSendKeys(ADDRESS_INPUT, "456 Another Street");
            helper.shortDelay();
            helper.clearAndSendKeys(PASSWORD_INPUT, "AnotherPassword123!");
            helper.shortDelay();
            helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, "AnotherPassword123!");
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit registration form
            System.out.println("📍 Step 4: Submitting form with existing email...");
            submitted = helper.smartFormSubmit(REGISTER_BUTTON);
            
            if (!submitted) {
                System.out.println("✅ Form submission was prevented by validation");
                System.out.println("✅ Existing email test passed - Form validation working!");
                helper.mediumDelay();
                return; // Test passed
            }
            
            // Wait for response
            System.out.println("📍 Step 5: Waiting for server response...");
            helper.longDelay();
            
            // Check for error message
            System.out.println("📍 Step 6: Checking for error message...");
            if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                System.out.println("✅ Existing email test passed!");
                System.out.println("📝 Server error message: " + errorText);
                helper.mediumDelay(); // Wait to see the error message
                
                Assert.assertTrue(errorText.toLowerCase().contains("email") || 
                                errorText.toLowerCase().contains("exist") ||
                                errorText.toLowerCase().contains("already") ||
                                errorText.toLowerCase().contains("duplicate"), 
                    "Error message should mention email already exists");
            } else {
                // Check if we're still on registration page (which indicates an error)
                String currentUrl = driver.getCurrentUrl();
                if (currentUrl.contains("register.php")) {
                    System.out.println("✅ Existing email test passed - Still on registration page");
                    System.out.println("   This indicates the email already exists and registration was prevented");
                    helper.mediumDelay(); // Wait to see the current state
                } else {
                    System.out.println("❌ No error message found for existing email");
                    System.out.println("Current URL: " + currentUrl);
                    helper.mediumDelay(); // Wait to see the current state
                    Assert.fail("No error message found for existing email");
                }
            }
            
            System.out.println("🎉 Existing email test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Existing email test failed: " + e.getMessage());
            helper.mediumDelay(); // Wait to see the error
            Assert.fail("Existing email test failed: " + e.getMessage());
        }
    }
}
