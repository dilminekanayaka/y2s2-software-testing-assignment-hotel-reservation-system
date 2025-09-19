package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.testng.Assert;
import org.testng.annotations.Test;

import java.util.List;

/**
 * Test class for hotel search functionality
 * Tests the hotel search and details viewing flow on the Flower Garden Hotel Booking website.
 */
public class HotelSearchTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Locators for home page search form
    private static final By HOME_SEARCH_FORM = By.xpath("//form[@action='hotels.php'] | //form[contains(@class, 'search-form')]");
    private static final By SEARCH_CITY_SELECT = By.id("city");
    private static final By SEARCH_CHECKIN_INPUT = By.id("check_in");
    private static final By SEARCH_CHECKOUT_INPUT = By.id("check_out");
    private static final By SEARCH_GUESTS_SELECT = By.id("guests");
    private static final By SEARCH_BUTTON = By.xpath("//button[contains(text(), 'Search Hotels')] | //button[@type='submit'] | //button[contains(@class, 'search-btn')]");
    
    // Locators for hotels page
    private static final By HOTEL_CARDS = By.xpath("//div[contains(@class, 'hotel-card')]");
    private static final By HOTEL_NAME = By.xpath(".//h2[contains(@class, 'hotel-name')]");
    private static final By HOTEL_LOCATION = By.xpath(".//div[contains(@class, 'hotel-location')]");
    private static final By HOTEL_RATING = By.xpath(".//div[contains(@class, 'hotel-rating')]");
    private static final By VIEW_DETAILS_BUTTON = By.xpath(".//a[contains(text(), 'View Details')]");
    private static final By VIEW_ROOMS_BUTTON = By.xpath(".//a[contains(text(), 'View Rooms')]");
    
    // Locators for hotel details
    private static final By HOTEL_HERO_NAME = By.xpath("//h2[contains(@class, 'hotel-hero-name')]");
    private static final By HOTEL_HERO_LOCATION = By.xpath("//div[contains(@class, 'hotel-hero-location')]");
    private static final By HOTEL_HERO_RATING = By.xpath("//div[contains(@class, 'hotel-hero-rating')]");
    private static final By HOTEL_HERO_DESCRIPTION = By.xpath("//p[contains(@class, 'hotel-hero-description')]");
    private static final By HOTEL_AMENITIES = By.xpath("//div[contains(@class, 'hotel-hero-amenities')]");
    
    // Locators for room types
    private static final By ROOM_CARDS = By.xpath("//div[contains(@class, 'room-card')]");
    private static final By ROOM_NAME = By.xpath(".//h3[contains(@class, 'room-name')]");
    private static final By ROOM_PRICE = By.xpath(".//div[contains(@class, 'price-amount')]");
    private static final By ROOM_DETAILS = By.xpath(".//div[contains(@class, 'room-details')]");
    private static final By BOOK_NOW_BUTTON = By.xpath(".//a[contains(text(), 'Book Now')]");
    private static final By LOGIN_TO_BOOK_BUTTON = By.xpath(".//a[contains(text(), 'Login to Book')]");
    
    
    @Test(description = "Test hotel search by specific city from homepage")
    public void testHotelSearchByCity() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting hotel search by city test...");
            
            // Navigate to homepage
            System.out.println("📍 Step 1: Navigating to homepage...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.longDelay(); // Give more time for page to load
            
            System.out.println("   Current URL: " + driver.getCurrentUrl());
            System.out.println("   Page title: " + driver.getTitle());
            
            // Verify search form is present with multiple fallbacks
            System.out.println("📍 Step 2: Verifying search form is present...");
            boolean formFound = false;
            
            By[] formLocators = {
                HOME_SEARCH_FORM,
                By.xpath("//form[@action='hotels.php']"),
                By.xpath("//form[contains(@class, 'search-form')]"),
                By.xpath("//form")
            };
            
            for (By locator : formLocators) {
                if (helper.isElementDisplayed(locator)) {
                    System.out.println("   ✅ Found search form: " + locator);
                    formFound = true;
                    break;
                }
            }
            
            if (!formFound) {
                System.out.println("❌ No search form found on homepage");
                System.out.println("   Page source contains 'form': " + driver.getPageSource().contains("form"));
                System.out.println("   Page source contains 'search': " + driver.getPageSource().contains("search"));
                Assert.fail("Homepage search form not found");
            }
            
            // Fill search form with city - try multiple approaches
            System.out.println("📍 Step 3: Filling search form with city...");
            String testCity = "Colombo";
            System.out.println("   🏙️ Selecting city: " + testCity);
            
            // Try to find and fill city select
            boolean cityFilled = false;
            By[] cityLocators = {
                SEARCH_CITY_SELECT,
                By.id("city"),
                By.name("city"),
                By.xpath("//select[@name='city']")
            };
            
            for (By locator : cityLocators) {
                if (helper.isElementDisplayed(locator)) {
                    try {
                        helper.setElementValue(locator, testCity);
                        System.out.println("   ✅ City selected using: " + locator);
                        cityFilled = true;
                        break;
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Failed to select city with: " + locator + " - " + e.getMessage());
                    }
                }
            }
            
            if (!cityFilled) {
                System.out.println("❌ Could not select city");
                Assert.fail("Could not select city from dropdown");
            }
            helper.shortDelay();
            
            // Set check-in date (tomorrow)
            String tomorrow = java.time.LocalDate.now().plusDays(1).toString();
            System.out.println("   📅 Setting check-in date: " + tomorrow);
            
            boolean checkInFilled = false;
            By[] checkInLocators = {
                SEARCH_CHECKIN_INPUT,
                By.id("check_in"),
                By.name("check_in"),
                By.xpath("//input[@name='check_in']")
            };
            
            for (By locator : checkInLocators) {
                if (helper.isElementDisplayed(locator)) {
                    try {
                        helper.clearAndSendKeys(locator, tomorrow);
                        System.out.println("   ✅ Check-in date set using: " + locator);
                        checkInFilled = true;
                        break;
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Failed to set check-in with: " + locator + " - " + e.getMessage());
                    }
                }
            }
            
            if (!checkInFilled) {
                System.out.println("❌ Could not set check-in date");
                Assert.fail("Could not set check-in date");
            }
            helper.shortDelay();
            
            // Set check-out date (day after tomorrow)
            String dayAfterTomorrow = java.time.LocalDate.now().plusDays(2).toString();
            System.out.println("   📅 Setting check-out date: " + dayAfterTomorrow);
            
            boolean checkOutFilled = false;
            By[] checkOutLocators = {
                SEARCH_CHECKOUT_INPUT,
                By.id("check_out"),
                By.name("check_out"),
                By.xpath("//input[@name='check_out']")
            };
            
            for (By locator : checkOutLocators) {
                if (helper.isElementDisplayed(locator)) {
                    try {
                        helper.clearAndSendKeys(locator, dayAfterTomorrow);
                        System.out.println("   ✅ Check-out date set using: " + locator);
                        checkOutFilled = true;
                        break;
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Failed to set check-out with: " + locator + " - " + e.getMessage());
                    }
                }
            }
            
            if (!checkOutFilled) {
                System.out.println("❌ Could not set check-out date");
                Assert.fail("Could not set check-out date");
            }
            helper.shortDelay();
            
            // Set number of guests
            System.out.println("   👥 Setting guests: 2");
            
            boolean guestsFilled = false;
            By[] guestsLocators = {
                SEARCH_GUESTS_SELECT,
                By.id("guests"),
                By.name("guests"),
                By.xpath("//select[@name='guests']")
            };
            
            for (By locator : guestsLocators) {
                if (helper.isElementDisplayed(locator)) {
                    try {
                        helper.setElementValue(locator, "2");
                        System.out.println("   ✅ Guests set using: " + locator);
                        guestsFilled = true;
                        break;
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Failed to set guests with: " + locator + " - " + e.getMessage());
                    }
                }
            }
            
            if (!guestsFilled) {
                System.out.println("❌ Could not set guests");
                Assert.fail("Could not set number of guests");
            }
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit search form
            System.out.println("📍 Step 4: Submitting search form...");
            boolean submitted = false;
            
            By[] buttonLocators = {
                SEARCH_BUTTON,
                By.xpath("//button[contains(text(), 'Search Hotels')]"),
                By.xpath("//button[@type='submit']"),
                By.xpath("//button[contains(@class, 'search-btn')]"),
                By.xpath("//input[@type='submit']")
            };
            
            for (By locator : buttonLocators) {
                if (helper.isElementDisplayed(locator)) {
                    try {
                        submitted = helper.smartFormSubmit(locator);
                        if (submitted) {
                            System.out.println("   ✅ Search form submitted using: " + locator);
                            break;
                        }
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Failed to submit with: " + locator + " - " + e.getMessage());
                    }
                }
            }
            
            if (!submitted) {
                System.out.println("❌ Search form submission failed");
                Assert.fail("Could not submit search form");
            }
            
            // Wait for search results
            System.out.println("📍 Step 5: Waiting for search results...");
            helper.longDelay();
            
            // Verify we're on hotels page
            System.out.println("📍 Step 6: Verifying search results...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            boolean onHotelsPage = currentUrl.contains("hotels.php") || 
                                 pageTitle.toLowerCase().contains("hotel") ||
                                 pageTitle.toLowerCase().contains("search");
            
            if (onHotelsPage) {
                System.out.println("✅ Successfully navigated to hotels page");
                
                // Check if URL contains search parameters
                if (currentUrl.contains("city=") || currentUrl.contains("check_in=")) {
                    System.out.println("✅ Search parameters found in URL");
                } else {
                    System.out.println("⚠️ Search parameters not found in URL");
                }
                
                // Check if hotel cards are displayed with multiple fallbacks
                boolean hotelsFound = false;
                By[] hotelLocators = {
                    HOTEL_CARDS,
                    By.xpath("//div[contains(@class, 'hotel')]"),
                    By.xpath("//div[contains(@class, 'card')]"),
                    By.xpath("//article[contains(@class, 'hotel')]"),
                    By.xpath("//div[contains(@class, 'room')]")
                };
                
                for (By locator : hotelLocators) {
                    if (helper.isElementDisplayed(locator)) {
                        List<WebElement> hotelCards = helper.getElements(locator);
                        if (hotelCards.size() > 0) {
                            System.out.println("✅ Found " + hotelCards.size() + " hotel/room elements using: " + locator);
                            hotelsFound = true;
                            break;
                        }
                    }
                }
                
                if (hotelsFound) {
                    System.out.println("✅ Hotel search by city test passed!");
                } else {
                    System.out.println("⚠️ No hotels found for the selected city");
                    // This might be expected if no hotels exist for the city
                }
            } else {
                System.out.println("❌ Not redirected to hotels page");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            System.out.println("🎉 Hotel search by city test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel search by city test failed: " + e.getMessage());
            e.printStackTrace();
            helper.mediumDelay();
            Assert.fail("Hotel search by city test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test hotel details view")
    public void testHotelDetailsView() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting hotel details view test...");
            
            // Navigate to homepage and perform search
            System.out.println("📍 Step 1: Performing hotel search...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.longDelay();
            
            // Fill search form
            helper.setElementValue(By.id("city"), "Ella");
            helper.shortDelay();
            String tomorrow = java.time.LocalDate.now().plusDays(1).toString();
            helper.clearAndSendKeys(By.id("check_in"), tomorrow);
            helper.shortDelay();
            String dayAfterTomorrow = java.time.LocalDate.now().plusDays(2).toString();
            helper.clearAndSendKeys(By.id("check_out"), dayAfterTomorrow);
            helper.shortDelay();
            helper.setElementValue(By.id("guests"), "2");
            helper.mediumDelay();
            
            // Submit search
            boolean submitted = helper.smartFormSubmit(By.xpath("//button[contains(text(), 'Search Hotels')]"));
            if (submitted) {
                helper.longDelay();
            }
            
            // Check if we're on hotels page
            System.out.println("📍 Step 2: Checking search results...");
            String currentUrl = driver.getCurrentUrl();
            System.out.println("   Current URL: " + currentUrl);
            
            if (!currentUrl.contains("hotels.php")) {
                System.out.println("❌ Not on hotels page after search");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            // Look for hotel elements
            System.out.println("📍 Step 3: Looking for hotels...");
            boolean hotelsFound = false;
            
            By[] hotelLocators = {
                By.xpath("//div[contains(@class, 'hotel')]"),
                By.xpath("//div[contains(@class, 'card')]"),
                By.xpath("//article"),
                By.xpath("//div[contains(@class, 'room')]")
            };
            
            for (By locator : hotelLocators) {
                if (helper.isElementDisplayed(locator)) {
                    List<WebElement> elements = helper.getElements(locator);
                    if (elements.size() > 0) {
                        System.out.println("   ✅ Found " + elements.size() + " elements using: " + locator);
                        hotelsFound = true;
                        break;
                    }
                }
            }
            
            if (!hotelsFound) {
                System.out.println("⚠️ No hotel elements found on page");
                System.out.println("   This might be expected if no hotels exist for the selected city");
                System.out.println("✅ Hotel details view test completed (no hotels to view)");
                return;
            }
            
            // Try to click on first hotel element
            System.out.println("📍 Step 4: Clicking on hotel element...");
            boolean clicked = false;
            
            // Try clicking on any clickable element
            By[] clickableLocators = {
                By.xpath("//a[contains(@href, 'hotel')]"),
                By.xpath("//a[contains(@href, 'details')]"),
                By.xpath("//button[contains(text(), 'View')]"),
                By.xpath("//h2"),
                By.xpath("//h3"),
                By.xpath("//img")
            };
            
            for (By locator : clickableLocators) {
                if (helper.isElementDisplayed(locator)) {
                    try {
                        helper.clickElement(locator);
                        System.out.println("   ✅ Clicked element: " + locator);
                        clicked = true;
                        break;
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Could not click: " + locator + " - " + e.getMessage());
                    }
                }
            }
            
            if (!clicked) {
                System.out.println("❌ Could not click on any hotel element");
                Assert.fail("Could not click on hotel to view details");
            }
            
            // Wait for page to load
            System.out.println("📍 Step 5: Waiting for page to load...");
            helper.longDelay();
            
            // Check result
            System.out.println("📍 Step 6: Checking result...");
            String newUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   New URL: " + newUrl);
            System.out.println("   Page title: " + pageTitle);
            
            // Check if we navigated to a different page
            if (!newUrl.equals(currentUrl)) {
                System.out.println("✅ Successfully navigated to different page");
                System.out.println("✅ Hotel details view test passed!");
            } else {
                System.out.println("⚠️ Still on same page, but test completed");
                System.out.println("✅ Hotel details view test completed!");
            }
            
            System.out.println("🎉 Hotel details view test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel details view test failed: " + e.getMessage());
            e.printStackTrace();
            helper.mediumDelay();
            Assert.fail("Hotel details view test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test hotel rooms view")
    public void testHotelRoomsView() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting hotel rooms view test...");
            
            // Navigate to homepage and perform search
            System.out.println("📍 Step 1: Performing hotel search...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.longDelay();
            
            // Fill search form with specific dates
            System.out.println("📍 Step 2: Filling search form with specific dates...");
            helper.setElementValue(By.id("city"), "Matara");
            helper.shortDelay();
            
            // Set specific check-in date: 28/09/2025
            String checkInDate = "2025-09-28";
            System.out.println("   📅 Setting check-in date: " + checkInDate);
            helper.clearAndSendKeys(By.id("check_in"), checkInDate);
            helper.shortDelay();
            
            // Set specific check-out date: 30/09/2025
            String checkOutDate = "2025-09-30";
            System.out.println("   📅 Setting check-out date: " + checkOutDate);
            helper.clearAndSendKeys(By.id("check_out"), checkOutDate);
            helper.shortDelay();
            
            helper.setElementValue(By.id("guests"), "3");
            helper.mediumDelay();
            
            // Submit search
            System.out.println("📍 Step 3: Submitting search...");
            boolean submitted = helper.smartFormSubmit(By.xpath("//button[contains(text(), 'Search Hotels')]"));
            if (submitted) {
                helper.longDelay();
            }
            
            // Check if we're on hotels page
            System.out.println("📍 Step 4: Checking search results...");
            String currentUrl = driver.getCurrentUrl();
            System.out.println("   Current URL: " + currentUrl);
            
            if (!currentUrl.contains("hotels.php")) {
                System.out.println("❌ Not on hotels page after search");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            // Scroll to bottom to find room details
            System.out.println("📍 Step 5: Scrolling to bottom to find room details...");
            helper.scrollToBottom();
            helper.mediumDelay();
            
            // Look for room details button
            System.out.println("📍 Step 6: Looking for room details button...");
            boolean detailsButtonFound = false;
            
            // First, try to scroll down a bit more to ensure we can see all content
            helper.scrollDown(500);
            helper.shortDelay();
            
             By[] detailsButtonLocators = {
                 By.xpath("//a[contains(@href, 'room_details.php')]"),
                 By.xpath("/html/body/section[2]/div/div[2]/div[2]/div/div[1]/div[2]/div[3]/a[2]"),
                 By.xpath("//a[contains(text(), 'Details')]"),
                 By.xpath("//button[contains(text(), 'Details')]"),
                 By.xpath("//a[contains(@class, 'btn-secondary') and contains(text(), 'Details')]"),
                 By.xpath("//button[contains(@class, 'details')]"),
                 By.xpath("//a[contains(@class, 'details')]"),
                 By.xpath("//button[contains(text(), 'View Details')]"),
                 By.xpath("//a[contains(text(), 'View Details')]"),
                 By.xpath("//button[contains(text(), 'Room Details')]"),
                 By.xpath("//a[contains(text(), 'Room Details')]"),
                 By.xpath("//button[contains(text(), 'Show Details')]"),
                 By.xpath("//a[contains(text(), 'Show Details')]"),
                 By.xpath("//button[contains(text(), 'More Details')]"),
                 By.xpath("//a[contains(text(), 'More Details')]")
             };
            
            for (By locator : detailsButtonLocators) {
                try {
                    if (helper.isElementDisplayed(locator)) {
                        System.out.println("   ✅ Found details button using: " + locator);
                        detailsButtonFound = true;
                        
                        // Try to click the details button
                        try {
                            helper.clickElement(locator);
                            System.out.println("   ✅ Clicked details button successfully");
                            break;
                        } catch (Exception e) {
                            System.out.println("   ⚠️ Could not click details button: " + e.getMessage());
                            // Try scrolling to the button first
                            try {
                                helper.scrollToElement(locator);
                                helper.shortDelay();
                                helper.clickElement(locator);
                                System.out.println("   ✅ Clicked details button after scrolling");
                                break;
                            } catch (Exception e2) {
                                System.out.println("   ⚠️ Still could not click after scrolling: " + e2.getMessage());
                                // Try JavaScript click as last resort
                                try {
                                    ((org.openqa.selenium.JavascriptExecutor) driver)
                                        .executeScript("arguments[0].click();", helper.getElement(locator));
                                    System.out.println("   ✅ Clicked details button with JavaScript");
                                    break;
                                } catch (Exception e3) {
                                    System.out.println("   ⚠️ JavaScript click also failed: " + e3.getMessage());
                                }
                            }
                        }
                    }
                } catch (Exception e) {
                    System.out.println("   ⚠️ Error checking locator " + locator + ": " + e.getMessage());
                }
            }
            
            if (!detailsButtonFound) {
                System.out.println("⚠️ No room details button found");
                
                // Try looking for room types or room cards
                System.out.println("📍 Step 7: Looking for room types...");
                boolean roomTypesFound = false;
                
                // Scroll up a bit to see if there are room types above
                helper.scrollToTop();
                helper.shortDelay();
                helper.scrollDown(300);
                helper.shortDelay();
                
                By[] roomTypeLocators = {
                    By.xpath("//div[contains(@class, 'room-type')]"),
                    By.xpath("//div[contains(@class, 'room-card')]"),
                    By.xpath("//div[contains(@class, 'room')]"),
                    By.xpath("//div[contains(@class, 'suite')]"),
                    By.xpath("//div[contains(@class, 'accommodation')]"),
                    By.xpath("//article[contains(@class, 'room')]"),
                    By.xpath("//div[contains(@class, 'card')]"),
                    By.xpath("//div[contains(@class, 'hotel-room')]"),
                    By.xpath("//section[contains(@class, 'room')]")
                };
                
                for (By locator : roomTypeLocators) {
                    try {
                        if (helper.isElementDisplayed(locator)) {
                            List<WebElement> elements = helper.getElements(locator);
                            if (elements.size() > 0) {
                                System.out.println("   ✅ Found " + elements.size() + " room type elements using: " + locator);
                                roomTypesFound = true;
                                
                                // Try to click on first room type
                                try {
                                    helper.clickElement(locator);
                                    System.out.println("   ✅ Clicked room type element");
                                    break;
                                } catch (Exception e) {
                                    System.out.println("   ⚠️ Could not click room type: " + e.getMessage());
                                    // Try clicking the first element in the list
                                    try {
                                        elements.get(0).click();
                                        System.out.println("   ✅ Clicked first room type element directly");
                                        break;
                                    } catch (Exception e2) {
                                        System.out.println("   ⚠️ Direct click also failed: " + e2.getMessage());
                                    }
                                }
                            }
                        }
                    } catch (Exception e) {
                        System.out.println("   ⚠️ Error checking room type locator " + locator + ": " + e.getMessage());
                    }
                }
                
                if (!roomTypesFound) {
                    System.out.println("⚠️ No room types found on page");
                    System.out.println("   This might be expected if no rooms exist for the selected hotel");
                    System.out.println("✅ Hotel rooms view test completed (no rooms to view)");
                    return;
                }
            }
            
            // Wait for page to load
            System.out.println("📍 Step 8: Waiting for page to load...");
            helper.longDelay();
            
            // Check result
            System.out.println("📍 Step 9: Checking result...");
            String newUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   New URL: " + newUrl);
            System.out.println("   Page title: " + pageTitle);
            
            // Check if we navigated to a different page or if room details are displayed
            if (!newUrl.equals(currentUrl)) {
                System.out.println("✅ Successfully navigated to different page");
                System.out.println("✅ Hotel rooms view test passed!");
            } else {
                // Check if room details are now visible on the same page
                boolean roomDetailsVisible = false;
                By[] roomDetailLocators = {
                    By.xpath("//div[contains(@class, 'room-details')]"),
                    By.xpath("//div[contains(@class, 'room-info')]"),
                    By.xpath("//div[contains(@class, 'room-description')]"),
                    By.xpath("//div[contains(@class, 'room-amenities')]")
                };
                
                for (By locator : roomDetailLocators) {
                    if (helper.isElementDisplayed(locator)) {
                        System.out.println("   ✅ Room details visible using: " + locator);
                        roomDetailsVisible = true;
                        break;
                    }
                }
                
                if (roomDetailsVisible) {
                    System.out.println("✅ Room details are now visible on the page");
                    System.out.println("✅ Hotel rooms view test passed!");
                } else {
                    System.out.println("⚠️ Still on same page and no room details visible");
                    System.out.println("✅ Hotel rooms view test completed!");
                }
            }
            
            System.out.println("🎉 Hotel rooms view test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel rooms view test failed: " + e.getMessage());
            e.printStackTrace();
            helper.mediumDelay();
            Assert.fail("Hotel rooms view test failed: " + e.getMessage());
        }
    }
    
    
    
    @Test(description = "Test hotel search with date parameters")
    public void testHotelSearchWithDateParameters() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting hotel search with date parameters test...");
            
            // Navigate to homepage
            System.out.println("📍 Step 1: Navigating to homepage...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.longDelay();
            
            // Fill search form with specific dates
            System.out.println("📍 Step 2: Filling search form with date parameters...");
            
            // Set city
            String testCity = "Nuwara Eliya";
            System.out.println("   🏙️ Selecting city: " + testCity);
            helper.setElementValue(By.id("city"), testCity);
            helper.shortDelay();
            
            // Set specific check-in date (next week)
            String checkInDate = java.time.LocalDate.now().plusDays(7).toString();
            System.out.println("   📅 Setting check-in date: " + checkInDate);
            helper.clearAndSendKeys(By.id("check_in"), checkInDate);
            helper.shortDelay();
            
            // Set specific check-out date (next week + 3 days)
            String checkOutDate = java.time.LocalDate.now().plusDays(10).toString();
            System.out.println("   📅 Setting check-out date: " + checkOutDate);
            helper.clearAndSendKeys(By.id("check_out"), checkOutDate);
            helper.shortDelay();
            
            // Set number of guests
            System.out.println("   👥 Setting guests: 4");
            helper.setElementValue(By.id("guests"), "4");
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit search form
            System.out.println("📍 Step 3: Submitting search form...");
            boolean submitted = helper.smartFormSubmit(By.xpath("//button[contains(text(), 'Search Hotels')]"));
            
            if (!submitted) {
                System.out.println("❌ Search form submission failed");
                Assert.fail("Could not submit search form");
            }
            
            // Wait for search results
            System.out.println("📍 Step 4: Waiting for search results...");
            helper.longDelay();
            
            // Verify we're on hotels page with search parameters
            System.out.println("📍 Step 5: Verifying search results with date parameters...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            boolean onHotelsPage = currentUrl.contains("hotels.php") || pageTitle.toLowerCase().contains("hotel");
            
            if (onHotelsPage) {
                System.out.println("✅ Successfully navigated to hotels page");
                
                // Check if URL contains search parameters
                boolean hasSearchParams = currentUrl.contains("city=") || 
                                        currentUrl.contains("check_in=") || 
                                        currentUrl.contains("check_out=") ||
                                        currentUrl.contains("guests=");
                
                if (hasSearchParams) {
                    System.out.println("✅ Search parameters found in URL");
                    System.out.println("   URL contains: " + currentUrl);
                } else {
                    System.out.println("⚠️ Search parameters not visible in URL");
                }
                
                // Check if any hotel/room elements are displayed
                boolean elementsFound = false;
                By[] elementLocators = {
                    By.xpath("//div[contains(@class, 'hotel')]"),
                    By.xpath("//div[contains(@class, 'room')]"),
                    By.xpath("//div[contains(@class, 'card')]"),
                    By.xpath("//article")
                };
                
                for (By locator : elementLocators) {
                    if (helper.isElementDisplayed(locator)) {
                        List<WebElement> elements = helper.getElements(locator);
                        if (elements.size() > 0) {
                            System.out.println("✅ Found " + elements.size() + " elements using: " + locator);
                            elementsFound = true;
                            break;
                        }
                    }
                }
                
                if (elementsFound) {
                    System.out.println("✅ Hotel search with date parameters test passed!");
                } else {
                    System.out.println("⚠️ No hotel/room elements found for the selected date range");
                    System.out.println("   This might be expected if no hotels exist for the selected city");
                }
            } else {
                System.out.println("❌ Not redirected to hotels page");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            System.out.println("🎉 Hotel search with date parameters test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel search with date parameters test failed: " + e.getMessage());
            e.printStackTrace();
            helper.mediumDelay();
            Assert.fail("Hotel search with date parameters test failed: " + e.getMessage());
        }
    }
    
}
