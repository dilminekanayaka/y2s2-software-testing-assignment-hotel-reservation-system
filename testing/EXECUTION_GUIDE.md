# Flower Garden Hotel Booking - Test Execution Guide

## 🚀 Quick Start Guide

This guide shows you how to run each of the 5 key user flow tests separately on your Flower Garden Hotel Booking website (https://flowergarden.infinityfree.me).

## 📋 Prerequisites

Before running any tests, ensure you have:

- ✅ Java 11 or higher installed
- ✅ Maven 3.6 or higher installed
- ✅ Chrome browser installed
- ✅ Internet connection
- ✅ Website accessible at https://flowergarden.infinityfree.me

## 🎯 Individual Test Execution

### 1. User Registration Tests

**What it tests:**

- User registration with valid data
- Registration validation (invalid email, password mismatch, missing fields)
- Registration with existing email

**How to run:**

#### Windows:

```bash
cd testing
run-user-registration-tests.bat
```

#### Linux/Mac:

```bash
cd testing
./run-user-registration-tests.sh
```

#### Maven Command:

```bash
cd testing
mvn test -Dtest=UserRegistrationTest
```

**Expected behavior:**

- Chrome browser opens automatically
- Navigates to https://flowergarden.infinityfree.me
- Tests registration functionality
- Generates report in `reports/UserRegistration/`

---

### 2. User Login Tests

**What it tests:**

- User login with valid credentials
- Login validation (invalid credentials, empty fields)
- Remember me functionality
- Logout functionality
- Login page navigation

**How to run:**

#### Windows:

```bash
cd testing
run-user-login-tests.bat
```

#### Linux/Mac:

```bash
cd testing
./run-user-login-tests.sh
```

#### Maven Command:

```bash
cd testing
mvn test -Dtest=UserLoginTest
```

**Expected behavior:**

- Chrome browser opens automatically
- Navigates to https://flowergarden.infinityfree.me
- Tests login functionality
- Generates report in `reports/UserLogin/`

---

### 3. Hotel Search Tests

**What it tests:**

- Hotel search functionality
- Hotel search by specific city
- Hotel details view
- Hotel rooms view
- Room booking button visibility
- Hotel amenities display
- Hotel rating display

**How to run:**

#### Windows:

```bash
cd testing
run-hotel-search-tests.bat
```

#### Linux/Mac:

```bash
cd testing
./run-hotel-search-tests.sh
```

#### Maven Command:

```bash
cd testing
mvn test -Dtest=HotelSearchTest
```

**Expected behavior:**

- Chrome browser opens automatically
- Navigates to https://flowergarden.infinityfree.me
- Tests hotel search functionality
- Generates report in `reports/HotelSearch/`

---

### 4. Hotel Booking Tests

**What it tests:**

- Complete hotel booking flow
- Booking with special requests
- Booking with coupon codes
- Payment form validation
- Booking summary display
- Error handling for invalid payment details

**How to run:**

#### Windows:

```bash
cd testing
run-hotel-booking-tests.bat
```

#### Linux/Mac:

```bash
cd testing
./run-hotel-booking-tests.sh
```

#### Maven Command:

```bash
cd testing
mvn test -Dtest=HotelBookingTest
```

**Expected behavior:**

- Chrome browser opens automatically
- Navigates to https://flowergarden.infinityfree.me
- Tests complete booking flow (requires login)
- Generates report in `reports/HotelBooking/`

---

### 5. Reservation View Tests

**What it tests:**

- Viewing reservations for logged-in users
- Reservation card details display
- Reservation status display
- Reservation details page navigation
- Cancellation button visibility
- No reservations message display
- Reservation sorting by date

**How to run:**

#### Windows:

```bash
cd testing
run-reservation-view-tests.bat
```

#### Linux/Mac:

```bash
cd testing
./run-reservation-view-tests.sh
```

#### Maven Command:

```bash
cd testing
mvn test -Dtest=ReservationViewTest
```

**Expected behavior:**

- Chrome browser opens automatically
- Navigates to https://flowergarden.infinityfree.me
- Tests reservation viewing functionality (requires login)
- Generates report in `reports/ReservationView/`

---

### 6. Reservation Cancellation Tests

**What it tests:**

- Successful reservation cancellation
- Cancellation page navigation
- Cancellation confirmation dialog
- Cancellation with already cancelled reservations
- Reservation details accuracy on cancellation page
- Refund information display
- Cancellation page accessibility

**How to run:**

#### Windows:

```bash
cd testing
run-reservation-cancellation-tests.bat
```

#### Linux/Mac:

```bash
cd testing
./run-reservation-cancellation-tests.sh
```

#### Maven Command:

```bash
cd testing
mvn test -Dtest=ReservationCancellationTest
```

**Expected behavior:**

- Chrome browser opens automatically
- Navigates to https://flowergarden.infinityfree.me
- Tests reservation cancellation functionality (requires login and existing reservations)
- Generates report in `reports/ReservationCancellation/`

---

## 🔧 Configuration

### Test Data Configuration

Edit `src/test/resources/config.properties` to customize:

```properties
# Base URL
base.url=https://flowergarden.infinityfree.me

# Test user credentials (for login/booking tests)
test.user.email=testuser@example.com
test.user.password=TestPassword123!

# Browser settings
browser=chrome
headless=false
window.maximize=true

# Timeout settings
implicit.wait=10
explicit.wait=20
page.load.timeout=30
```

### Test Dependencies

Some tests require specific conditions:

1. **Login Tests**: Require valid user credentials in config.properties
2. **Booking Tests**: Require login first, then booking functionality
3. **Reservation Tests**: Require existing reservations in the system
4. **Cancellation Tests**: Require confirmed reservations that can be cancelled

## 📊 Test Results

### Report Locations

Each test suite generates reports in separate directories:

```
reports/
├── UserRegistration/          # User registration test results
├── UserLogin/                # User login test results
├── HotelSearch/              # Hotel search test results
├── HotelBooking/             # Hotel booking test results
├── ReservationView/          # Reservation view test results
└── ReservationCancellation/  # Reservation cancellation test results
```

### Screenshots

Failed tests automatically capture screenshots in:

```
screenshots/
├── UserRegistrationTest_*_FAILURE_*.png
├── UserLoginTest_*_FAILURE_*.png
├── HotelSearchTest_*_FAILURE_*.png
├── HotelBookingTest_*_FAILURE_*.png
├── ReservationViewTest_*_FAILURE_*.png
└── ReservationCancellationTest_*_FAILURE_*.png
```

## 🚨 Troubleshooting

### Common Issues

1. **WebDriver Issues**

   - Ensure Chrome browser is installed and up-to-date
   - Check if ChromeDriver is automatically downloaded by WebDriverManager

2. **Test Failures**

   - Verify website is accessible at https://flowergarden.infinityfree.me
   - Check test data in config.properties
   - Review screenshots in ./screenshots/ directory

3. **Timeout Issues**

   - Increase timeout values in config.properties
   - Check network connectivity
   - Verify website performance

4. **Login Test Failures**

   - Ensure test user credentials are correct
   - Verify user account exists in the system

5. **Booking Test Failures**
   - Ensure user is logged in
   - Check if hotels and rooms are available
   - Verify payment test data

### Debug Mode

Run tests with debug information:

```bash
mvn test -Dmaven.surefire.debug -Dtest=TestClassName
```

### Verbose Output

```bash
mvn test -X -Dtest=TestClassName
```

## 📝 Test Execution Order

For comprehensive testing, run tests in this order:

1. **User Registration Tests** (creates test users)
2. **User Login Tests** (verifies login functionality)
3. **Hotel Search Tests** (tests search without login)
4. **Hotel Booking Tests** (requires login)
5. **Reservation View Tests** (requires bookings)
6. **Reservation Cancellation Tests** (requires confirmed reservations)

## 🎉 Success Indicators

A successful test run will show:

- ✅ All test methods pass
- ✅ Chrome browser opens and navigates correctly
- ✅ Website functionality works as expected
- ✅ Reports generated in appropriate directories
- ✅ No screenshots captured (indicating no failures)

## 📞 Support

If you encounter issues:

1. Check the test execution logs
2. Review screenshots for visual debugging
3. Verify website functionality manually
4. Check configuration settings
5. Ensure all prerequisites are met
