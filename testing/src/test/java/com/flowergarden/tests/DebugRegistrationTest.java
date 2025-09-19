package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.annotations.Test;

/**
 * Debug test to help identify registration page issues
 */
public class DebugRegistrationTest extends TestBase {
    
    private PageObjectHelper helper;
    
    @Test(description = "Debug registration page loading")
    public void debugRegistrationPage() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("=== DEBUGGING REGISTRATION PAGE ===");
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            
            // Try to find registration link
            By registerLink = By.xpath("//a[@href='register.php']");
            if (helper.isElementDisplayed(registerLink)) {
                System.out.println("✅ Registration link found");
                helper.clickElement(registerLink);
                
                // Wait for page to load
                Thread.sleep(3000);
                
                System.out.println("After clicking register link:");
                System.out.println("Current URL: " + driver.getCurrentUrl());
                System.out.println("Page title: " + driver.getTitle());
                
                // Check for form elements
                By firstName = By.id("first_name");
                By email = By.id("email");
                By password = By.id("password");
                By confirmPassword = By.id("confirm_password");
                By submitButton = By.xpath("//button[contains(text(), 'Create Account')]");
                By altButton1 = By.xpath("//button[@type='submit']");
                By altButton2 = By.xpath("//input[@type='submit']");
                
                System.out.println("\nForm elements check:");
                System.out.println("First name field: " + (helper.isElementDisplayed(firstName) ? "✅" : "❌"));
                System.out.println("Email field: " + (helper.isElementDisplayed(email) ? "✅" : "❌"));
                System.out.println("Password field: " + (helper.isElementDisplayed(password) ? "✅" : "❌"));
                System.out.println("Confirm password field: " + (helper.isElementDisplayed(confirmPassword) ? "✅" : "❌"));
                System.out.println("Submit button (text): " + (helper.isElementDisplayed(submitButton) ? "✅" : "❌"));
                System.out.println("Submit button (type): " + (helper.isElementDisplayed(altButton1) ? "✅" : "❌"));
                System.out.println("Input submit: " + (helper.isElementDisplayed(altButton2) ? "✅" : "❌"));
                
                // Get page source snippet
                String pageSource = driver.getPageSource();
                if (pageSource.contains("Create Account")) {
                    System.out.println("✅ 'Create Account' text found in page source");
                } else {
                    System.out.println("❌ 'Create Account' text NOT found in page source");
                }
                
                if (pageSource.contains("register.php")) {
                    System.out.println("✅ 'register.php' found in page source");
                } else {
                    System.out.println("❌ 'register.php' NOT found in page source");
                }
                
                // Try to get all buttons on the page
                System.out.println("\nAll buttons on page:");
                var buttons = driver.findElements(By.tagName("button"));
                for (int i = 0; i < buttons.size(); i++) {
                    var button = buttons.get(i);
                    System.out.println("Button " + i + ": text='" + button.getText() + "', type='" + button.getAttribute("type") + "'");
                }
                
            } else {
                System.out.println("❌ Registration link not found");
                
                // List all links on the page
                System.out.println("\nAll links on page:");
                var links = driver.findElements(By.tagName("a"));
                for (int i = 0; i < Math.min(links.size(), 10); i++) {
                    var link = links.get(i);
                    System.out.println("Link " + i + ": text='" + link.getText() + "', href='" + link.getAttribute("href") + "'");
                }
            }
            
        } catch (Exception e) {
            System.out.println("❌ Debug test failed: " + e.getMessage());
            e.printStackTrace();
        }
    }
}
