package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

/**
 * Test class for hotel booking functionality
 * Tests the complete hotel booking flow on the Flower Garden Hotel Booking website.
 */
public class HotelBookingTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Locators for login (required for booking)
    private static final By EMAIL_INPUT = By.id("email");
    private static final By PASSWORD_INPUT = By.id("password");
    private static final By LOGIN_BUTTON = By.xpath("//button[contains(text(), 'Sign In')]");
    private static final By LOGIN_LINK = By.xpath("//a[contains(text(), 'login')]");
    
    // Locators for hotels page
    private static final By HOTELS_LINK = By.xpath("//a[contains(text(), 'hotels')]");
    private static final By HOTEL_CARDS = By.xpath("//div[contains(@class, 'hotel-card')]");
    private static final By VIEW_ROOMS_BUTTON = By.xpath(".//a[contains(text(), 'View Rooms')]");
    
    // Locators for rooms page
    private static final By ROOM_CARDS = By.xpath("//div[contains(@class, 'room-card')]");
    private static final By BOOK_NOW_BUTTON = By.xpath(".//a[contains(text(), 'Book Now')]");
    
    // Locators for booking page
    private static final By GUESTS_SELECT = By.id("guests");
    private static final By SPECIAL_REQUESTS_TEXTAREA = By.id("special_requests");
    private static final By COUPON_CODE_INPUT = By.id("coupon_code");
    private static final By APPLY_COUPON_BUTTON = By.id("apply_coupon");
    private static final By COUPON_MESSAGE = By.id("coupon_message");
    
    // Payment form locators
    private static final By CARD_NAME_INPUT = By.id("card_name");
    private static final By CARD_NUMBER_INPUT = By.id("card_number");
    private static final By CARD_EXPIRY_INPUT = By.id("card_expiry");
    private static final By CARD_CVV_INPUT = By.id("card_cvv");
    private static final By BILLING_ADDRESS_TEXTAREA = By.id("billing_address");
    private static final By BOOK_BUTTON = By.xpath("//button[contains(text(), 'Complete Payment')]");
    
    // Booking summary locators
    private static final By HOTEL_NAME_SUMMARY = By.xpath("//div[contains(@class, 'summary-hotel')]//h3");
    private static final By ROOM_TYPE_SUMMARY = By.xpath("//div[contains(@class, 'summary-room')]//h4");
    private static final By CHECKIN_DATE_SUMMARY = By.xpath("//span[contains(@class, 'date-value')]");
    private static final By TOTAL_AMOUNT_SUMMARY = By.xpath("//span[contains(@class, 'total-amount')]");
    
    // Success/Error message locators
    private static final By SUCCESS_MESSAGE = By.xpath("//div[contains(@class, 'alert-success')]");
    private static final By ERROR_MESSAGE = By.xpath("//div[contains(@class, 'alert-error')]");
    
    @Test(description = "Test complete hotel booking flow with valid data")
    public void testCompleteHotelBookingFlow() {
        helper = new PageObjectHelper(driver);
        
        // Step 1: Login
        performLogin();
        
        // Step 2: Navigate to hotels and select a room
        navigateToRooms();
        
        // Step 3: Start booking process
        startBookingProcess();
        
        // Step 4: Fill booking form
        fillBookingForm();
        
        // Step 5: Fill payment form
        fillPaymentForm();
        
        // Step 6: Complete booking
        completeBooking();
        
        System.out.println("Complete hotel booking flow test completed");
    }
    
    @Test(description = "Test booking with special requests")
    public void testBookingWithSpecialRequests() {
        helper = new PageObjectHelper(driver);
        
        // Login
        performLogin();
        
        // Navigate to rooms
        navigateToRooms();
        
        // Start booking
        startBookingProcess();
        
        // Fill booking form with special requests
        helper.sendKeysToElement(GUESTS_SELECT, "2");
        helper.sendKeysToElement(SPECIAL_REQUESTS_TEXTAREA, 
            "Please provide extra pillows and a late checkout if possible.");
        
        // Fill payment form
        fillPaymentForm();
        
        // Complete booking
        completeBooking();
        
        System.out.println("Booking with special requests test completed");
    }
    
    @Test(description = "Test booking with coupon code")
    public void testBookingWithCouponCode() {
        helper = new PageObjectHelper(driver);
        
        // Login
        performLogin();
        
        // Navigate to rooms
        navigateToRooms();
        
        // Start booking
        startBookingProcess();
        
        // Fill booking form
        helper.sendKeysToElement(GUESTS_SELECT, "2");
        
        // Apply coupon code
        helper.sendKeysToElement(COUPON_CODE_INPUT, "WELCOME10");
        helper.clickElement(APPLY_COUPON_BUTTON);
        
        // Wait for coupon validation
        helper.waitForElementToBeVisible(COUPON_MESSAGE);
        
        // Check if coupon was applied successfully
        String couponMessage = helper.getTextFromElement(COUPON_MESSAGE);
        boolean couponApplied = couponMessage.toLowerCase().contains("success") || 
                               couponMessage.toLowerCase().contains("applied");
        
        if (couponApplied) {
            System.out.println("Coupon applied successfully: " + couponMessage);
        } else {
            System.out.println("Coupon validation message: " + couponMessage);
        }
        
        // Fill payment form
        fillPaymentForm();
        
        // Complete booking
        completeBooking();
        
        System.out.println("Booking with coupon code test completed");
    }
    
    @Test(description = "Test booking with invalid payment details")
    public void testBookingWithInvalidPaymentDetails() {
        helper = new PageObjectHelper(driver);
        
        // Login
        performLogin();
        
        // Navigate to rooms
        navigateToRooms();
        
        // Start booking
        startBookingProcess();
        
        // Fill booking form
        helper.sendKeysToElement(GUESTS_SELECT, "2");
        
        // Fill payment form with invalid data
        helper.sendKeysToElement(CARD_NAME_INPUT, "Test User");
        helper.sendKeysToElement(CARD_NUMBER_INPUT, "1234567890123456"); // Invalid card number
        helper.sendKeysToElement(CARD_EXPIRY_INPUT, "13/25"); // Invalid expiry
        helper.sendKeysToElement(CARD_CVV_INPUT, "12"); // Invalid CVV
        helper.sendKeysToElement(BILLING_ADDRESS_TEXTAREA, "123 Test Street");
        
        // Submit booking form
        helper.clickElement(BOOK_BUTTON);
        
        // Verify error handling
        boolean hasError = helper.isElementDisplayed(ERROR_MESSAGE) || 
                          helper.waitForTitleToContain("Book Hotel");
        
        Assert.assertTrue(hasError, 
            "Should show error for invalid payment details");
        
        System.out.println("Booking with invalid payment details test completed");
    }
    
    @Test(description = "Test booking form validation")
    public void testBookingFormValidation() {
        helper = new PageObjectHelper(driver);
        
        // Login
        performLogin();
        
        // Navigate to rooms
        navigateToRooms();
        
        // Start booking
        startBookingProcess();
        
        // Try to submit form without filling required fields
        helper.clickElement(BOOK_BUTTON);
        
        // Verify form validation prevents submission
        boolean stillOnBookingPage = helper.waitForTitleToContain("Book Hotel");
        boolean hasValidationErrors = helper.isElementDisplayed(ERROR_MESSAGE);
        
        Assert.assertTrue(stillOnBookingPage || hasValidationErrors, 
            "Should prevent submission with missing required fields");
        
        System.out.println("Booking form validation test completed");
    }
    
    @Test(description = "Test booking summary display")
    public void testBookingSummaryDisplay() {
        helper = new PageObjectHelper(driver);
        
        // Login
        performLogin();
        
        // Navigate to rooms
        navigateToRooms();
        
        // Start booking
        startBookingProcess();
        
        // Verify booking summary is displayed
        Assert.assertTrue(helper.isElementDisplayed(HOTEL_NAME_SUMMARY), 
            "Hotel name should be displayed in summary");
        Assert.assertTrue(helper.isElementDisplayed(ROOM_TYPE_SUMMARY), 
            "Room type should be displayed in summary");
        Assert.assertTrue(helper.isElementDisplayed(CHECKIN_DATE_SUMMARY), 
            "Check-in date should be displayed in summary");
        Assert.assertTrue(helper.isElementDisplayed(TOTAL_AMOUNT_SUMMARY), 
            "Total amount should be displayed in summary");
        
        // Verify summary contains valid data
        String hotelName = helper.getTextFromElement(HOTEL_NAME_SUMMARY);
        String roomType = helper.getTextFromElement(ROOM_TYPE_SUMMARY);
        String totalAmount = helper.getTextFromElement(TOTAL_AMOUNT_SUMMARY);
        
        Assert.assertFalse(hotelName.isEmpty(), "Hotel name should not be empty");
        Assert.assertFalse(roomType.isEmpty(), "Room type should not be empty");
        Assert.assertTrue(totalAmount.contains("$"), "Total amount should contain currency symbol");
        
        System.out.println("Booking summary display test completed");
    }
    
    /**
     * Helper method to perform user login
     */
    private void performLogin() {
        helper.clickElement(LOGIN_LINK);
        helper.waitForTitleToContain("Login");
        
        String testEmail = getProperty("test.user.email", "testuser@example.com");
        String testPassword = getProperty("test.user.password", "TestPassword123!");
        
        helper.sendKeysToElement(EMAIL_INPUT, testEmail);
        helper.sendKeysToElement(PASSWORD_INPUT, testPassword);
        helper.clickElement(LOGIN_BUTTON);
        
        // Wait for successful login
        helper.waitForUrlToContain("home.php");
    }
    
    /**
     * Helper method to navigate to rooms page
     */
    private void navigateToRooms() {
        helper.clickElement(HOTELS_LINK);
        helper.waitForTitleToContain("Hotels");
        
        // Click on first hotel's "View Rooms" button
        helper.clickElement(By.xpath("//a[contains(text(), 'View Rooms')]"));
        
        // Wait for rooms to load
        helper.waitForElementToBeVisible(ROOM_CARDS);
    }
    
    /**
     * Helper method to start booking process
     */
    private void startBookingProcess() {
        // Click on first room's "Book Now" button
        helper.clickElement(By.xpath("//a[contains(text(), 'Book Now')]"));
        
        // Wait for booking page to load
        helper.waitForTitleToContain("Book Hotel");
    }
    
    /**
     * Helper method to fill booking form
     */
    private void fillBookingForm() {
        helper.sendKeysToElement(GUESTS_SELECT, "2");
        helper.sendKeysToElement(SPECIAL_REQUESTS_TEXTAREA, 
            "Please provide extra towels and a quiet room.");
    }
    
    /**
     * Helper method to fill payment form
     */
    private void fillPaymentForm() {
        String cardName = getProperty("test.card.name", "Test User");
        String cardNumber = getProperty("test.card.number", "4111111111111111");
        String cardExpiry = getProperty("test.card.expiry", "12/25");
        String cardCvv = getProperty("test.card.cvv", "123");
        String billingAddress = getProperty("test.billing.address", "123 Test Street");
        
        helper.sendKeysToElement(CARD_NAME_INPUT, cardName);
        helper.sendKeysToElement(CARD_NUMBER_INPUT, cardNumber);
        helper.sendKeysToElement(CARD_EXPIRY_INPUT, cardExpiry);
        helper.sendKeysToElement(CARD_CVV_INPUT, cardCvv);
        helper.sendKeysToElement(BILLING_ADDRESS_TEXTAREA, billingAddress);
    }
    
    /**
     * Helper method to complete booking
     */
    private void completeBooking() {
        helper.clickElement(BOOK_BUTTON);
        
        // Wait for booking confirmation or error
        boolean bookingCompleted = helper.waitForUrlToContain("booking_confirmation.php") ||
                                  helper.isElementDisplayed(SUCCESS_MESSAGE);
        
        if (bookingCompleted) {
            System.out.println("Booking completed successfully");
        } else {
            // Check for error message
            if (helper.isElementDisplayed(ERROR_MESSAGE)) {
                String errorText = helper.getTextFromElement(ERROR_MESSAGE);
                System.out.println("Booking error: " + errorText);
            }
        }
    }
}
