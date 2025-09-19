package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.testng.Assert;
import org.testng.annotations.Test;

import java.util.List;

/**
 * Test class for reservation cancellation functionality
 * Tests the reservation cancellation flow on the Flower Garden Hotel Booking website.
 */
public class ReservationCancellationTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Locators for login (required for cancellation)
    private static final By EMAIL_INPUT = By.id("email");
    private static final By PASSWORD_INPUT = By.id("password");
    private static final By LOGIN_BUTTON = By.xpath("//button[contains(text(), 'Sign In')]");
    private static final By LOGIN_LINK = By.xpath("//a[contains(text(), 'login')]");
    
    // Locators for user dropdown menu
    private static final By USER_AVATAR = By.xpath("//div[contains(@class, 'user-avatar')]");
    private static final By MY_RESERVATIONS_LINK = By.xpath("//a[contains(text(), 'My Reservations')]");
    
    // Locators for reservations page
    private static final By RESERVATION_CARDS = By.xpath("//div[contains(@class, 'reservation-card')]");
    private static final By CANCEL_BUTTON = By.xpath(".//a[contains(text(), 'Cancel')]");
    private static final By STATUS_BADGE = By.xpath(".//div[contains(@class, 'status-badge')]");
    
    // Locators for cancellation page
    private static final By CANCEL_CONTAINER = By.xpath("//div[contains(@class, 'cancel-container')]");
    private static final By CANCEL_HEADER = By.xpath("//h1[contains(text(), 'Cancel Reservation')]");
    private static final By RESERVATION_SUMMARY = By.xpath("//div[contains(@class, 'reservation-summary')]");
    private static final By HOTEL_NAME_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][1]//span");
    private static final By HOTEL_LOCATION_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][2]//span");
    private static final By ROOM_TYPE_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][3]//span");
    private static final By CHECKIN_DATE_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][4]//span");
    private static final By CHECKOUT_DATE_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][5]//span");
    private static final By GUESTS_COUNT_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][6]//span");
    private static final By TOTAL_AMOUNT_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][7]//span");
    private static final By BOOKING_REFERENCE_CANCEL = By.xpath("//div[contains(@class, 'detail-item')][8]//span");
    
    // Locators for cancellation actions
    private static final By CONFIRM_CANCEL_BUTTON = By.xpath("//button[contains(text(), 'Yes, Cancel Reservation')]");
    private static final By BACK_TO_RESERVATIONS_BUTTON = By.xpath("//a[contains(text(), 'Back to Reservations')]");
    private static final By WARNING_MESSAGE = By.xpath("//div[contains(@class, 'alert-warning')]");
    
    // Locators for success/error messages
    private static final By SUCCESS_MESSAGE = By.xpath("//div[contains(@class, 'alert-success')]");
    private static final By ERROR_MESSAGE = By.xpath("//div[contains(@class, 'alert-error')]");
    
    @Test(description = "Test successful reservation cancellation")
    public void testSuccessfulReservationCancellation() {
        helper = new PageObjectHelper(driver);
        
        // Step 1: Login
        performLogin();
        
        // Step 2: Navigate to My Reservations
        navigateToMyReservations();
        
        // Step 3: Find a confirmed reservation to cancel
        WebElement confirmedReservation = findConfirmedReservation();
        
        if (confirmedReservation != null) {
            // Step 4: Click Cancel button
            WebElement cancelButton = confirmedReservation.findElement(CANCEL_BUTTON);
            helper.clickElement(By.xpath("//a[contains(text(), 'Cancel')]"));
            
            // Step 5: Verify cancellation page is loaded
            helper.waitForElementToBeVisible(CANCEL_CONTAINER);
            Assert.assertTrue(helper.isElementDisplayed(CANCEL_HEADER), 
                "Cancel reservation header should be displayed");
            
            // Step 6: Verify reservation details are displayed
            verifyReservationDetailsOnCancelPage();
            
            // Step 7: Verify warning message
            Assert.assertTrue(helper.isElementDisplayed(WARNING_MESSAGE), 
                "Warning message should be displayed");
            
            // Step 8: Confirm cancellation
            helper.clickElement(CONFIRM_CANCEL_BUTTON);
            
            // Step 9: Verify success message
            helper.waitForElementToBeVisible(SUCCESS_MESSAGE);
            Assert.assertTrue(helper.isElementDisplayed(SUCCESS_MESSAGE), 
                "Success message should be displayed after cancellation");
            
            String successText = helper.getTextFromElement(SUCCESS_MESSAGE);
            Assert.assertTrue(successText.toLowerCase().contains("cancelled"), 
                "Success message should mention cancellation");
            
            System.out.println("Successful reservation cancellation test completed");
        } else {
            System.out.println("No confirmed reservations found for cancellation test");
        }
    }
    
    @Test(description = "Test cancellation page navigation")
    public void testCancellationPageNavigation() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Find a confirmed reservation
        WebElement confirmedReservation = findConfirmedReservation();
        
        if (confirmedReservation != null) {
            // Click Cancel button
            helper.clickElement(By.xpath("//a[contains(text(), 'Cancel')]"));
            
            // Wait for cancellation page
            helper.waitForElementToBeVisible(CANCEL_CONTAINER);
            
            // Test "Back to Reservations" button
            helper.clickElement(BACK_TO_RESERVATIONS_BUTTON);
            
            // Verify we're back on reservations page
            helper.waitForTitleToContain("My Reservations");
            Assert.assertTrue(helper.isElementDisplayed(RESERVATION_CARDS), 
                "Should be back on reservations page");
        }
        
        System.out.println("Cancellation page navigation test completed");
    }
    
    @Test(description = "Test cancellation confirmation dialog")
    public void testCancellationConfirmationDialog() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Find a confirmed reservation
        WebElement confirmedReservation = findConfirmedReservation();
        
        if (confirmedReservation != null) {
            // Click Cancel button
            helper.clickElement(By.xpath("//a[contains(text(), 'Cancel')]"));
            
            // Wait for cancellation page
            helper.waitForElementToBeVisible(CANCEL_CONTAINER);
            
            // Verify confirmation button text
            String confirmButtonText = helper.getTextFromElement(CONFIRM_CANCEL_BUTTON);
            Assert.assertTrue(confirmButtonText.toLowerCase().contains("cancel"), 
                "Confirm button should mention cancellation");
            
            // Verify warning message content
            String warningText = helper.getTextFromElement(WARNING_MESSAGE);
            Assert.assertTrue(warningText.toLowerCase().contains("important") || 
                             warningText.toLowerCase().contains("refund"), 
                "Warning message should mention important information or refund");
        }
        
        System.out.println("Cancellation confirmation dialog test completed");
    }
    
    @Test(description = "Test cancellation with already cancelled reservation")
    public void testCancellationWithAlreadyCancelledReservation() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Find a cancelled reservation
        WebElement cancelledReservation = findCancelledReservation();
        
        if (cancelledReservation != null) {
            // Verify cancel button is not present for cancelled reservations
            List<WebElement> cancelButtons = cancelledReservation.findElements(CANCEL_BUTTON);
            Assert.assertTrue(cancelButtons.size() == 0, 
                "Cancel button should not be present for cancelled reservations");
        }
        
        System.out.println("Cancellation with already cancelled reservation test completed");
    }
    
    @Test(description = "Test cancellation page reservation details accuracy")
    public void testCancellationPageReservationDetailsAccuracy() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Find a confirmed reservation
        WebElement confirmedReservation = findConfirmedReservation();
        
        if (confirmedReservation != null) {
            // Get reservation details from main page
            String hotelNameMain = confirmedReservation.findElement(By.xpath(".//h3")).getText();
            String hotelLocationMain = confirmedReservation.findElement(By.xpath(".//p[contains(@class, 'hotel-name')]")).getText();
            String roomTypeMain = confirmedReservation.findElement(By.xpath(".//p[contains(@class, 'room-type')]")).getText();
            
            // Click Cancel button
            helper.clickElement(By.xpath("//a[contains(text(), 'Cancel')]"));
            
            // Wait for cancellation page
            helper.waitForElementToBeVisible(CANCEL_CONTAINER);
            
            // Get reservation details from cancellation page
            String hotelNameCancel = helper.getTextFromElement(HOTEL_NAME_CANCEL);
            String hotelLocationCancel = helper.getTextFromElement(HOTEL_LOCATION_CANCEL);
            String roomTypeCancel = helper.getTextFromElement(ROOM_TYPE_CANCEL);
            
            // Verify details match
            Assert.assertEquals(hotelNameCancel, hotelNameMain, 
                "Hotel name should match between main page and cancellation page");
            Assert.assertEquals(hotelLocationCancel, hotelLocationMain, 
                "Hotel location should match between main page and cancellation page");
            Assert.assertEquals(roomTypeCancel, roomTypeMain, 
                "Room type should match between main page and cancellation page");
        }
        
        System.out.println("Cancellation page reservation details accuracy test completed");
    }
    
    @Test(description = "Test cancellation refund information")
    public void testCancellationRefundInformation() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Find a confirmed reservation
        WebElement confirmedReservation = findConfirmedReservation();
        
        if (confirmedReservation != null) {
            // Click Cancel button
            helper.clickElement(By.xpath("//a[contains(text(), 'Cancel')]"));
            
            // Wait for cancellation page
            helper.waitForElementToBeVisible(CANCEL_CONTAINER);
            
            // Verify refund information is displayed
            String warningText = helper.getTextFromElement(WARNING_MESSAGE);
            Assert.assertTrue(warningText.toLowerCase().contains("refund"), 
                "Warning message should mention refund information");
            
            // Verify total amount is displayed
            Assert.assertTrue(helper.isElementDisplayed(TOTAL_AMOUNT_CANCEL), 
                "Total amount should be displayed on cancellation page");
            
            String totalAmount = helper.getTextFromElement(TOTAL_AMOUNT_CANCEL);
            Assert.assertTrue(totalAmount.contains("$"), 
                "Total amount should contain currency symbol");
        }
        
        System.out.println("Cancellation refund information test completed");
    }
    
    @Test(description = "Test cancellation page accessibility")
    public void testCancellationPageAccessibility() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Find a confirmed reservation
        WebElement confirmedReservation = findConfirmedReservation();
        
        if (confirmedReservation != null) {
            // Click Cancel button
            helper.clickElement(By.xpath("//a[contains(text(), 'Cancel')]"));
            
            // Wait for cancellation page
            helper.waitForElementToBeVisible(CANCEL_CONTAINER);
            
            // Verify all important elements are accessible
            Assert.assertTrue(helper.isElementDisplayed(CANCEL_HEADER), 
                "Cancel header should be accessible");
            Assert.assertTrue(helper.isElementDisplayed(RESERVATION_SUMMARY), 
                "Reservation summary should be accessible");
            Assert.assertTrue(helper.isElementDisplayed(WARNING_MESSAGE), 
                "Warning message should be accessible");
            Assert.assertTrue(helper.isElementDisplayed(CONFIRM_CANCEL_BUTTON), 
                "Confirm cancel button should be accessible");
            Assert.assertTrue(helper.isElementDisplayed(BACK_TO_RESERVATIONS_BUTTON), 
                "Back to reservations button should be accessible");
        }
        
        System.out.println("Cancellation page accessibility test completed");
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
     * Helper method to navigate to My Reservations page
     */
    private void navigateToMyReservations() {
        // Click on user avatar to access dropdown
        helper.clickElement(USER_AVATAR);
        
        // Click on My Reservations link
        helper.clickElement(MY_RESERVATIONS_LINK);
        
        // Wait for reservations page to load
        helper.waitForTitleToContain("My Reservations");
    }
    
    /**
     * Helper method to find a confirmed reservation
     */
    private WebElement findConfirmedReservation() {
        List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
        
        for (WebElement card : reservationCards) {
            WebElement statusBadge = card.findElement(STATUS_BADGE);
            String status = statusBadge.getText().toLowerCase();
            
            if (status.equals("confirmed")) {
                // Check if cancel button is present
                List<WebElement> cancelButtons = card.findElements(CANCEL_BUTTON);
                if (cancelButtons.size() > 0) {
                    return card;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Helper method to find a cancelled reservation
     */
    private WebElement findCancelledReservation() {
        List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
        
        for (WebElement card : reservationCards) {
            WebElement statusBadge = card.findElement(STATUS_BADGE);
            String status = statusBadge.getText().toLowerCase();
            
            if (status.equals("cancelled")) {
                return card;
            }
        }
        
        return null;
    }
    
    /**
     * Helper method to verify reservation details on cancellation page
     */
    private void verifyReservationDetailsOnCancelPage() {
        Assert.assertTrue(helper.isElementDisplayed(HOTEL_NAME_CANCEL), 
            "Hotel name should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(HOTEL_LOCATION_CANCEL), 
            "Hotel location should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(ROOM_TYPE_CANCEL), 
            "Room type should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(CHECKIN_DATE_CANCEL), 
            "Check-in date should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(CHECKOUT_DATE_CANCEL), 
            "Check-out date should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(GUESTS_COUNT_CANCEL), 
            "Guests count should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(TOTAL_AMOUNT_CANCEL), 
            "Total amount should be displayed on cancellation page");
        Assert.assertTrue(helper.isElementDisplayed(BOOKING_REFERENCE_CANCEL), 
            "Booking reference should be displayed on cancellation page");
    }
}
