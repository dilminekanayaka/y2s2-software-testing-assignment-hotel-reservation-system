#!/bin/bash

echo "=========================================="
echo "Flower Garden Hotel Booking Test Suite"
echo "User Login Tests"
echo "=========================================="
echo

# Check if Java is installed
if ! command -v java &> /dev/null; then
    echo "Error: Java is not installed or not in PATH"
    echo "Please install Java 11 or higher and try again"
    exit 1
fi

# Check if Maven is installed
if ! command -v mvn &> /dev/null; then
    echo "Error: Maven is not installed or not in PATH"
    echo "Please install Maven 3.6 or higher and try again"
    exit 1
fi

echo "Java and Maven are available"
echo

# Create directories if they don't exist
mkdir -p reports/UserLogin
mkdir -p screenshots

echo "Starting User Login Tests..."
echo "Website: https://flowergarden.infinityfree.me"
echo "Browser: Chrome (will open automatically)"
echo

# Run User Login tests only
mvn test -Dtest=UserLoginTest

echo
echo "=========================================="
echo "User Login Test execution completed!"
echo "Check reports/UserLogin directory for detailed results."
echo "Check screenshots directory for failure screenshots."
echo "=========================================="
echo
