# 🔧 **User Registration Test Debug Guide**

This guide helps identify and fix issues with the user registration test.

## 🎯 **Common Issues and Solutions**

### **Issue 1: Registration Link Not Found**

**Problem**: Test can't find the registration link
**Solution**: Updated locator to use `href` attribute:

```java
private static final By REGISTER_LINK = By.xpath("//a[@href='register.php']");
```

### **Issue 2: Form Fields Not Getting Filled**

**Problem**: Input values are not being entered correctly
**Solution**: Use `clearAndSendKeys()` method:

```java
helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
```

### **Issue 3: Form Submission Not Working**

**Problem**: Button click doesn't submit the form
**Solution**: Wait for button to be clickable before clicking:

```java
helper.waitForElementClickability(REGISTER_BUTTON);
helper.clickElement(REGISTER_BUTTON);
```

### **Issue 4: Success Message Not Detected**

**Problem**: Test fails to find success message
**Solution**: Wait for success message with timeout:

```java
helper.waitForElementVisibility(SUCCESS_MESSAGE, 10);
```

## 🔍 **Debugging Steps**

### **Step 1: Check Website Accessibility**

1. Open browser manually
2. Navigate to: https://flowergarden.infinityfree.me
3. Click on "register" link
4. Verify registration page loads

### **Step 2: Test Form Manually**

1. Fill out the registration form manually
2. Submit the form
3. Check if account is created successfully
4. Note any error messages

### **Step 3: Check Form Elements**

1. Right-click on form fields
2. Inspect element to verify IDs match test locators
3. Check if form has proper `action` and `method` attributes

### **Step 4: Run Test with Debug Output**

```java
@Test(description = "Debug registration test")
public void debugRegistrationTest() {
    helper = new PageObjectHelper(driver);

    // Navigate to registration page
    System.out.println("Navigating to registration page...");
    helper.clickElement(REGISTER_LINK);
    helper.waitForTitleToContain("Register");
    System.out.println("Current URL: " + driver.getCurrentUrl());
    System.out.println("Page title: " + driver.getTitle());

    // Check if form elements are present
    System.out.println("Checking form elements...");
    System.out.println("First name field present: " + helper.isElementDisplayed(FIRST_NAME_INPUT));
    System.out.println("Email field present: " + helper.isElementDisplayed(EMAIL_INPUT));
    System.out.println("Password field present: " + helper.isElementDisplayed(PASSWORD_INPUT));
    System.out.println("Register button present: " + helper.isElementDisplayed(REGISTER_BUTTON));

    // Fill form with debug output
    String testEmail = "debug" + System.currentTimeMillis() + "@example.com";
    System.out.println("Using test email: " + testEmail);

    helper.clearAndSendKeys(FIRST_NAME_INPUT, "Debug");
    helper.clearAndSendKeys(LAST_NAME_INPUT, "Test");
    helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
    helper.clearAndSendKeys(PASSWORD_INPUT, "Debug123!");
    helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, "Debug123!");

    System.out.println("Form filled, submitting...");
    helper.clickElement(REGISTER_BUTTON);

    // Wait and check for success/error
    try {
        helper.waitForElementVisibility(SUCCESS_MESSAGE, 5);
        System.out.println("Success message found: " + helper.getTextFromElement(SUCCESS_MESSAGE));
    } catch (Exception e) {
        System.out.println("No success message found, checking for error...");
        if (helper.isElementDisplayed(ERROR_MESSAGE)) {
            System.out.println("Error message: " + helper.getTextFromElement(ERROR_MESSAGE));
        }
    }
}
```

## 🛠️ **Enhanced Test Methods**

### **Improved Success Test**

