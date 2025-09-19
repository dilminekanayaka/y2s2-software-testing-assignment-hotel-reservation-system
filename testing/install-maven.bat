@echo off
echo ==========================================
echo Automatic Maven Installation
echo ==========================================
echo.

REM Check if Java is installed
java -version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Java is not installed or not in PATH
    echo Please install Java 11 or higher first
    pause
    exit /b 1
)

echo Java is available
echo.

REM Create Maven directory
if not exist "C:\apache-maven" mkdir C:\apache-maven

echo Downloading Maven...
echo This may take a few minutes...
echo.

REM Download Maven using PowerShell
powershell -Command "& {Invoke-WebRequest -Uri 'https://dlcdn.apache.org/maven/maven-3/3.9.6/binaries/apache-maven-3.9.6-bin.zip' -OutFile 'C:\apache-maven\maven.zip'}"

if %errorlevel% neq 0 (
    echo Error: Failed to download Maven
    echo Please download manually from: https://maven.apache.org/download.cgi
    pause
    exit /b 1
)

echo Download completed!
echo.

echo Extracting Maven...
powershell -Command "& {Expand-Archive -Path 'C:\apache-maven\maven.zip' -DestinationPath 'C:\apache-maven' -Force}"

if %errorlevel% neq 0 (
    echo Error: Failed to extract Maven
    pause
    exit /b 1
)

echo Extraction completed!
echo.

REM Set environment variables
echo Setting up environment variables...
setx MAVEN_HOME "C:\apache-maven\apache-maven-3.9.6" /M
setx PATH "%PATH%;C:\apache-maven\apache-maven-3.9.6\bin" /M

echo.
echo ==========================================
echo Maven Installation Completed!
echo ==========================================
echo.
echo Please close and reopen Command Prompt, then run:
echo mvn -version
echo.
echo If successful, you can then run the test batch files.
echo ==========================================
echo.
pause
