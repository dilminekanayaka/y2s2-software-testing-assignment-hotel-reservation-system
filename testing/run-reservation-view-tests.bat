@echo off
echo ==========================================
echo Flower Garden Hotel Booking Test Suite
echo Reservation View Tests
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
if not exist "reports\ReservationView" mkdir reports\ReservationView
if not exist "screenshots" mkdir screenshots

echo Starting Reservation View Tests...
echo Website: https://flowergarden.infinityfree.me
echo Browser: Chrome (will open automatically)
echo.

REM Run Reservation View tests only
mvn test -Dtest=ReservationViewTest

echo.
echo ==========================================
echo Reservation View Test execution completed!
echo Check reports\ReservationView directory for detailed results.
echo Check screenshots directory for failure screenshots.
echo ==========================================
echo.
pause
