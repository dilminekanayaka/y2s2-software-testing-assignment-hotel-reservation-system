package com.flowergarden.utils;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.time.Duration;
import java.util.List;

/**
 * Helper class providing common page object operations
 * for the Flower Garden Hotel Booking test suite.
 */
public class PageObjectHelper {
    
    private WebDriver driver;
    private WebDriverWait wait;
    
    public PageObjectHelper(WebDriver driver) {
        this.driver = driver;
        this.wait = new WebDriverWait(driver, Duration.ofSeconds(20));
    }
    
    /**
     * Wait for element to be visible and clickable
     */
    public WebElement waitForElementToBeClickable(By locator) {
        return wait.until(ExpectedConditions.elementToBeClickable(locator));
    }
    
    /**
     * Wait for element to be visible
     */
    public WebElement waitForElementToBeVisible(By locator) {
        return wait.until(ExpectedConditions.visibilityOfElementLocated(locator));
    }
    
    /**
     * Wait for element to be present in DOM
     */
    public WebElement waitForElementToBePresent(By locator) {
        return wait.until(ExpectedConditions.presenceOfElementLocated(locator));
    }
    
    /**
     * Wait for text to be present in element
     */
    public boolean waitForTextToBePresentInElement(By locator, String text) {
        return wait.until(ExpectedConditions.textToBePresentInElementLocated(locator, text));
    }
    
    /**
     * Wait for URL to contain specific text
     */
    public boolean waitForUrlToContain(String text) {
        return wait.until(ExpectedConditions.urlContains(text));
    }
    
    /**
     * Wait for page title to contain specific text
     */
    public boolean waitForTitleToContain(String text) {
        return wait.until(ExpectedConditions.titleContains(text));
    }
    
    /**
     * Wait for element to be visible with custom timeout
     */
    public WebElement waitForElementVisibility(By locator, int timeoutInSeconds) {
        WebDriverWait customWait = new WebDriverWait(driver, Duration.ofSeconds(timeoutInSeconds));
        return customWait.until(ExpectedConditions.visibilityOfElementLocated(locator));
    }
    
    /**
     * Wait for element to be clickable with custom timeout
     */
    public WebElement waitForElementClickability(By locator, int timeoutInSeconds) {
        WebDriverWait customWait = new WebDriverWait(driver, Duration.ofSeconds(timeoutInSeconds));
        return customWait.until(ExpectedConditions.elementToBeClickable(locator));
    }
    
    /**
     * Wait for element to be clickable (default timeout)
     */
    public WebElement waitForElementClickability(By locator) {
        return waitForElementClickability(locator, 20);
    }
    
    /**
     * Click element with wait
     */
    public void clickElement(By locator) {
        WebElement element = waitForElementToBeClickable(locator);
        element.click();
    }
    
    /**
     * Send keys to element with wait
     */
    public void sendKeysToElement(By locator, String text) {
        WebElement element = waitForElementToBeVisible(locator);
        element.clear();
        element.sendKeys(text);
    }
    
    /**
     * Clear and send keys to element with wait
     */
    public void clearAndSendKeys(By locator, String text) {
        WebElement element = waitForElementToBeVisible(locator);
        element.clear();
        element.sendKeys(text);
    }
    
    /**
     * Get text from element with wait
     */
    public String getTextFromElement(By locator) {
        WebElement element = waitForElementToBeVisible(locator);
        return element.getText();
    }
    
    /**
     * Check if element is displayed
     */
    public boolean isElementDisplayed(By locator) {
        try {
            WebElement element = waitForElementToBeVisible(locator);
            return element.isDisplayed();
        } catch (Exception e) {
            return false;
        }
    }
    
    /**
     * Check if element is enabled
     */
    public boolean isElementEnabled(By locator) {
        try {
            WebElement element = waitForElementToBeVisible(locator);
            return element.isEnabled();
        } catch (Exception e) {
            return false;
        }
    }
    
    /**
     * Get attribute value from element
     */
    public String getAttributeFromElement(By locator, String attribute) {
        WebElement element = waitForElementToBeVisible(locator);
        return element.getAttribute(attribute);
    }
    
