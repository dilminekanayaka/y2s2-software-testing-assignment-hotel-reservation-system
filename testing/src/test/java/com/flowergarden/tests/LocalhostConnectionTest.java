package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

/**
 * Test to verify localhost connection and basic page loading
 */
public class LocalhostConnectionTest extends TestBase {
    
    private PageObjectHelper helper;
    
    @Test(description = "Test localhost connection and basic navigation")
    public void testLocalhostConnection() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("=== TESTING LOCALHOST CONNECTION ===");
            System.out.println("Base URL: " + baseUrl);
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            
            // Check if we're on the home page
            Assert.assertTrue(driver.getCurrentUrl().contains("localhost"), 
                "Should be on localhost");
            
            // Check if home page loaded
            Assert.assertTrue(driver.getTitle().contains("Flower Garden") || 
                            driver.getTitle().contains("Home"), 
                "Should be on Flower Garden home page");
            
            System.out.println("✅ Home page loaded successfully");
            
            // Try to find navigation links
            By homeLink = By.xpath("//a[contains(@href, 'home.php')]");
            By hotelsLink = By.xpath("//a[contains(@href, 'hotels.php')]");
            By loginLink = By.xpath("//a[contains(@href, 'login.php')]");
            By registerLink = By.xpath("//a[contains(@href, 'register.php')]");
            
            System.out.println("\nNavigation links check:");
            System.out.println("Home link: " + (helper.isElementDisplayed(homeLink) ? "✅" : "❌"));
            System.out.println("Hotels link: " + (helper.isElementDisplayed(hotelsLink) ? "✅" : "❌"));
            System.out.println("Login link: " + (helper.isElementDisplayed(loginLink) ? "✅" : "❌"));
            System.out.println("Register link: " + (helper.isElementDisplayed(registerLink) ? "✅" : "❌"));
            
            // Try to click register link
            if (helper.isElementDisplayed(registerLink)) {
                System.out.println("\nTesting register link click...");
                helper.clickElement(registerLink);
                
                // Wait for page to load
                Thread.sleep(3000);
                
                System.out.println("After clicking register:");
                System.out.println("Current URL: " + driver.getCurrentUrl());
                System.out.println("Page title: " + driver.getTitle());
                
                // Check if we're on registration page
                if (driver.getCurrentUrl().contains("register.php")) {
                    System.out.println("✅ Successfully navigated to registration page");
                    
                    // Check for form elements
                    By firstName = By.id("first_name");
                    By email = By.id("email");
                    By password = By.id("password");
                    By submitButton = By.xpath("//button[contains(text(), 'Create Account')]");
                    
                    System.out.println("\nRegistration form elements:");
                    System.out.println("First name: " + (helper.isElementDisplayed(firstName) ? "✅" : "❌"));
                    System.out.println("Email: " + (helper.isElementDisplayed(email) ? "✅" : "❌"));
                    System.out.println("Password: " + (helper.isElementDisplayed(password) ? "✅" : "❌"));
                    System.out.println("Submit button: " + (helper.isElementDisplayed(submitButton) ? "✅" : "❌"));
                    
                } else {
                    System.out.println("❌ Failed to navigate to registration page");
                    System.out.println("Expected URL to contain 'register.php'");
                }
            } else {
                System.out.println("❌ Register link not found on home page");
            }
            
        } catch (Exception e) {
            System.out.println("❌ Localhost connection test failed: " + e.getMessage());
            e.printStackTrace();
            Assert.fail("Localhost connection test failed: " + e.getMessage());
        }
    }
}
