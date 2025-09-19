package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

/**
 * RoomBookingTest - Test room booking functionality
 * Flow: Register Account → Login → Test Room Booking
 */
public class RoomBookingTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Login page locators
    private static final By EMAIL_INPUT = By.id("email");
    private static final By PASSWORD_INPUT = By.id("password");
    private static final By LOGIN_BUTTON = By.xpath("//button[contains(text(), 'Sign In')]");
    private static final By LOGIN_LINK = By.xpath("//a[contains(text(), 'login')]");
    
    // User interface locators
    private static final By USER_AVATAR = By.xpath("//img[contains(@class, 'user-avatar')] | //div[contains(@class, 'user-avatar')]");
    private static final By LOGOUT_LINK = By.xpath("//a[contains(text(), 'logout') or contains(text(), 'Logout') or contains(text(), 'Sign Out')]");
    
    // Error/Success message locators
    private static final By ERROR_MESSAGE = By.xpath("//div[contains(@class, 'alert-error')] | //div[contains(@class, 'error')] | //div[contains(@class, 'alert-danger')]");
    private static final By SUCCESS_MESSAGE = By.xpath("//div[contains(@class, 'alert-success')] | //div[contains(@class, 'success')]");
    
    // Hotel search form locators
    private static final By CITY_DROPDOWN = By.id("city");
    private static final By CHECKIN_DATE = By.id("check_in");
    private static final By CHECKOUT_DATE = By.id("check_out");
    private static final By GUESTS_DROPDOWN = By.id("guests");
    private static final By SEARCH_BTN = By.xpath("//button[contains(text(), 'Search Hotels')]");
    
    // Hotel search results locators
    private static final By ROOM_CARDS = By.xpath("//div[contains(@class, 'room-card')]");
    private static final By ROOM_TYPES = By.xpath("//div[contains(@class, 'room-type')] | //h3[contains(text(), 'Room')] | //h4[contains(text(), 'Room')]");
    private static final By HOTEL_CARDS = By.xpath("//div[contains(@class, 'hotel-card')] | //div[contains(@class, 'hotel-item')]");
    private static final By BOOK_NOW_BTN = By.xpath("//a[contains(text(), 'Book Now')] | //button[contains(text(), 'Book Now')] | //a[contains(@class, 'book-now')]");
    
    // Booking page locators
    private static final By BOOKING_GUESTS = By.id("guests");
    private static final By SPECIAL_REQUESTS = By.id("special_requests");
    
    // Payment form locators
    private static final By CARD_NAME = By.id("card_name");
    private static final By CARD_NUMBER = By.id("card_number");
    private static final By CARD_EXPIRY = By.id("card_expiry");
    private static final By CARD_CVV = By.id("card_cvv");
    private static final By BILLING_ADDRESS = By.id("billing_address");
    private static final By COMPLETE_PAYMENT_BTN = By.xpath("//button[contains(text(), 'Complete Payment')] | //button[contains(text(), 'Book Now')] | //button[@type='submit']");
    
    // Booking confirmation locators
    private static final By BOOKING_CONFIRMATION = By.xpath("//div[contains(@class, 'booking-confirmation')] | //h1[contains(text(), 'Booking Confirmation')] | //h2[contains(text(), 'Booking Confirmation')]");
    private static final By BOOKING_SUCCESS = By.xpath("//div[contains(@class, 'success')] | //div[contains(@class, 'alert-success')] | //*[contains(text(), 'successfully')]");
    
    /**
     * Generate a unique email for testing
     */
    private String generateUniqueEmail() {
        String timestamp = String.valueOf(System.currentTimeMillis());
        return "roombookingtest" + timestamp + "@example.com";
    }
    
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
            
            // STEP 9: SEARCH FOR HOTELS
            System.out.println("📍 Step 9: Searching for hotels...");
            searchForHotels();
            System.out.println("✅ Hotel search completed");
            
            System.out.println("🎉 Complete room booking test completed!");
            
        } catch (Exception e) {
            System.out.println("❌ Room booking test failed: " + e.getMessage());
            helper.mediumDelay();
            Assert.fail("Room booking test failed: " + e.getMessage());
        }
    }
    
    /**
     * Search for hotels with specific criteria and display room types
     */
    private void searchForHotels() {
        System.out.println("  🏨 Starting hotel search...");
        
        try {
            // Navigate to homepage if not already there
            System.out.println("  🌐 Navigating to homepage...");
            driver.get(baseUrl);
            helper.waitForElementToBePresent(CITY_DROPDOWN);
            helper.mediumDelay();
            
            // Fill search form step by step
            System.out.println("  📋 Filling hotel search form...");
            
            // Select city
            System.out.println("    🏙️ Selecting city: Colombo");
            helper.selectDropdownByValue(CITY_DROPDOWN, "Colombo");
            helper.shortDelay();
            
            // Enter check-in date
            System.out.println("    📅 Setting check-in date: 11/11/2025");
            helper.clearAndSendKeys(CHECKIN_DATE, "11/11/2025");
            helper.shortDelay();
            
            // Enter check-out date
            System.out.println("    📅 Setting check-out date: 13/11/2025");
            helper.clearAndSendKeys(CHECKOUT_DATE, "13/11/2025");
            helper.shortDelay();
            
            // Select number of guests
            System.out.println("    👥 Selecting number of guests: 2");
            helper.selectDropdownByValue(GUESTS_DROPDOWN, "2");
            helper.shortDelay();
            
            // Submit search
            System.out.println("  🚀 Submitting hotel search...");
            
            // Scroll to make sure search button is visible and clickable
            helper.scrollDown(200);
            helper.shortDelay();
            
            // Try multiple methods to submit the search
            boolean searchSubmitted = false;
            
            // Method 1: Try smart form submit
            try {
                searchSubmitted = helper.smartFormSubmit(SEARCH_BTN);
                if (searchSubmitted) {
                    System.out.println("  ✅ Search submitted using smart form submit");
                }
            } catch (Exception e) {
                System.out.println("  ⚠️ Smart form submit failed: " + e.getMessage());
            }
            
            // Method 2: Try force click if smart submit failed
            if (!searchSubmitted) {
                try {
                    System.out.println("  🔄 Trying force click on search button...");
                    searchSubmitted = helper.forceClickElement(SEARCH_BTN);
                    if (searchSubmitted) {
                        System.out.println("  ✅ Search submitted using force click");
                    }
                } catch (Exception e) {
                    System.out.println("  ⚠️ Force click failed: " + e.getMessage());
                }
            }
            
            // Method 3: Try JavaScript form submission
            if (!searchSubmitted) {
                try {
                    System.out.println("  🔄 Trying JavaScript form submission...");
                    ((org.openqa.selenium.JavascriptExecutor) driver)
                        .executeScript("document.querySelector('form').submit();");
                    searchSubmitted = true;
                    System.out.println("  ✅ Search submitted using JavaScript");
                } catch (Exception e) {
                    System.out.println("  ⚠️ JavaScript submission failed: " + e.getMessage());
                }
            }
            
            if (!searchSubmitted) {
                System.out.println("  ❌ All hotel search submission methods failed");
                Assert.fail("Could not submit hotel search form");
            }
            
            // Wait for search results
            System.out.println("  ⏳ Waiting for search results...");
            helper.longDelay();
            
            // Verify search results loaded
            System.out.println("  📍 Verifying search results...");
            String currentUrl = driver.getCurrentUrl();
            System.out.println("    Current URL: " + currentUrl);
            
            // Check if we're on hotels page or search results page
            boolean onResultsPage = currentUrl.contains("hotels.php") || 
                                 currentUrl.contains("search") || 
                                 helper.isElementDisplayed(HOTEL_CARDS) ||
                                 helper.isElementDisplayed(ROOM_CARDS);
            
            if (!onResultsPage) {
                System.out.println("  ❌ Not on search results page");
                Assert.fail("Hotel search did not navigate to results page");
            }
            
            System.out.println("  ✅ Search results page loaded successfully");
            
            // Scroll down to see more content
            System.out.println("  📜 Scrolling down to view room types...");
            helper.scrollDown(500);
            helper.mediumDelay();
            
            // Look for room types and display them
            System.out.println("  🏠 Looking for room types...");
            displayRoomTypes();
            
            // Scroll down more to see additional content
            System.out.println("  📜 Scrolling down further...");
            helper.scrollDown(800);
            helper.mediumDelay();
            
            // Display any additional room information
            displayAdditionalRoomInfo();
            
            // STEP 10: CLICK BOOK NOW AND COMPLETE BOOKING
            System.out.println("  🏠 Starting room booking process...");
            completeRoomBooking();
            System.out.println("  ✅ Room booking completed successfully");
            
        } catch (Exception e) {
            System.out.println("  ❌ Hotel search failed: " + e.getMessage());
            e.printStackTrace();
            Assert.fail("Hotel search failed: " + e.getMessage());
        }
    }
    
    /**
     * Display available room types
     */
    private void displayRoomTypes() {
        try {
            // Check for room cards
            if (helper.isElementDisplayed(ROOM_CARDS)) {
                System.out.println("  🏠 Found room cards:");
                var roomCards = helper.getElements(ROOM_CARDS);
                System.out.println("    Total room cards found: " + roomCards.size());
                
                for (int i = 0; i < Math.min(roomCards.size(), 5); i++) {
                    try {
                        String roomText = roomCards.get(i).getText();
                        if (!roomText.trim().isEmpty()) {
                            System.out.println("    Room " + (i + 1) + ": " + roomText.substring(0, Math.min(100, roomText.length())) + "...");
                        }
                    } catch (Exception e) {
                        System.out.println("    Room " + (i + 1) + ": [Could not read room details]");
                    }
                }
            }
            
            // Check for specific room types
            if (helper.isElementDisplayed(ROOM_TYPES)) {
                System.out.println("  🏨 Found room types:");
                var roomTypes = helper.getElements(ROOM_TYPES);
                System.out.println("    Total room types found: " + roomTypes.size());
                
                for (int i = 0; i < Math.min(roomTypes.size(), 3); i++) {
                    try {
                        String roomTypeText = roomTypes.get(i).getText();
                        if (!roomTypeText.trim().isEmpty()) {
                            System.out.println("    Room Type " + (i + 1) + ": " + roomTypeText);
                        }
                    } catch (Exception e) {
                        System.out.println("    Room Type " + (i + 1) + ": [Could not read room type]");
                    }
                }
            }
            
            // Check for hotel cards
            if (helper.isElementDisplayed(HOTEL_CARDS)) {
                System.out.println("  🏨 Found hotel cards:");
                var hotelCards = helper.getElements(HOTEL_CARDS);
                System.out.println("    Total hotel cards found: " + hotelCards.size());
                
                for (int i = 0; i < Math.min(hotelCards.size(), 3); i++) {
                    try {
                        String hotelText = hotelCards.get(i).getText();
                        if (!hotelText.trim().isEmpty()) {
                            System.out.println("    Hotel " + (i + 1) + ": " + hotelText.substring(0, Math.min(80, hotelText.length())) + "...");
                        }
                    } catch (Exception e) {
                        System.out.println("    Hotel " + (i + 1) + ": [Could not read hotel details]");
                    }
                }
            }
            
        } catch (Exception e) {
            System.out.println("  ⚠️ Could not display room types: " + e.getMessage());
        }
    }
    
    /**
     * Display additional room information after scrolling
     */
    private void displayAdditionalRoomInfo() {
        try {
            System.out.println("  📋 Additional room information:");
            
            // Look for any pricing information
            By priceElements = By.xpath("//span[contains(@class, 'price')] | //div[contains(@class, 'price')] | //*[contains(text(), '$')]");
            if (helper.isElementDisplayed(priceElements)) {
                var prices = helper.getElements(priceElements);
                System.out.println("    💰 Found " + prices.size() + " price elements");
                for (int i = 0; i < Math.min(prices.size(), 3); i++) {
                    try {
                        String priceText = prices.get(i).getText();
                        if (!priceText.trim().isEmpty()) {
                            System.out.println("      Price " + (i + 1) + ": " + priceText);
                        }
                    } catch (Exception e) {
                        System.out.println("      Price " + (i + 1) + ": [Could not read price]");
                    }
                }
            }
            
            // Look for amenities
            By amenityElements = By.xpath("//div[contains(@class, 'amenity')] | //span[contains(@class, 'amenity')] | //*[contains(text(), 'WiFi') or contains(text(), 'Pool') or contains(text(), 'Gym')]");
            if (helper.isElementDisplayed(amenityElements)) {
                var amenities = helper.getElements(amenityElements);
                System.out.println("    🏊 Found " + amenities.size() + " amenity elements");
                for (int i = 0; i < Math.min(amenities.size(), 5); i++) {
                    try {
                        String amenityText = amenities.get(i).getText();
                        if (!amenityText.trim().isEmpty()) {
                            System.out.println("      Amenity " + (i + 1) + ": " + amenityText);
                        }
                    } catch (Exception e) {
                        System.out.println("      Amenity " + (i + 1) + ": [Could not read amenity]");
                    }
                }
            }
            
        } catch (Exception e) {
            System.out.println("  ⚠️ Could not display additional room info: " + e.getMessage());
        }
    }
    
    /**
     * Complete the room booking process
     */
    private void completeRoomBooking() {
        try {
            // Look for Book Now button
            System.out.println("    🔍 Looking for Book Now button...");
            
            // Scroll up a bit to make sure Book Now button is visible
            helper.scrollDown(-300);
            helper.shortDelay();
            
            // Try to find and click Book Now button
            boolean bookNowClicked = false;
            
            if (helper.isElementDisplayed(BOOK_NOW_BTN)) {
                System.out.println("    🚀 Clicking Book Now button...");
                try {
                    helper.clickElement(BOOK_NOW_BTN);
                    bookNowClicked = true;
                    System.out.println("    ✅ Book Now button clicked successfully");
                } catch (Exception e) {
                    System.out.println("    ⚠️ Regular click failed, trying JavaScript click...");
                    try {
                        helper.forceClickElement(BOOK_NOW_BTN);
                        bookNowClicked = true;
                        System.out.println("    ✅ Book Now button clicked with JavaScript");
                    } catch (Exception e2) {
                        System.out.println("    ❌ JavaScript click also failed: " + e2.getMessage());
                    }
                }
            } else {
                System.out.println("    ❌ Book Now button not found");
                // Try alternative locators
                By[] altBookButtons = {
                    By.xpath("//a[contains(text(), 'Book')]"),
                    By.xpath("//button[contains(text(), 'Book')]"),
                    By.xpath("//a[contains(@href, 'book')]"),
                    By.xpath("//button[contains(@class, 'book')]")
                };
                
                for (By altButton : altBookButtons) {
                    if (helper.isElementDisplayed(altButton)) {
                        System.out.println("    🔄 Trying alternative book button: " + altButton);
                        try {
                            helper.clickElement(altButton);
                            bookNowClicked = true;
                            System.out.println("    ✅ Alternative book button clicked");
                            break;
                        } catch (Exception e) {
                            System.out.println("    ⚠️ Alternative button click failed: " + e.getMessage());
                        }
                    }
                }
            }
            
            if (!bookNowClicked) {
                System.out.println("    ❌ Could not click any Book Now button");
                Assert.fail("Could not find or click Book Now button");
            }
            
            // Wait for booking page to load
            System.out.println("    ⏳ Waiting for booking page to load...");
            helper.longDelay();
            
            // Verify we're on booking page
            String currentUrl = driver.getCurrentUrl();
            System.out.println("    📍 Current URL: " + currentUrl);
            
            boolean onBookingPage = currentUrl.contains("book") || 
                                  currentUrl.contains("booking") ||
                                  helper.isElementDisplayed(BOOKING_GUESTS) ||
                                  helper.isElementDisplayed(CARD_NAME);
            
            if (!onBookingPage) {
                System.out.println("    ❌ Not on booking page");
                Assert.fail("Did not navigate to booking page");
            }
            
            System.out.println("    ✅ Successfully navigated to booking page");
            
            // Fill booking form
            System.out.println("    📋 Filling booking form...");
            fillBookingForm();
            
            // Fill payment form
            System.out.println("    💳 Filling payment form...");
            fillPaymentForm();
            
            // Complete booking
            System.out.println("    🚀 Completing booking...");
            completeBookingSubmission();
            
            // Verify booking success
            System.out.println("    ✅ Verifying booking success...");
            verifyBookingSuccess();
            
        } catch (Exception e) {
            System.out.println("    ❌ Booking process failed: " + e.getMessage());
            e.printStackTrace();
            Assert.fail("Booking process failed: " + e.getMessage());
        }
    }
    
    /**
     * Fill the booking form with guest information
     */
    private void fillBookingForm() {
        try {
            // Select number of guests for booking
            if (helper.isElementDisplayed(BOOKING_GUESTS)) {
                System.out.println("      👥 Setting booking guests: 2");
                helper.selectDropdownByValue(BOOKING_GUESTS, "2");
                helper.shortDelay();
            }
            
            // Add special requests
            if (helper.isElementDisplayed(SPECIAL_REQUESTS)) {
                System.out.println("      📝 Adding special requests...");
                helper.clearAndSendKeys(SPECIAL_REQUESTS, "Please provide extra pillows and late checkout. Also, please arrange airport pickup.");
                helper.shortDelay();
            }
            
            System.out.println("      ✅ Booking form filled successfully");
            
        } catch (Exception e) {
            System.out.println("      ⚠️ Could not fill booking form: " + e.getMessage());
        }
    }
    
    /**
     * Fill the payment form with test data
     */
    private void fillPaymentForm() {
        try {
            // Card holder name
            if (helper.isElementDisplayed(CARD_NAME)) {
                System.out.println("      👤 Setting card name: Test User");
                helper.clearAndSendKeys(CARD_NAME, "Test User");
                helper.shortDelay();
            }
            
            // Card number
            if (helper.isElementDisplayed(CARD_NUMBER)) {
                System.out.println("      💳 Setting card number: 4111111111111111");
                helper.clearAndSendKeys(CARD_NUMBER, "4111111111111111");
                helper.shortDelay();
            }
            
            // Card expiry
            if (helper.isElementDisplayed(CARD_EXPIRY)) {
                System.out.println("      📅 Setting card expiry: 12/25");
                helper.clearAndSendKeys(CARD_EXPIRY, "12/25");
                helper.shortDelay();
            }
            
            // Card CVV
            if (helper.isElementDisplayed(CARD_CVV)) {
                System.out.println("      🔒 Setting card CVV: 123");
                helper.clearAndSendKeys(CARD_CVV, "123");
                helper.shortDelay();
            }
            
            // Billing address
            if (helper.isElementDisplayed(BILLING_ADDRESS)) {
                System.out.println("      🏠 Setting billing address...");
                helper.clearAndSendKeys(BILLING_ADDRESS, "123 Test Street, Test City, Test State 12345");
                helper.shortDelay();
            }
            
            System.out.println("      ✅ Payment form filled successfully");
            
        } catch (Exception e) {
            System.out.println("      ⚠️ Could not fill payment form: " + e.getMessage());
        }
    }
    
    /**
     * Complete the booking submission
     */
    private void completeBookingSubmission() {
        try {
            // Scroll down to make sure payment button is visible
            helper.scrollDown(300);
            helper.shortDelay();
            
            // Try to submit the booking
            System.out.println("      🚀 Submitting booking...");
            boolean bookingSubmitted = helper.smartFormSubmit(COMPLETE_PAYMENT_BTN);
            
            if (!bookingSubmitted) {
                System.out.println("      ❌ Booking submission failed");
                Assert.fail("Could not submit booking form");
            }
            
            System.out.println("      ✅ Booking submitted successfully");
            
            // Wait for processing
            System.out.println("      ⏳ Waiting for booking processing...");
            helper.longDelay();
            
            // Handle "Save Card" popup if it appears
            System.out.println("      🔍 Checking for save card popup...");
            handleSaveCardPopup();
            
        } catch (Exception e) {
            System.out.println("      ❌ Booking submission failed: " + e.getMessage());
            Assert.fail("Booking submission failed: " + e.getMessage());
        }
    }
    
    /**
     * Verify booking success
     */
    private void verifyBookingSuccess() {
        try {
            String currentUrl = driver.getCurrentUrl();
            System.out.println("      📍 Final URL: " + currentUrl);
            
            // Check for booking confirmation page
            boolean bookingSuccess = currentUrl.contains("booking_confirmation") ||
                                   currentUrl.contains("confirmation") ||
                                   helper.isElementDisplayed(BOOKING_CONFIRMATION) ||
                                   helper.isElementDisplayed(BOOKING_SUCCESS);
            
            if (bookingSuccess) {
                System.out.println("      🎉 Booking completed successfully!");
                
                // Try to display confirmation details
                if (helper.isElementDisplayed(BOOKING_CONFIRMATION)) {
                    String confirmationText = helper.getTextFromElement(BOOKING_CONFIRMATION);
                    System.out.println("      📋 Confirmation: " + confirmationText);
                }
                
                if (helper.isElementDisplayed(BOOKING_SUCCESS)) {
                    String successText = helper.getTextFromElement(BOOKING_SUCCESS);
                    System.out.println("      ✅ Success message: " + successText);
                }
                
            } else {
                System.out.println("      ❌ Booking confirmation not found");
                // Check for error messages
                if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                    String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                    System.out.println("      ❌ Error: " + errorText);
                    Assert.fail("Booking failed with error: " + errorText);
                } else {
                    Assert.fail("Booking completion could not be verified");
                }
            }
            
        } catch (Exception e) {
            System.out.println("      ❌ Booking verification failed: " + e.getMessage());
            Assert.fail("Booking verification failed: " + e.getMessage());
        }
    }
    
    /**
     * Handle "Save Card" popup that appears after payment
     */
    private void handleSaveCardPopup() {
        try {
            // Common locators for save card popup
            By[] saveCardPopupLocators = {
                By.xpath("//div[contains(@class, 'modal') and contains(text(), 'save')]"),
                By.xpath("//div[contains(@class, 'popup') and contains(text(), 'card')]"),
                By.xpath("//div[contains(@class, 'dialog') and contains(text(), 'save')]"),
                By.xpath("//*[contains(text(), 'Save card')]"),
                By.xpath("//*[contains(text(), 'Save this card')]"),
                By.xpath("//*[contains(text(), 'Remember card')]"),
                By.xpath("//*[contains(text(), 'Store card')]"),
                By.xpath("//button[contains(text(), 'Save')]"),
                By.xpath("//button[contains(text(), 'Don\\'t save')]"),
                By.xpath("//button[contains(text(), 'Cancel')]"),
                By.xpath("//button[contains(text(), 'No')]"),
                By.xpath("//button[contains(text(), 'Skip')]"),
                By.xpath("//button[contains(@class, 'close')]"),
                By.xpath("//button[contains(@class, 'cancel')]"),
                By.xpath("//span[contains(@class, 'close')]"),
                By.xpath("//*[@aria-label='Close']"),
                By.xpath("//*[@aria-label='Cancel']")
            };
            
            // Wait a bit for popup to appear
            helper.shortDelay();
            
            // Check for popup and try to dismiss it
            boolean popupHandled = false;
            
            for (By locator : saveCardPopupLocators) {
                try {
                    if (helper.isElementDisplayed(locator)) {
                        System.out.println("        🎯 Found save card popup element: " + locator);
                        
                        // Try to click the dismiss button
                        try {
                            helper.clickElement(locator);
                            System.out.println("        ✅ Successfully dismissed save card popup");
                            popupHandled = true;
                            helper.shortDelay();
                            break;
                        } catch (Exception e) {
                            System.out.println("        ⚠️ Regular click failed, trying JavaScript click...");
                            try {
                                helper.forceClickElement(locator);
                                System.out.println("        ✅ Successfully dismissed popup with JavaScript");
                                popupHandled = true;
                                helper.shortDelay();
                                break;
                            } catch (Exception e2) {
                                System.out.println("        ❌ JavaScript click also failed: " + e2.getMessage());
                            }
                        }
                    }
                } catch (Exception e) {
                    // Continue to next locator
                }
            }
            
            // If popup wasn't found with specific locators, try generic modal dismissal
            if (!popupHandled) {
                System.out.println("        🔄 Trying generic modal dismissal...");
                
                // Try pressing Escape key
                try {
                    ((org.openqa.selenium.JavascriptExecutor) driver)
                        .executeScript("document.dispatchEvent(new KeyboardEvent('keydown', {key: 'Escape', keyCode: 27}));");
                    System.out.println("        ✅ Pressed Escape key to dismiss popup");
                    popupHandled = true;
                    helper.shortDelay();
                } catch (Exception e) {
                    System.out.println("        ⚠️ Escape key failed: " + e.getMessage());
                }
                
                // Try clicking outside the modal
                if (!popupHandled) {
                    try {
                        ((org.openqa.selenium.JavascriptExecutor) driver)
                            .executeScript("document.querySelector('body').click();");
                        System.out.println("        ✅ Clicked outside modal to dismiss");
                        popupHandled = true;
                        helper.shortDelay();
                    } catch (Exception e) {
                        System.out.println("        ⚠️ Outside click failed: " + e.getMessage());
                    }
                }
            }
            
            if (popupHandled) {
                System.out.println("        🎉 Save card popup successfully dismissed");
            } else {
                System.out.println("        ℹ️ No save card popup found or already dismissed");
            }
            
        } catch (Exception e) {
            System.out.println("        ⚠️ Error handling save card popup: " + e.getMessage());
            // Don't fail the test if popup handling fails
        }
    }
}
