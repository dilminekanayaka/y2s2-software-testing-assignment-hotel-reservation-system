@echo off
echo ==========================================
echo Flower Garden Hotel Booking Test Suite
echo User Login Tests
echo ==========================================
echo.

REM Check if Java is installed
java -version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Java is not installed or not in PATH
    echo Please install Java 11 or higher and try again
    pause
    exit /b 1
)

REM Check if Maven is installed
mvn -version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Maven is not installed or not in PATH
    echo Please install Maven 3.6 or higher and try again
    pause
    exit /b 1
)

echo Java and Maven are available
echo.

REM Create directories if they don't exist
if not exist "reports\UserLogin" mkdir reports\UserLogin
if not exist "screenshots" mkdir screenshots

echo Starting User Login Tests...
echo Website: https://flowergarden.infinityfree.me
echo Browser: Chrome (will open automatically)
echo.

REM Run User Login tests only
mvn test -Dtest=UserLoginTest

echo.
echo ==========================================
echo User Login Test execution completed!
echo Check reports\UserLogin directory for detailed results.
echo Check screenshots directory for failure screenshots.
echo ==========================================
echo.
pause
