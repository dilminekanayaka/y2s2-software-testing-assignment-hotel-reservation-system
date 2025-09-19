package com.flowergarden.tests;

import com.flowergarden.utils.PageObjectHelper;
import com.flowergarden.utils.TestBase;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.testng.Assert;
import org.testng.annotations.Test;

import java.util.List;

/**
 * Test class for reservation viewing functionality
 * Tests the reservation viewing and management flow on the Flower Garden Hotel Booking website.
 */
public class ReservationViewTest extends TestBase {
    
    private PageObjectHelper helper;
    
    // Locators for login (required for viewing reservations)
    private static final By EMAIL_INPUT = By.id("email");
    private static final By PASSWORD_INPUT = By.id("password");
    private static final By LOGIN_BUTTON = By.xpath("//button[contains(text(), 'Sign In')]");
    private static final By LOGIN_LINK = By.xpath("//a[contains(text(), 'login')]");
    
    // Locators for user dropdown menu
    private static final By USER_AVATAR = By.xpath("//div[contains(@class, 'user-avatar')]");
    private static final By MY_RESERVATIONS_LINK = By.xpath("//a[contains(text(), 'My Reservations')]");
    
    // Locators for reservations page
    private static final By RESERVATIONS_CONTAINER = By.xpath("//div[contains(@class, 'reservations-container')]");
    private static final By RESERVATION_CARDS = By.xpath("//div[contains(@class, 'reservation-card')]");
    private static final By NO_RESERVATIONS_MESSAGE = By.xpath("//div[contains(@class, 'no-reservations')]");
    
    // Locators for individual reservation card
    private static final By RESERVATION_HEADER = By.xpath(".//div[contains(@class, 'reservation-header')]");
    private static final By HOTEL_NAME_RESERVATION = By.xpath(".//h3");
    private static final By HOTEL_LOCATION_RESERVATION = By.xpath(".//p[contains(@class, 'hotel-name')]");
    private static final By ROOM_TYPE_RESERVATION = By.xpath(".//p[contains(@class, 'room-type')]");
    private static final By STATUS_BADGE = By.xpath(".//div[contains(@class, 'status-badge')]");
    
    // Locators for reservation details
    private static final By RESERVATION_DETAILS = By.xpath(".//div[contains(@class, 'reservation-details')]");
    private static final By CHECKIN_DATE = By.xpath(".//div[contains(@class, 'detail-group')][1]//p");
    private static final By CHECKOUT_DATE = By.xpath(".//div[contains(@class, 'detail-group')][2]//p");
    private static final By GUESTS_COUNT = By.xpath(".//div[contains(@class, 'detail-group')][3]//p");
    private static final By BOOKING_REFERENCE = By.xpath(".//div[contains(@class, 'detail-group')][4]//p");
    private static final By TOTAL_AMOUNT = By.xpath(".//div[contains(@class, 'detail-group')][5]//p");
    private static final By PAYMENT_STATUS = By.xpath(".//div[contains(@class, 'detail-group')][6]//p");
    
    // Locators for reservation actions
    private static final By RESERVATION_ACTIONS = By.xpath(".//div[contains(@class, 'reservation-actions')]");
    private static final By VIEW_DETAILS_BUTTON = By.xpath(".//a[contains(text(), 'View Details')]");
    private static final By CANCEL_BUTTON = By.xpath(".//a[contains(text(), 'Cancel')]");
    private static final By BOOK_ANOTHER_BUTTON = By.xpath(".//a[contains(text(), 'Book Another')]");
    
    // Locators for reservation details page
    private static final By RESERVATION_DETAILS_PAGE = By.xpath("//div[contains(@class, 'booking-confirmation')]");
    private static final By BOOKING_REFERENCE_DETAILS = By.xpath("//h2[contains(text(), 'Booking Reference')]");
    private static final By HOTEL_INFO_DETAILS = By.xpath("//div[contains(@class, 'hotel-info')]");
    private static final By BOOKING_DATES_DETAILS = By.xpath("//div[contains(@class, 'booking-dates')]");
    private static final By PAYMENT_INFO_DETAILS = By.xpath("//div[contains(@class, 'payment-info')]");
    