    /**
     * Select option from dropdown by visible text
     */
    public void selectDropdownByVisibleText(By locator, String text) {
        WebElement element = waitForElementToBeClickable(locator);
        org.openqa.selenium.support.ui.Select select = 
            new org.openqa.selenium.support.ui.Select(element);
        select.selectByVisibleText(text);
    }
    
    /**
     * Select option from dropdown by value
     */
    public void selectDropdownByValue(By locator, String value) {
        WebElement element = waitForElementToBeClickable(locator);
        org.openqa.selenium.support.ui.Select select = 
            new org.openqa.selenium.support.ui.Select(element);
        select.selectByValue(value);
    }
    
    /**
     * Get all elements matching locator
     */
    public List<WebElement> getElements(By locator) {
        waitForElementToBePresent(locator);
        return driver.findElements(locator);
    }
    
    /**
     * Scroll to element
     */
    public void scrollToElement(By locator) {
        WebElement element = waitForElementToBeVisible(locator);
        ((org.openqa.selenium.JavascriptExecutor) driver)
            .executeScript("arguments[0].scrollIntoView(true);", element);
    }
    
    /**
     * Wait for page to load completely
     */
    public void waitForPageToLoad() {
        wait.until(webDriver -> 
            ((org.openqa.selenium.JavascriptExecutor) webDriver)
                .executeScript("return document.readyState").equals("complete"));
    }
    
    /**
     * Add a delay for better test visibility
     */
    public void delay(int milliseconds) {
        try {
            Thread.sleep(milliseconds);
        } catch (InterruptedException e) {
            Thread.currentThread().interrupt();
        }
    }
    
    /**
     * Add a short delay (1 second) for better test visibility
     */
    public void shortDelay() {
        delay(1000);
    }
    
    /**
     * Add a medium delay (2 seconds) for better test visibility
     */
    public void mediumDelay() {
        delay(2000);
    }
    
    /**
     * Add a long delay (3 seconds) for better test visibility
     */
    public void longDelay() {
        delay(3000);
    }
    
    /**
     * Check if an element has HTML5 validation error
     */
    public boolean hasValidationError(By locator) {
        try {
            WebElement element = waitForElementToBeVisible(locator);
            String validity = (String) ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("return arguments[0].validity.valid;", element);
            return !Boolean.parseBoolean(validity);
        } catch (Exception e) {
            return false;
        }
    }
    
    /**
     * Get HTML5 validation message
     */
    public String getValidationMessage(By locator) {
        try {
            WebElement element = waitForElementToBeVisible(locator);
            return (String) ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("return arguments[0].validationMessage;", element);
        } catch (Exception e) {
            return null;
        }
    }
    
    /**
     * Check if form has any validation errors
     */
    public boolean hasFormValidationErrors() {
        try {
            return (Boolean) ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("return !document.querySelector('form').checkValidity();");
        } catch (Exception e) {
            return false;
        }
    }
    
    /**
     * Try to submit form and handle validation
     */
    public boolean trySubmitForm(By buttonLocator) {
        try {
            // First try to click the button
            WebElement button = waitForElementToBeClickable(buttonLocator);
            button.click();
            return true;
        } catch (Exception e) {
            System.out.println("Primary button click failed: " + e.getMessage());
            
            // Try alternative button locators
            By altButton1 = By.xpath("//button[@type='submit']");
            By altButton2 = By.xpath("//input[@type='submit']");
            
            try {
                if (isElementDisplayed(altButton1)) {
                    clickElement(altButton1);
                    return true;
                } else if (isElementDisplayed(altButton2)) {
                    clickElement(altButton2);
                    return true;
                }
            } catch (Exception e2) {
                System.out.println("Alternative button click failed: " + e2.getMessage());
            }
            
            // Try JavaScript form submission
            try {
                ((org.openqa.selenium.JavascriptExecutor) driver)
                    .executeScript("document.querySelector('form').submit();");
                return true;
            } catch (Exception e3) {
                System.out.println("JavaScript form submission failed: " + e3.getMessage());
                return false;
            }
        }
    }
    
