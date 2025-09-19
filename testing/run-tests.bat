@echo off
echo Starting Flower Garden Hotel Booking Test Suite...
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
if not exist "reports" mkdir reports
if not exist "screenshots" mkdir screenshots

echo Running all tests...
mvn clean test

echo.
echo Test execution completed!
echo Check the reports directory for detailed results.
echo Check the screenshots directory for failure screenshots.
echo.
pause
