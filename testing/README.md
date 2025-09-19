# Flower Garden Hotel Booking - Selenium Test Suite

This directory contains comprehensive Selenium WebDriver test scripts for the Flower Garden Hotel Booking website (https://flowergarden.infinityfree.me/).

## Test Coverage

The test suite covers 5 key user flows:

1. **User Registration and Login** (`UserRegistrationTest.java`, `UserLoginTest.java`)

   - User registration with valid data
   - Registration validation (invalid email, password mismatch, missing fields)
   - User login with valid credentials
   - Login validation (invalid credentials, empty fields)
   - Remember me functionality
   - Logout functionality

2. **Searching for Hotel Details** (`HotelSearchTest.java`)

   - Hotel search functionality
   - Hotel search by city
   - Hotel details view
   - Hotel rooms view
   - Room booking button visibility
   - Hotel amenities display
   - Hotel rating display

3. **Making a Booking** (`HotelBookingTest.java`)

   - Complete hotel booking flow
   - Booking with special requests
   - Booking with coupon codes
   - Payment form validation
   - Booking summary display
   - Error handling for invalid payment details

4. **Viewing Bookings** (`ReservationViewTest.java`)

   - Viewing reservations for logged-in users
   - Reservation card details display
   - Reservation status display
   - Reservation details page navigation
   - Cancellation button visibility
   - No reservations message display
   - Reservation sorting by date

5. **Canceling Bookings** (`ReservationCancellationTest.java`)
   - Successful reservation cancellation
   - Cancellation page navigation
   - Cancellation confirmation dialog
   - Cancellation with already cancelled reservations
   - Reservation details accuracy on cancellation page
   - Refund information display
   - Cancellation page accessibility

## Project Structure

```
testing/
├── pom.xml                           # Maven configuration
├── README.md                         # This file
├── src/
│   ├── main/java/com/flowergarden/utils/
│   │   ├── TestBase.java            # Base test class with setup/teardown
│   │   ├── PageObjectHelper.java    # Common page object operations
│   │   └── TestListener.java        # Test execution listener
│   └── test/
│       ├── java/com/flowergarden/tests/
│       │   ├── UserRegistrationTest.java
│       │   ├── UserLoginTest.java
│       │   ├── HotelSearchTest.java
│       │   ├── HotelBookingTest.java
│       │   ├── ReservationViewTest.java
│       │   ├── ReservationCancellationTest.java
│       │   └── TestRunner.java      # Programmatic test runner
│       └── resources/
│           ├── testng.xml           # TestNG suite configuration
│           └── config.properties    # Test configuration
├── reports/                          # Test execution reports
└── screenshots/                      # Screenshots on test failures
```

## Prerequisites

- Java 11 or higher
- Maven 3.6 or higher
- Chrome browser (or Firefox/Edge)
- Internet connection

## Setup and Execution

### 1. Install Dependencies

```bash
cd testing
mvn clean install
```

### 2. Configure Test Parameters

Edit `src/test/resources/config.properties` to update:

- Base URL (if different from https://flowergarden.infinityfree.me)
- Test user credentials
- Browser preferences
- Timeout settings

### 3. Run Tests Separately (Recommended)

#### 🎯 **Individual Test Suite Execution**

Each of the 5 key user flows can be run independently:

##### **Option A: Using Batch Files (Windows)**

```bash
# Run User Registration Tests
run-user-registration-tests.bat

# Run User Login Tests
run-user-login-tests.bat

# Run Hotel Search Tests
run-hotel-search-tests.bat

# Run Hotel Booking Tests
run-hotel-booking-tests.bat

# Run Reservation View Tests
run-reservation-view-tests.bat

# Run Reservation Cancellation Tests
run-reservation-cancellation-tests.bat
```

##### **Option B: Using Shell Scripts (Linux/Mac)**

```bash
# Run User Registration Tests
./run-user-registration-tests.sh

# Run User Login Tests
./run-user-login-tests.sh

# Run Hotel Search Tests
./run-hotel-search-tests.sh

# Run Hotel Booking Tests
./run-hotel-booking-tests.sh

# Run Reservation View Tests
./run-reservation-view-tests.sh

# Run Reservation Cancellation Tests
./run-reservation-cancellation-tests.sh
```

##### **Option C: Using Maven Commands**

```bash
# Run User Registration Tests
mvn test -Dtest=UserRegistrationTest

# Run User Login Tests
mvn test -Dtest=UserLoginTest

# Run Hotel Search Tests
mvn test -Dtest=HotelSearchTest

# Run Hotel Booking Tests
mvn test -Dtest=HotelBookingTest

# Run Reservation View Tests
mvn test -Dtest=ReservationViewTest

# Run Reservation Cancellation Tests
mvn test -Dtest=ReservationCancellationTest
```

##### **Option D: Using Individual Test Runners**

```bash
# Run User Registration Tests
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.UserRegistrationTestRunner"

# Run User Login Tests
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.UserLoginTestRunner"

# Run Hotel Search Tests
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.HotelSearchTestRunner"

# Run Hotel Booking Tests
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.HotelBookingTestRunner"

# Run Reservation View Tests
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.ReservationViewTestRunner"

# Run Reservation Cancellation Tests
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.ReservationCancellationTestRunner"
```

### 4. Run All Tests Together

#### Option A: Using Maven

```bash
mvn test
```

#### Option B: Using TestNG XML

```bash
mvn test -DsuiteXmlFile=src/test/resources/testng.xml
```

#### Option C: Using TestRunner

```bash
mvn exec:java -Dexec.mainClass="com.flowergarden.tests.TestRunner"
```

### 5. View Results

- Test reports: `./reports/` (organized by test suite)
- Screenshots: `./screenshots/`
- Console output for real-time results

## Configuration Options

### Browser Configuration

```properties
browser=chrome          # chrome, firefox, edge
headless=false         # true for headless execution
window.maximize=true   # maximize browser window
```

### Timeout Settings

```properties
implicit.wait=10        # Implicit wait timeout in seconds
explicit.wait=20        # Explicit wait timeout in seconds
page.load.timeout=30    # Page load timeout in seconds
```

### Test Data

```properties
test.user.email=testuser@example.com
test.user.password=TestPassword123!
test.hotel.city=Colombo
test.checkin.date=2024-02-15
test.checkout.date=2024-02-17
```

## Test Features

### Page Object Model

- Centralized locator management
- Reusable helper methods
- Clean separation of test logic and page interactions

### Robust Wait Strategies

- Explicit waits for dynamic content
- Implicit waits for general elements
- Custom wait conditions for specific scenarios

### Error Handling

- Comprehensive exception handling
- Screenshot capture on failures
- Detailed error reporting

### Cross-Browser Support

- Chrome, Firefox, and Edge support
- Automatic driver management via WebDriverManager
- Headless execution capability

### Parallel Execution

- Configurable parallel test execution
- Thread-safe test implementation
- Optimized execution time

## Troubleshooting

### Common Issues

1. **WebDriver Issues**

   - Ensure browser is installed and up-to-date
   - Check WebDriverManager logs for driver download issues

2. **Test Failures**

   - Verify website is accessible
   - Check test data in config.properties
   - Review screenshots in ./screenshots/ directory

3. **Timeout Issues**
   - Increase timeout values in config.properties
   - Check network connectivity
   - Verify website performance

### Debug Mode

Run tests with debug information:

```bash
mvn test -Dmaven.surefire.debug
```

### Verbose Output

```bash
mvn test -X
```

## Contributing

When adding new tests:

1. Follow the existing naming conventions
2. Use the PageObjectHelper for common operations
3. Add appropriate assertions and error handling
4. Update this README with new test descriptions
5. Ensure tests are independent and can run in parallel

## Support

For issues or questions:

1. Check the test execution logs
2. Review screenshots for visual debugging
3. Verify website functionality manually
4. Check configuration settings