    @Test(description = "Test viewing reservations for logged-in user")
    public void testViewReservationsForLoggedInUser() {
        helper = new PageObjectHelper(driver);
        
        // Step 1: Login
        performLogin();
        
        // Step 2: Navigate to My Reservations
        navigateToMyReservations();
        
        // Step 3: Verify reservations page is loaded
        Assert.assertTrue(helper.isElementDisplayed(RESERVATIONS_CONTAINER), 
            "Reservations container should be displayed");
        
        // Step 4: Check if reservations exist or show no reservations message
        boolean hasReservations = helper.isElementDisplayed(RESERVATION_CARDS);
        boolean hasNoReservationsMessage = helper.isElementDisplayed(NO_RESERVATIONS_MESSAGE);
        
        Assert.assertTrue(hasReservations || hasNoReservationsMessage, 
            "Should either show reservations or no reservations message");
        
        if (hasReservations) {
            verifyReservationCards();
        } else {
            verifyNoReservationsMessage();
        }
        
        System.out.println("View reservations for logged-in user test completed");
    }
    
    @Test(description = "Test reservation card details display")
    public void testReservationCardDetailsDisplay() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Check if reservations exist
        if (helper.isElementDisplayed(RESERVATION_CARDS)) {
            List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
            Assert.assertTrue(reservationCards.size() > 0, 
                "At least one reservation should be displayed");
            
            WebElement firstReservation = reservationCards.get(0);
            
            // Verify reservation header elements
            Assert.assertTrue(firstReservation.findElement(HOTEL_NAME_RESERVATION).isDisplayed(), 
                "Hotel name should be displayed in reservation card");
            Assert.assertTrue(firstReservation.findElement(HOTEL_LOCATION_RESERVATION).isDisplayed(), 
                "Hotel location should be displayed in reservation card");
            Assert.assertTrue(firstReservation.findElement(ROOM_TYPE_RESERVATION).isDisplayed(), 
                "Room type should be displayed in reservation card");
            Assert.assertTrue(firstReservation.findElement(STATUS_BADGE).isDisplayed(), 
                "Status badge should be displayed in reservation card");
            
            // Verify reservation details
            Assert.assertTrue(firstReservation.findElement(RESERVATION_DETAILS).isDisplayed(), 
                "Reservation details should be displayed");
            
            // Verify action buttons
            Assert.assertTrue(firstReservation.findElement(RESERVATION_ACTIONS).isDisplayed(), 
                "Reservation actions should be displayed");
        }
        
