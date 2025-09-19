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
    private static final By HOME_SEARCH_FORM = By.xpath("//form[@action='hotels.php']");
    private static final By SEARCH_CITY_SELECT = By.id("city");
    private static final By SEARCH_CHECKIN_INPUT = By.id("check_in");
    private static final By SEARCH_CHECKOUT_INPUT = By.id("check_out");
    private static final By SEARCH_GUESTS_SELECT = By.id("guests");
    private static final By SEARCH_BUTTON = By.xpath("//button[contains(text(), 'Search Hotels')] | //button[@type='submit']");
    
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
            helper.mediumDelay();
            
            // Verify search form is present
            System.out.println("📍 Step 2: Verifying search form is present...");
            if (!helper.isElementDisplayed(HOME_SEARCH_FORM)) {
                Assert.fail("Homepage search form not found");
            }
            helper.shortDelay();
            
            // Fill search form with city
            System.out.println("📍 Step 3: Filling search form with city...");
            String testCity = "Colombo";
            System.out.println("   🏙️ Selecting city: " + testCity);
            
            helper.clearAndSendKeys(SEARCH_CITY_SELECT, testCity);
            helper.shortDelay();
            
            // Set check-in date (tomorrow)
            String tomorrow = java.time.LocalDate.now().plusDays(1).toString();
            System.out.println("   📅 Setting check-in date: " + tomorrow);
            helper.clearAndSendKeys(SEARCH_CHECKIN_INPUT, tomorrow);
            helper.shortDelay();
            
            // Set check-out date (day after tomorrow)
            String dayAfterTomorrow = java.time.LocalDate.now().plusDays(2).toString();
            System.out.println("   📅 Setting check-out date: " + dayAfterTomorrow);
            helper.clearAndSendKeys(SEARCH_CHECKOUT_INPUT, dayAfterTomorrow);
            helper.shortDelay();
            
            // Set number of guests
            System.out.println("   👥 Setting guests: 2");
            helper.clearAndSendKeys(SEARCH_GUESTS_SELECT, "2");
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit search form
            System.out.println("📍 Step 4: Submitting search form...");
            boolean submitted = helper.smartFormSubmit(SEARCH_BUTTON);
            
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
            
            boolean onHotelsPage = currentUrl.contains("hotels.php") || pageTitle.toLowerCase().contains("hotel");
            
            if (onHotelsPage) {
                System.out.println("✅ Successfully navigated to hotels page");
                
                // Check if hotel cards are displayed
                if (helper.isElementDisplayed(HOTEL_CARDS)) {
                    List<WebElement> hotelCards = helper.getElements(HOTEL_CARDS);
                    System.out.println("✅ Found " + hotelCards.size() + " hotel cards");
                    
                    if (hotelCards.size() > 0) {
                        System.out.println("✅ Hotel search by city test passed!");
                    } else {
                        System.out.println("⚠️ No hotels found for the selected city");
                    }
                } else {
                    System.out.println("⚠️ No hotel cards found on results page");
                }
            } else {
                System.out.println("❌ Not redirected to hotels page");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            System.out.println("🎉 Hotel search by city test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel search by city test failed: " + e.getMessage());
            helper.mediumDelay();
            Assert.fail("Hotel search by city test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test hotel details view")
    public void testHotelDetailsView() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting hotel details view test...");
            
            // First, perform a search to get to hotels page
            System.out.println("📍 Step 1: Performing hotel search...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.mediumDelay();
            
            // Fill and submit search form
            helper.clearAndSendKeys(SEARCH_CITY_SELECT, "Colombo");
            helper.shortDelay();
            String tomorrow = java.time.LocalDate.now().plusDays(1).toString();
            helper.clearAndSendKeys(SEARCH_CHECKIN_INPUT, tomorrow);
            helper.shortDelay();
            String dayAfterTomorrow = java.time.LocalDate.now().plusDays(2).toString();
            helper.clearAndSendKeys(SEARCH_CHECKOUT_INPUT, dayAfterTomorrow);
            helper.shortDelay();
            helper.clearAndSendKeys(SEARCH_GUESTS_SELECT, "2");
            helper.mediumDelay();
            
            boolean submitted = helper.smartFormSubmit(SEARCH_BUTTON);
            if (submitted) {
                helper.longDelay(); // Wait for search results
            }
            
            // Navigate to hotels page if not already there
            System.out.println("📍 Step 2: Navigating to hotels page...");
            if (!driver.getCurrentUrl().contains("hotels.php")) {
                helper.clickElement(By.xpath("//a[@href='hotels.php'] | //a[contains(text(), 'hotels')]"));
                helper.waitForTitleToContain("Hotels");
                helper.mediumDelay();
            }
            
            // Look for hotel cards or hotel links
            System.out.println("📍 Step 3: Looking for hotels to view details...");
            
            // Try multiple ways to find hotels
            By[] hotelLocators = {
                HOTEL_CARDS,
                By.xpath("//div[contains(@class, 'hotel')]"),
                By.xpath("//a[contains(@href, 'hotel')]"),
                By.xpath("//div[contains(@class, 'card')]"),
                By.xpath("//article[contains(@class, 'hotel')]")
            };
            
            List<WebElement> hotelElements = null;
            for (By locator : hotelLocators) {
                if (helper.isElementDisplayed(locator)) {
                    hotelElements = helper.getElements(locator);
                    if (hotelElements.size() > 0) {
                        System.out.println("   ✅ Found " + hotelElements.size() + " hotel elements");
                        break;
                    }
                }
            }
            
            if (hotelElements == null || hotelElements.size() == 0) {
                System.out.println("⚠️ No hotels found on the page");
                System.out.println("   Current URL: " + driver.getCurrentUrl());
                System.out.println("   Page title: " + driver.getTitle());
                Assert.fail("No hotels found to view details");
            }
            
            // Try to click on first hotel or hotel link
            System.out.println("📍 Step 4: Clicking on first hotel...");
            WebElement firstHotel = hotelElements.get(0);
            
            // Try multiple ways to click on hotel
            boolean clicked = false;
            
            // Method 1: Look for "View Details" button
            try {
                WebElement viewDetailsBtn = firstHotel.findElement(VIEW_DETAILS_BUTTON);
                helper.clickElement(VIEW_DETAILS_BUTTON);
                clicked = true;
                System.out.println("   ✅ Clicked 'View Details' button");
            } catch (Exception e) {
                System.out.println("   'View Details' button not found: " + e.getMessage());
            }
            
            // Method 2: Click on hotel card itself
            if (!clicked) {
                try {
                    helper.clickElement(By.xpath("//a[contains(@href, 'hotel')] | //a[contains(@href, 'details')]"));
                    clicked = true;
                    System.out.println("   ✅ Clicked hotel link");
                } catch (Exception e) {
                    System.out.println("   Hotel link not found: " + e.getMessage());
                }
            }
            
            // Method 3: Click on hotel name or image
            if (!clicked) {
                try {
                    helper.clickElement(By.xpath("//h2 | //h3 | //img"));
                    clicked = true;
                    System.out.println("   ✅ Clicked hotel name/image");
                } catch (Exception e) {
                    System.out.println("   Hotel name/image not clickable: " + e.getMessage());
                }
            }
            
            if (!clicked) {
                System.out.println("❌ Could not click on any hotel element");
                Assert.fail("Could not click on hotel to view details");
            }
            
            // Wait for hotel details page to load
            System.out.println("📍 Step 5: Waiting for hotel details page...");
            helper.longDelay();
            
            // Verify we're on a hotel details page
            System.out.println("📍 Step 6: Verifying hotel details page...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            boolean onDetailsPage = currentUrl.contains("hotel") || 
                                  currentUrl.contains("details") ||
                                  pageTitle.toLowerCase().contains("hotel") ||
                                  pageTitle.toLowerCase().contains("details");
            
            if (onDetailsPage) {
                System.out.println("✅ Successfully navigated to hotel details page");
                
                // Check for hotel details elements
                boolean hasDetails = false;
                
                if (helper.isElementDisplayed(HOTEL_HERO_NAME)) {
                    System.out.println("   ✅ Hotel name displayed");
                    hasDetails = true;
                }
                
                if (helper.isElementDisplayed(HOTEL_HERO_LOCATION)) {
                    System.out.println("   ✅ Hotel location displayed");
                    hasDetails = true;
                }
                
                if (helper.isElementDisplayed(HOTEL_HERO_DESCRIPTION)) {
                    System.out.println("   ✅ Hotel description displayed");
                    hasDetails = true;
                }
                
                if (hasDetails) {
                    System.out.println("✅ Hotel details view test passed!");
                } else {
                    System.out.println("⚠️ Hotel details page loaded but no specific details found");
                }
            } else {
                System.out.println("❌ Not on hotel details page");
                Assert.fail("Did not navigate to hotel details page");
            }
            
            System.out.println("🎉 Hotel details view test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel details view test failed: " + e.getMessage());
            helper.mediumDelay();
            Assert.fail("Hotel details view test failed: " + e.getMessage());
        }
    }
    
    @Test(description = "Test hotel rooms view")
    public void testHotelRoomsView() {
        helper = new PageObjectHelper(driver);
        
        try {
            System.out.println("🚀 Starting hotel rooms view test...");
            
            // First, perform a search to get to hotels page
            System.out.println("📍 Step 1: Performing hotel search...");
            driver.get(getProperty("base.url", "http://localhost/SereneTripsLK"));
            helper.waitForTitleToContain("Home");
            helper.mediumDelay();
            
            // Fill and submit search form
            helper.clearAndSendKeys(SEARCH_CITY_SELECT, "Colombo");
            helper.shortDelay();
            String tomorrow = java.time.LocalDate.now().plusDays(1).toString();
            helper.clearAndSendKeys(SEARCH_CHECKIN_INPUT, tomorrow);
            helper.shortDelay();
            String dayAfterTomorrow = java.time.LocalDate.now().plusDays(2).toString();
            helper.clearAndSendKeys(SEARCH_CHECKOUT_INPUT, dayAfterTomorrow);
            helper.shortDelay();
            helper.clearAndSendKeys(SEARCH_GUESTS_SELECT, "2");
            helper.mediumDelay();
            
            boolean submitted = helper.smartFormSubmit(SEARCH_BUTTON);
            if (submitted) {
                helper.longDelay(); // Wait for search results
            }
            
            // Navigate to hotels page if not already there
            System.out.println("📍 Step 2: Navigating to hotels page...");
            if (!driver.getCurrentUrl().contains("hotels.php")) {
                helper.clickElement(By.xpath("//a[@href='hotels.php'] | //a[contains(text(), 'hotels')]"));
                helper.waitForTitleToContain("Hotels");
                helper.mediumDelay();
            }
            
            // Look for hotel cards or hotel links
            System.out.println("📍 Step 3: Looking for hotels to view rooms...");
            
            // Try multiple ways to find hotels
            By[] hotelLocators = {
                HOTEL_CARDS,
                By.xpath("//div[contains(@class, 'hotel')]"),
                By.xpath("//a[contains(@href, 'hotel')]"),
                By.xpath("//div[contains(@class, 'card')]"),
                By.xpath("//article[contains(@class, 'hotel')]")
            };
            
            List<WebElement> hotelElements = null;
            for (By locator : hotelLocators) {
                if (helper.isElementDisplayed(locator)) {
                    hotelElements = helper.getElements(locator);
                    if (hotelElements.size() > 0) {
                        System.out.println("   ✅ Found " + hotelElements.size() + " hotel elements");
                        break;
                    }
                }
            }
            
            if (hotelElements == null || hotelElements.size() == 0) {
                System.out.println("⚠️ No hotels found on the page");
                System.out.println("   Current URL: " + driver.getCurrentUrl());
                System.out.println("   Page title: " + driver.getTitle());
                Assert.fail("No hotels found to view rooms");
            }
            
            // Try to click on "View Rooms" button or hotel link
            System.out.println("📍 Step 4: Clicking to view hotel rooms...");
            boolean clicked = false;
            
            // Method 1: Look for "View Rooms" button
            try {
                helper.clickElement(VIEW_ROOMS_BUTTON);
                clicked = true;
                System.out.println("   ✅ Clicked 'View Rooms' button");
            } catch (Exception e) {
                System.out.println("   'View Rooms' button not found: " + e.getMessage());
            }
            
            // Method 2: Click on hotel link that might lead to rooms
            if (!clicked) {
                try {
                    helper.clickElement(By.xpath("//a[contains(@href, 'room')] | //a[contains(@href, 'booking')]"));
                    clicked = true;
                    System.out.println("   ✅ Clicked room/booking link");
                } catch (Exception e) {
                    System.out.println("   Room/booking link not found: " + e.getMessage());
                }
            }
            
            // Method 3: Click on hotel card itself
            if (!clicked) {
                try {
                    helper.clickElement(By.xpath("//a[contains(@href, 'hotel')] | //div[contains(@class, 'hotel')]"));
                    clicked = true;
                    System.out.println("   ✅ Clicked hotel card");
                } catch (Exception e) {
                    System.out.println("   Hotel card not clickable: " + e.getMessage());
                }
            }
            
            if (!clicked) {
                System.out.println("❌ Could not click on any hotel element");
                Assert.fail("Could not click on hotel to view rooms");
            }
            
            // Wait for rooms page to load
            System.out.println("📍 Step 5: Waiting for rooms page...");
            helper.longDelay();
            
            // Verify we're on a rooms page
            System.out.println("📍 Step 6: Verifying rooms page...");
            String currentUrl = driver.getCurrentUrl();
            String pageTitle = driver.getTitle();
            
            System.out.println("   Current URL: " + currentUrl);
            System.out.println("   Page title: " + pageTitle);
            
            boolean onRoomsPage = currentUrl.contains("room") || 
                                currentUrl.contains("booking") ||
                                pageTitle.toLowerCase().contains("room") ||
                                pageTitle.toLowerCase().contains("booking");
            
            if (onRoomsPage) {
                System.out.println("✅ Successfully navigated to rooms page");
                
                // Check for room elements
                boolean hasRooms = false;
                
                if (helper.isElementDisplayed(ROOM_CARDS)) {
                    List<WebElement> roomCards = helper.getElements(ROOM_CARDS);
                    System.out.println("   ✅ Found " + roomCards.size() + " room cards");
                    hasRooms = true;
                    
                    if (roomCards.size() > 0) {
                        System.out.println("✅ Hotel rooms view test passed!");
                    } else {
                        System.out.println("⚠️ No rooms found for the selected hotel");
                    }
                } else {
                    // Try alternative room locators
                    By[] roomLocators = {
                        By.xpath("//div[contains(@class, 'room')]"),
                        By.xpath("//div[contains(@class, 'suite')]"),
                        By.xpath("//div[contains(@class, 'accommodation')]"),
                        By.xpath("//article[contains(@class, 'room')]")
                    };
                    
                    for (By locator : roomLocators) {
                        if (helper.isElementDisplayed(locator)) {
                            List<WebElement> rooms = helper.getElements(locator);
                            if (rooms.size() > 0) {
                                System.out.println("   ✅ Found " + rooms.size() + " room elements");
                                hasRooms = true;
                                break;
                            }
                        }
                    }
                }
                
                if (!hasRooms) {
                    System.out.println("⚠️ Rooms page loaded but no room elements found");
                }
            } else {
                System.out.println("❌ Not on rooms page");
                Assert.fail("Did not navigate to rooms page");
            }
            
            System.out.println("🎉 Hotel rooms view test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel rooms view test failed: " + e.getMessage());
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
            helper.mediumDelay();
            
            // Verify search form is present
            System.out.println("📍 Step 2: Verifying search form is present...");
            if (!helper.isElementDisplayed(HOME_SEARCH_FORM)) {
                Assert.fail("Homepage search form not found");
            }
            helper.shortDelay();
            
            // Fill search form with specific dates
            System.out.println("📍 Step 3: Filling search form with date parameters...");
            
            // Set city
            String testCity = "Kandy";
            System.out.println("   🏙️ Selecting city: " + testCity);
            helper.clearAndSendKeys(SEARCH_CITY_SELECT, testCity);
            helper.shortDelay();
            
            // Set specific check-in date (next week)
            String checkInDate = java.time.LocalDate.now().plusDays(7).toString();
            System.out.println("   📅 Setting check-in date: " + checkInDate);
            helper.clearAndSendKeys(SEARCH_CHECKIN_INPUT, checkInDate);
            helper.shortDelay();
            
            // Set specific check-out date (next week + 3 days)
            String checkOutDate = java.time.LocalDate.now().plusDays(10).toString();
            System.out.println("   📅 Setting check-out date: " + checkOutDate);
            helper.clearAndSendKeys(SEARCH_CHECKOUT_INPUT, checkOutDate);
            helper.shortDelay();
            
            // Set number of guests
            System.out.println("   👥 Setting guests: 4");
            helper.clearAndSendKeys(SEARCH_GUESTS_SELECT, "4");
            helper.mediumDelay(); // Wait to see the form filled
            
            // Submit search form
            System.out.println("📍 Step 4: Submitting search form...");
            boolean submitted = helper.smartFormSubmit(SEARCH_BUTTON);
            
            if (!submitted) {
                System.out.println("❌ Search form submission failed");
                Assert.fail("Could not submit search form");
            }
            
            // Wait for search results
            System.out.println("📍 Step 5: Waiting for search results...");
            helper.longDelay();
            
            // Verify we're on hotels page with search parameters
            System.out.println("📍 Step 6: Verifying search results with date parameters...");
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
                
                // Check if hotel cards are displayed
                if (helper.isElementDisplayed(HOTEL_CARDS)) {
                    List<WebElement> hotelCards = helper.getElements(HOTEL_CARDS);
                    System.out.println("✅ Found " + hotelCards.size() + " hotel cards");
                    
                    if (hotelCards.size() > 0) {
                        System.out.println("✅ Hotel search with date parameters test passed!");
                        
                        // Check if search parameters are displayed on the page
                        By[] searchParamLocators = {
                            By.xpath("//div[contains(@class, 'search-params')]"),
                            By.xpath("//div[contains(@class, 'search-info')]"),
                            By.xpath("//div[contains(@class, 'search-summary')]"),
                            By.xpath("//span[contains(text(), '" + testCity + "')]"),
                            By.xpath("//span[contains(text(), '" + checkInDate + "')]"),
                            By.xpath("//span[contains(text(), '" + checkOutDate + "')]")
                        };
                        
                        boolean foundSearchInfo = false;
                        for (By locator : searchParamLocators) {
                            if (helper.isElementDisplayed(locator)) {
                                String searchInfo = helper.getTextFromElement(locator);
                                if (!searchInfo.isEmpty()) {
                                    System.out.println("   ✅ Search parameters displayed: " + searchInfo);
                                    foundSearchInfo = true;
                                    break;
                                }
                            }
                        }
                        
                        if (!foundSearchInfo) {
                            System.out.println("   ⚠️ Search parameters not displayed on page");
                        }
                    } else {
                        System.out.println("⚠️ No hotels found for the selected date range");
                    }
                } else {
                    System.out.println("⚠️ No hotel cards found on results page");
                }
            } else {
                System.out.println("❌ Not redirected to hotels page");
                Assert.fail("Search did not redirect to hotels page");
            }
            
            System.out.println("🎉 Hotel search with date parameters test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Hotel search with date parameters test failed: " + e.getMessage());
            helper.mediumDelay();
            Assert.fail("Hotel search with date parameters test failed: " + e.getMessage());
        }
    }
    
}
