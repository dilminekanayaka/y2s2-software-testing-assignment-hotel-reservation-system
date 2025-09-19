# 🚀 **Running Tests on Localhost**

This guide explains how to run the fixed UserRegistrationTest on localhost.

## 🔧 **Setup Requirements**

### **1. Start XAMPP**

1. **Start Apache** - Your web server
2. **Start MySQL** - Your database
3. **Verify** - Go to http://localhost/SereneTripsLK

### **2. Database Setup**

Make sure your database is properly configured in `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'serenetripslk');
```

### **3. Test Configuration**

The test is already configured to use localhost:

```properties
base.url=http://localhost/SereneTripsLK
```

## 🎯 **Fixed Test Cases**

### **1. Successful Registration Test**

- **What it tests**: Complete registration with valid data
- **Expected result**: ✅ Account created successfully
- **Error handling**: Shows detailed success/error messages

### **2. Invalid Email Test**

- **What it tests**: Registration with invalid email format
- **Expected result**: ✅ Shows "Invalid email format" error
- **Error handling**: Both server-side and client-side validation

### **3. Password Mismatch Test**

- **What it tests**: Registration with different passwords
- **Expected result**: ✅ Shows "Passwords do not match" error
- **Error handling**: Real-time validation

### **4. Missing Fields Test**

- **What it tests**: Registration with empty required fields
- **Expected result**: ✅ Shows "All fields are required" error
- **Error handling**: Form validation prevents submission

### **5. Existing Email Test**

- **What it tests**: Registration with already used email
- **Expected result**: ✅ Shows "Email already registered" error
- **Error handling**: Database validation

## 🚀 **Running the Tests**

### **Method 1: IntelliJ IDEA**

1. **Open** IntelliJ IDEA
2. **Open** the `testing` project folder
3. **Wait** for Maven dependencies to download
4. **Navigate** to `UserRegistrationTest.java`
5. **Right-click** on any test method
6. **Select** "Run 'testSuccessfulUserRegistration()'"

### **Method 2: Command Line**

```bash
cd testing
mvn test -Dtest=UserRegistrationTest
```

### **Method 3: Run Individual Tests**

```bash
# Run only successful registration test
mvn test -Dtest=UserRegistrationTest#testSuccessfulUserRegistration

# Run only invalid email test
mvn test -Dtest=UserRegistrationTest#testRegistrationWithInvalidEmail

# Run only password mismatch test
mvn test -Dtest=UserRegistrationTest#testRegistrationWithPasswordMismatch
```

## 📊 **Expected Test Output**

### **Successful Registration**

```
✅ Registration successful!
Success message: Registration successful! Your account has been created. Please click here to login and start booking your perfect stay.
```

### **Invalid Email**

```
✅ Invalid email test passed - Error: Invalid email format
```

### **Password Mismatch**

```
✅ Password mismatch test passed - Error: Passwords do not match
```

### **Missing Fields**

```
✅ Missing fields test passed - Error: All fields are required
```

### **Existing Email**

```
✅ Existing email test passed - Error: Email already registered
```

## 🔍 **Debugging Tips**

### **If Tests Still Fail**

1. **Check XAMPP Status**

   - Apache is running
   - MySQL is running
   - Website accessible at http://localhost/SereneTripsLK

2. **Check Database Connection**

   - Database exists
   - Users table exists
   - Proper permissions

3. **Check Form Elements**

   - Registration page loads correctly
   - Form fields have correct IDs
   - Submit button works

4. **Check Error Messages**
   - Server-side validation works
   - Client-side validation works
   - Error messages display correctly

### **Common Issues**

1. **"Connection refused"** - XAMPP not running
2. **"Element not found"** - Wrong page URL or form structure
3. **"No error message"** - Validation not working properly
4. **"Database error"** - MySQL connection issues

## 🎉 **Key Improvements Made**

### **1. Enhanced Error Handling**

- Try-catch blocks in all test methods
- Detailed console output for debugging
- Graceful failure handling

### **2. Better Form Validation**

- Client-side JavaScript validation
- Server-side PHP validation
- Real-time error messages

### **3. Improved Test Data**

- Unique email generation for each test
- Proper form field clearing
- Realistic test scenarios

### **4. Localhost Configuration**

- Updated base URL to localhost
- Proper XAMPP integration
- Local database testing

### **5. Enhanced Debugging**

- Console output for each test step
- Error message validation
- Success/failure indicators

## 🚀 **Quick Start**

1. **Start XAMPP** (Apache + MySQL)
2. **Open IntelliJ IDEA**
3. **Open testing project**
4. **Run UserRegistrationTest**
5. **Watch tests execute** with detailed output

The tests should now work perfectly on localhost! 🎯