    /**
     * Safe click method that handles click interception issues
     */
    public boolean safeClickElement(By locator) {
        try {
            WebElement element = waitForElementToBeClickable(locator);
            
            // Scroll element into view
            ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", element);
            
            // Wait a bit for scroll to complete
            delay(1000);
            
            // Try regular click first
            try {
                element.click();
                return true;
            } catch (Exception e) {
                System.out.println("Regular click failed, trying JavaScript click: " + e.getMessage());
                
                // Try JavaScript click
                ((org.openqa.selenium.JavascriptExecutor) driver)
                    .executeScript("arguments[0].click();", element);
                return true;
            }
        } catch (Exception e) {
            System.out.println("Safe click failed: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * Force click using JavaScript (bypasses click interception)
     */
    public boolean forceClickElement(By locator) {
        try {
            WebElement element = waitForElementToBeVisible(locator);
            
            // Scroll element into view
            ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", element);
            
            // Wait for scroll
            delay(1000);
            
            // Force click using JavaScript
            ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("arguments[0].click();", element);
            
            return true;
        } catch (Exception e) {
            System.out.println("Force click failed: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * Smart form submission that handles all click issues
     */
    public boolean smartFormSubmit(By buttonLocator) {
        System.out.println("🔘 Attempting smart form submission...");
        
        // Method 1: Try safe click
        System.out.println("   Method 1: Safe click...");
        if (safeClickElement(buttonLocator)) {
            System.out.println("   ✅ Safe click successful");
            return true;
        }
        
        // Method 2: Try force click
        System.out.println("   Method 2: Force click...");
        if (forceClickElement(buttonLocator)) {
            System.out.println("   ✅ Force click successful");
            return true;
        }
        
        // Method 3: Try alternative button locators
        System.out.println("   Method 3: Alternative button locators...");
        By[] altLocators = {
            By.xpath("//button[@type='submit']"),
            By.xpath("//input[@type='submit']"),
            By.xpath("//button[contains(text(), 'Register')]"),
            By.xpath("//button[contains(text(), 'Create')]"),
            By.xpath("//button[contains(text(), 'Submit')]"),
            By.xpath("//input[@value='Register']"),
            By.xpath("//input[@value='Create Account']")
        };
        
        for (By altLocator : altLocators) {
            try {
                if (isElementDisplayed(altLocator)) {
                    System.out.println("   Found alternative button: " + altLocator);
                    if (safeClickElement(altLocator)) {
                        System.out.println("   ✅ Alternative button click successful");
                        return true;
                    }
                }
            } catch (Exception e) {
                System.out.println("   Alternative button failed: " + e.getMessage());
            }
        }
        
        // Method 4: JavaScript form submission
        System.out.println("   Method 4: JavaScript form submission...");
        try {
            ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("document.querySelector('form').submit();");
            System.out.println("   ✅ JavaScript form submission successful");
            return true;
        } catch (Exception e) {
            System.out.println("   JavaScript form submission failed: " + e.getMessage());
        }
        
        // Method 5: Try clicking any submit button
        System.out.println("   Method 5: Any submit button...");
        try {
            ((org.openqa.selenium.JavascriptExecutor) driver)
                .executeScript("document.querySelector('button[type=\"submit\"], input[type=\"submit\"]').click();");
            System.out.println("   ✅ Any submit button click successful");
            return true;
        } catch (Exception e) {
            System.out.println("   Any submit button click failed: " + e.getMessage());
        }
        
        System.out.println("   ❌ All form submission methods failed");
        return false;
    }
    
    /**
     * Take screenshot
     */
    public void takeScreenshot(String fileName) {
        try {
            org.openqa.selenium.TakesScreenshot screenshot = 
                (org.openqa.selenium.TakesScreenshot) driver;
            java.io.File sourceFile = screenshot.getScreenshotAs(org.openqa.selenium.OutputType.FILE);
            java.io.File destinationFile = new java.io.File("./screenshots/" + fileName + ".png");
            org.apache.commons.io.FileUtils.copyFile(sourceFile, destinationFile);
        } catch (Exception e) {
            System.err.println("Failed to take screenshot: " + e.getMessage());
        }
    }
}
