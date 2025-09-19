@echo off
echo ==========================================
echo Setting up Standalone Test Environment
echo (Without Maven)
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

REM Create necessary directories
echo Creating directories...
if not exist "lib" mkdir lib
if not exist "target\classes" mkdir target\classes
if not exist "target\test-classes" mkdir target\test-classes
if not exist "reports" mkdir reports
if not exist "screenshots" mkdir screenshots

echo Directories created successfully!
echo.

echo ==========================================
echo Next Steps:
echo ==========================================
echo 1. Download required JAR files to the 'lib' directory:
echo    - selenium-java-4.15.0.jar
echo    - testng-7.8.0.jar
echo    - webdrivermanager-5.6.2.jar
echo    - commons-lang3-3.13.0.jar
echo    - jackson-databind-2.16.0.jar
echo    - slf4j-simple-2.0.9.jar
echo.
echo 2. Download from:
echo    https://mvnrepository.com/artifact/org.seleniumhq.selenium/selenium-java
echo    https://mvnrepository.com/artifact/org.testng/testng
echo    https://mvnrepository.com/artifact/io.github.bonigarcia/webdrivermanager
echo    https://mvnrepository.com/artifact/org.apache.commons/commons-lang3
echo    https://mvnrepository.com/artifact/com.fasterxml.jackson.core/jackson-databind
echo    https://mvnrepository.com/artifact/org.slf4j/slf4j-simple
echo.
echo 3. After downloading JARs, run: run-tests-without-maven.bat
echo ==========================================
echo.
pause
