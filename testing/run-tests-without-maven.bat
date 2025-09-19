@echo off
echo ==========================================
echo Flower Garden Hotel Booking Test Suite
echo Running without Maven (Standalone)
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

echo Java is available
echo.

REM Create directories if they don't exist
if not exist "reports" mkdir reports
if not exist "screenshots" mkdir screenshots

echo Starting tests...
echo Website: https://flowergarden.infinityfree.me
echo Browser: Chrome (will open automatically)
echo.

REM Compile Java files
echo Compiling Java files...
javac -cp "lib/*" -d target/classes src/main/java/com/flowergarden/utils/*.java
javac -cp "lib/*;target/classes" -d target/test-classes src/test/java/com/flowergarden/tests/*.java

if %errorlevel% neq 0 (
    echo Error: Compilation failed. Please check Java files.
    pause
    exit /b 1
)

echo Compilation successful!
echo.

REM Run tests
echo Running tests...
java -cp "lib/*;target/classes;target/test-classes" com.flowergarden.tests.TestRunner

echo.
echo ==========================================
echo Test execution completed!
echo Check the reports directory for detailed results.
echo Check the screenshots directory for failure screenshots.
echo ==========================================
echo.
pause