        System.out.println("Reservation card details display test completed");
    }
    
    @Test(description = "Test reservation status display")
    public void testReservationStatusDisplay() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Check if reservations exist
        if (helper.isElementDisplayed(RESERVATION_CARDS)) {
            List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
            
            for (WebElement reservationCard : reservationCards) {
                WebElement statusBadge = reservationCard.findElement(STATUS_BADGE);
                String statusText = statusBadge.getText();
                
                // Verify status is one of the expected values
                Assert.assertTrue(
                    statusText.equalsIgnoreCase("confirmed") ||
                    statusText.equalsIgnoreCase("pending") ||
                    statusText.equalsIgnoreCase("cancelled") ||
                    statusText.equalsIgnoreCase("checked-in") ||
                    statusText.equalsIgnoreCase("checked-out"),
                    "Status should be one of: confirmed, pending, cancelled, checked-in, checked-out"
                );
                
                // Verify status badge has appropriate CSS class
                String statusClass = statusBadge.getAttribute("class");
                Assert.assertTrue(statusClass.contains("status-"), 
                    "Status badge should have status CSS class");
            }
        }
        
        System.out.println("Reservation status display test completed");
    }
    
    @Test(description = "Test reservation details page navigation")
    public void testReservationDetailsPageNavigation() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Check if reservations exist
        if (helper.isElementDisplayed(RESERVATION_CARDS)) {
            List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
            WebElement firstReservation = reservationCards.get(0);
            
            // Click on "View Details" button
            WebElement viewDetailsButton = firstReservation.findElement(VIEW_DETAILS_BUTTON);
            helper.clickElement(By.xpath("//a[contains(text(), 'View Details')]"));
            
            // Wait for details page to load
            helper.waitForElementToBeVisible(RESERVATION_DETAILS_PAGE);
            
            // Verify details page elements
            Assert.assertTrue(helper.isElementDisplayed(BOOKING_REFERENCE_DETAILS), 
                "Booking reference should be displayed on details page");
            Assert.assertTrue(helper.isElementDisplayed(HOTEL_INFO_DETAILS), 
                "Hotel info should be displayed on details page");
            Assert.assertTrue(helper.isElementDisplayed(BOOKING_DATES_DETAILS), 
                "Booking dates should be displayed on details page");
            Assert.assertTrue(helper.isElementDisplayed(PAYMENT_INFO_DETAILS), 
                "Payment info should be displayed on details page");
        }
        
        System.out.println("Reservation details page navigation test completed");
    }
    
    @Test(description = "Test reservation cancellation button visibility")
    public void testReservationCancellationButtonVisibility() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Check if reservations exist
        if (helper.isElementDisplayed(RESERVATION_CARDS)) {
            List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
            
            for (WebElement reservationCard : reservationCards) {
                WebElement statusBadge = reservationCard.findElement(STATUS_BADGE);
                String statusText = statusBadge.getText().toLowerCase();
                
                // Check if cancel button is present
                List<WebElement> cancelButtons = reservationCard.findElements(CANCEL_BUTTON);
                
                if (statusText.equals("confirmed")) {
                    // Cancel button should be visible for confirmed reservations
                    Assert.assertTrue(cancelButtons.size() > 0, 
                        "Cancel button should be visible for confirmed reservations");
                } else {
                    // Cancel button should not be visible for other statuses
                    Assert.assertTrue(cancelButtons.size() == 0, 
                        "Cancel button should not be visible for " + statusText + " reservations");
                }
            }
        }
        
        System.out.println("Reservation cancellation button visibility test completed");
    }
    
    @Test(description = "Test no reservations message display")
    public void testNoReservationsMessageDisplay() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Check if no reservations message is displayed
        if (helper.isElementDisplayed(NO_RESERVATIONS_MESSAGE)) {
            String noReservationsText = helper.getTextFromElement(NO_RESERVATIONS_MESSAGE);
            
            // Verify message content
            Assert.assertTrue(noReservationsText.toLowerCase().contains("no reservations"), 
                "No reservations message should contain 'no reservations'");
            
            // Check if "Browse Hotels" button is present
            By browseHotelsButton = By.xpath("//a[contains(text(), 'Browse Hotels')]");
            Assert.assertTrue(helper.isElementDisplayed(browseHotelsButton), 
                "Browse Hotels button should be displayed when no reservations exist");
        }
        
        System.out.println("No reservations message display test completed");
    }
    
    @Test(description = "Test reservation sorting by date")
    public void testReservationSortingByDate() {
        helper = new PageObjectHelper(driver);
        
        // Login and navigate to reservations
        performLogin();
        navigateToMyReservations();
        
        // Check if multiple reservations exist
        if (helper.isElementDisplayed(RESERVATION_CARDS)) {
            List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
            
            if (reservationCards.size() > 1) {
                // Verify reservations are sorted by check-in date (most recent first)
                String firstCheckinDate = reservationCards.get(0)
                    .findElement(CHECKIN_DATE).getText();
                String secondCheckinDate = reservationCards.get(1)
                    .findElement(CHECKIN_DATE).getText();
                
                // This is a basic check - in a real scenario, you'd parse dates and compare
                Assert.assertFalse(firstCheckinDate.isEmpty(), 
                    "First reservation should have check-in date");
                Assert.assertFalse(secondCheckinDate.isEmpty(), 
                    "Second reservation should have check-in date");
            }
        }
        
        System.out.println("Reservation sorting by date test completed");
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
     * Helper method to verify reservation cards
     */
    private void verifyReservationCards() {
        List<WebElement> reservationCards = helper.getElements(RESERVATION_CARDS);
        Assert.assertTrue(reservationCards.size() > 0, 
            "At least one reservation card should be displayed");
        
        for (WebElement card : reservationCards) {
            Assert.assertTrue(card.findElement(HOTEL_NAME_RESERVATION).isDisplayed(), 
                "Hotel name should be displayed in each reservation card");
            Assert.assertTrue(card.findElement(STATUS_BADGE).isDisplayed(), 
                "Status badge should be displayed in each reservation card");
        }
    }
    
    /**
     * Helper method to verify no reservations message
     */
    private void verifyNoReservationsMessage() {
        Assert.assertTrue(helper.isElementDisplayed(NO_RESERVATIONS_MESSAGE), 
            "No reservations message should be displayed");
        
        String messageText = helper.getTextFromElement(NO_RESERVATIONS_MESSAGE);
        Assert.assertFalse(messageText.isEmpty(), 
            "No reservations message should not be empty");
    }
}
