@echo off
echo ==========================================
echo Flower Garden Hotel Booking Test Suite
echo Hotel Booking Tests
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
if not exist "reports\HotelBooking" mkdir reports\HotelBooking
if not exist "screenshots" mkdir screenshots

echo Starting Hotel Booking Tests...
echo Website: https://flowergarden.infinityfree.me
echo Browser: Chrome (will open automatically)
echo.

REM Run Hotel Booking tests only
mvn test -Dtest=HotelBookingTest

echo.
echo ==========================================
echo Hotel Booking Test execution completed!
echo Check reports\HotelBooking directory for detailed results.
echo Check screenshots directory for failure screenshots.
echo ==========================================
echo.
pause