```java
@Test(description = "Test successful user registration with enhanced debugging")
public void testSuccessfulUserRegistrationEnhanced() {
    helper = new PageObjectHelper(driver);

    // Navigate to registration page
    helper.clickElement(REGISTER_LINK);
    helper.waitForTitleToContain("Register");

    // Generate unique test data
    String timestamp = String.valueOf(System.currentTimeMillis());
    String testEmail = "testuser" + timestamp + "@example.com";
    String testPassword = "TestPassword123!";

    // Fill registration form with all required fields
    helper.clearAndSendKeys(FIRST_NAME_INPUT, "Test");
    helper.clearAndSendKeys(LAST_NAME_INPUT, "User");
    helper.clearAndSendKeys(EMAIL_INPUT, testEmail);
    helper.clearAndSendKeys(PHONE_INPUT, "+1234567890");
    helper.clearAndSendKeys(DATE_OF_BIRTH_INPUT, "1990-01-01");
    helper.clearAndSendKeys(ADDRESS_INPUT, "123 Test Street");
    helper.clearAndSendKeys(PASSWORD_INPUT, testPassword);
    helper.clearAndSendKeys(CONFIRM_PASSWORD_INPUT, testPassword);

    // Wait for form to be ready
    helper.waitForElementClickability(REGISTER_BUTTON);

    // Submit registration form
    helper.clickElement(REGISTER_BUTTON);

    // Wait for response (success or error)
    try {
        // Wait for success message
        helper.waitForElementVisibility(SUCCESS_MESSAGE, 10);
        Assert.assertTrue(helper.isElementDisplayed(SUCCESS_MESSAGE),
            "Registration success message should be displayed");

        String successText = helper.getTextFromElement(SUCCESS_MESSAGE);
        Assert.assertTrue(successText.contains("successful") || successText.contains("created"),
            "Success message should contain 'successful' or 'created'");

        System.out.println("✅ Registration successful!");
        System.out.println("Success message: " + successText);

    } catch (Exception e) {
        // Check for error message
        if (helper.isElementDisplayed(ERROR_MESSAGE)) {
            String errorText = helper.getTextFromElement(ERROR_MESSAGE);
            System.out.println("❌ Registration failed with error: " + errorText);
            Assert.fail("Registration failed: " + errorText);
        } else {
            System.out.println("❌ No success or error message found");
            Assert.fail("Registration test failed - no response message");
        }
    }
}
```

## 🔧 **Troubleshooting Checklist**

### **Before Running Tests**

- [ ] Website is accessible: https://flowergarden.infinityfree.me
- [ ] Registration page loads correctly
- [ ] Form elements have correct IDs
- [ ] Database connection is working
- [ ] All required dependencies are installed

### **During Test Execution**

- [ ] Browser opens successfully
- [ ] Navigation to registration page works
- [ ] Form fields are filled correctly
- [ ] Submit button is clicked
- [ ] Page responds to form submission
- [ ] Success/error messages are displayed

### **Common Error Messages**

1. **"Email already registered"** - Use unique email addresses
2. **"Passwords do not match"** - Ensure password fields match
3. **"All fields are required"** - Fill all required fields
4. **"Invalid email format"** - Use valid email format
5. **"Password must be at least 6 characters"** - Use longer passwords

## 📊 **Test Data Requirements**

### **Valid Test Data**

- **First Name**: Any non-empty string
- **Last Name**: Any non-empty string
- **Email**: Unique email format (e.g., test123@example.com)
- **Phone**: Optional, any format
- **Date of Birth**: Optional, YYYY-MM-DD format
- **Address**: Optional, any string
- **Password**: At least 6 characters
- **Confirm Password**: Must match password

### **Invalid Test Data Examples**

- **Email**: "invalid-email", "test@", "@example.com"
- **Password Mismatch**: "password123" vs "password456"
- **Short Password**: "123"
- **Empty Required Fields**: Leave first_name, last_name, email, password empty

## 🚀 **Running the Fixed Tests**

### **In IntelliJ IDEA**

1. Open the `testing` project
2. Navigate to `UserRegistrationTest.java`
3. Right-click on the test method
4. Select "Run 'testSuccessfulUserRegistration()'"

### **Expected Output**

```
✅ Registration successful!
Success message: Registration successful! Your account has been created. Please click here to login and start booking your perfect stay.
```

## 🎯 **Key Improvements Made**

1. **Better Locators**: Using `href` attribute for registration link
2. **Enhanced Form Filling**: Using `clearAndSendKeys()` method
3. **Improved Waiting**: Custom timeouts for elements
4. **Better Error Handling**: Try-catch blocks for success/error messages
5. **Debug Output**: Console logging for troubleshooting
6. **Robust Assertions**: Multiple conditions for success validation

The registration test should now work correctly and create accounts successfully! 🎉
