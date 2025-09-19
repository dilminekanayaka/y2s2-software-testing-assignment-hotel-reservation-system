package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

/**
 * Slow registration test with maximum visibility for debugging
 */
public class SlowRegistrationTest extends TestBase {
    
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
    private static final By SUCCESS_MESSAGE = By.xpath("//div[contains(@class, 'alert-success')]");
    private static final By ERROR_MESSAGE = By.xpath("//div[contains(@class, 'alert-error')]");
    private static final By REGISTER_LINK = By.xpath("//a[@href='register.php']");
    
    /**
     * Generate unique email for testing
     */
    private String generateUniqueEmail() {
        return "slowtest" + System.currentTimeMillis() + "@example.com";
    }
    
    @Test(description = "Slow successful user registration test with maximum visibility")
    public void slowSuccessfulUserRegistration() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🐌 SLOW REGISTRATION TEST - MAXIMUM VISIBILITY 🐌");
            System.out.println("This test runs slowly so you can see every step clearly");
            System.out.println("==================================================");
            
            // Navigate to registration page
            System.out.println("📍 STEP 1: Navigating to registration page...");
            System.out.println("   Current URL: " + driver.getCurrentUrl());
            helper.clickElement(REGISTER_LINK);
            helper.waitForTitleToContain("Register");
            helper.delay(3000); // 3 second delay to see navigation
            
            System.out.println("   New URL: " + driver.getCurrentUrl());
            System.out.println("   Page title: " + driver.getTitle());
            helper.delay(2000); // 2 second delay to see page load
            
            // Generate unique test data
            String testEmail = generateUniqueEmail();
            String testPassword = "SlowTest123!";
            
            System.out.println("📍 STEP 2: Preparing test data...");
            System.out.println("   📧 Test email: " + testEmail);
            System.out.println("   🔒 Test password: " + testPassword);
            helper.delay(2000); // 2 second delay to see test data
            
            System.out.println("📍 STEP 3: Filling registration form (SLOWLY)...");
            
            // Fill each field with delays
            System.out.println("   ✏️ Filling first name: 'Slow'");
            helper.clearAndSendKeys(FIRST_NAME_INPUT, "Slow");
            helper.delay(1500); // 1.5 second delay after each field
            
            System.out.println("   ✏️ Filling last name: 'Test'");
            helper.clearAndSendKeys(LAST_NAME_INPUT, "Test");
            helper.delay(1500);
            
            System.out.println("   ✏️ Filling email: '" + testEmail + "'");
            helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
            helper.delay(1500);
            
            System.out.println("   ✏️ Filling phone: '+1234567890'");
            helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
            helper.delay(1500);
            
            System.out.println("   ✏️ Filling date of birth: '1990-01-01'");
            helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1990-01-01");
            helper.delay(1500);
            
            System.out.println("   ✏️ Filling address: '123 Slow Test Street'");
            helper.clearAndSendKeys(ADDRESS_INPUT, "123 Slow Test Street");
            helper.delay(1500);
            
            System.out.println("   ✏️ Filling password: '" + testPassword + "'");
            helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
            helper.delay(1500);
            
            System.out.println("   ✏️ Confirming password: '" + testPassword + "'");
            helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, testPassword);
            helper.delay(3000); // 3 second delay to see complete form
            
            System.out.println("📍 STEP 4: Submitting registration form...");
            System.out.println("   🔘 Looking for submit button...");
            
            // Try to find and click submit button
            try {
                helper.waitForElementClickability(REGISTER_BUTTON, 10);
                System.out.println("   ✅ Submit button found and clickable");
                helper.delay(1000);
                helper.clickElement(REGISTER_BUTTON);
                System.out.println("   🔘 Submit button clicked!");
            } catch (Exception e) {
                System.out.println("   ❌ Primary button not found, trying alternatives...");
                
                // Try alternative button locators
                By altButton1 = By.xpath("//button[@type='submit']");
                By altButton2 = By.xpath("//input[@type='submit']");
                
                if (helper.isElementDisplayed(altButton1)) {
                    helper.clickElement(altButton1);
                    System.out.println("   ✅ Used alternative button locator 1");
                } else if (helper.isElementDisplayed(altButton2)) {
                    helper.clickElement(altButton2);
                    System.out.println("   ✅ Used alternative button locator 2");
                } else {
                    System.out.println("   ❌ No submit button found!");
                    Assert.fail("No submit button found on the page");
                }
            }
            
            // Wait for response
            System.out.println("📍 STEP 5: Waiting for server response...");
            System.out.println("   ⏳ Waiting 5 seconds for form processing...");
            helper.delay(5000); // 5 second delay for server response
            
            System.out.println("   Current URL after submission: " + driver.getCurrentUrl());
            System.out.println("   Page title after submission: " + driver.getTitle());
            helper.delay(2000);
            
            // Check for success message
            System.out.println("📍 STEP 6: Checking for success/error message...");
            
            if (helper.isElementDisplayed(SUCCESS_MESSAGE)) {
                String successText = helper.getTextFromElement(SUCCESS_MESSAGE);
                System.out.println("🎉 SUCCESS! Registration completed!");
                System.out.println("📝 Success message: " + successText);
                helper.delay(5000); // 5 second delay to see success message
                
                Assert.assertTrue(successText.contains("successful") || successText.contains("created"), 
                    "Success message should contain 'successful' or 'created'");
                
            } else {
                // Check for error message
                if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                    String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                    System.out.println("❌ Registration failed with error:");
                    System.out.println("📝 Error message: " + errorText);
                    helper.delay(5000); // 5 second delay to see error message
                    Assert.fail("Registration failed with error: " + errorText);
                } else {
                    System.out.println("❌ No success or error message found");
                    System.out.println("   This might indicate a page loading issue");
                    helper.delay(5000); // 5 second delay to see current state
                    Assert.fail("Registration test failed - no response message found");
                }
            }
            
            System.out.println("🎉 SLOW REGISTRATION TEST COMPLETED!");
            System.out.println("==================================================");
            
        } catch (Exception e) {
            System.out.println("❌ Slow registration test failed with exception:");
            System.out.println("   Error: " + e.getMessage());
            e.printStackTrace();
            helper.delay(5000); // 5 second delay to see the error
            Assert.fail("Slow registration test failed: " + e.getMessage());
        }
    }
}
