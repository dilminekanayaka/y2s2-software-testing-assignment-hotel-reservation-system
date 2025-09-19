package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

/**
 * Simple test to verify hotel search form works
 */
public class SimpleHotelSearchTest extends TestBase {
    
    private PageObjectHelper helper;
    
    @Test(description = "Simple hotel search test")
    public void testSimpleHotelSearch() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting simple hotel search test...");
            
            // Navigate to homepage
            System.out.println("📍 Step 1: Navigating to homepage...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.longDelay();
            
            System.out.println("   Current URL: " + driver.getCurrentUrl());
            System.out.println("   Page title: " + driver.getTitle());
            
            // Fill search form
            System.out.println("📍 Step 2: Filling search form...");
            
            // Set city
            System.out.println("   🏙️ Selecting city: Colombo");
            helper.setElementValue(By.id("city"), "Colombo");
            helper.shortDelay();
            
            // Set check-in date
            String tomorrow = java.time.LocalDate.now().plusDays(1).toString();
            System.out.println("   📅 Setting check-in date: " + tomorrow);
            helper.clearAndSendKeys(By.id("check_in"), tomorrow);
            helper.shortDelay();
            
            // Set check-out date
            String dayAfterTomorrow = java.time.LocalDate.now().plusDays(2).toString();
            System.out.println("   📅 Setting check-out date: " + dayAfterTomorrow);
            helper.clearAndSendKeys(By.id("check_out"), dayAfterTomorrow);
            helper.shortDelay();
            
            // Set guests
            System.out.println("   👥 Setting guests: 2");
            helper.setElementValue(By.id("guests"), "2");
            helper.mediumDelay();
            
            // Submit search
            System.out.println("📍 Step 3: Submitting search...");
            boolean submitted = helper.smartFormSubmit(By.xpath("//button[contains(text(), 'Search Hotels')]"));
            
            if (!submitted) {
                System.out.println("❌ Search submission failed");
                Assert.fail("Could not submit search form");
            }
            
            // Wait for results
            System.out.println("📍 Step 4: Waiting for results...");
            helper.longDelay();
            
            // Check result
            System.out.println("📍 Step 5: Checking result...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            if (currentUrl.contains("hotels.php")) {
                System.out.println("✅ Successfully navigated to hotels page!");
                
                if (currentUrl.contains("city=") && currentUrl.contains("check_in=")) {
                    System.out.println("✅ Search parameters found in URL!");
                    System.out.println("✅ Simple hotel search test passed!");
                } else {
                    System.out.println("⚠️ Search parameters not found in URL");
                }
            } else {
                System.out.println("❌ Not redirected to hotels page");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            System.out.println("🎉 Simple hotel search test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Simple hotel search test failed: " + e.getMessage());
            e.printStackTrace();
            Assert.fail("Simple hotel search test failed: " + e.getMessage());
        }
    }
}
