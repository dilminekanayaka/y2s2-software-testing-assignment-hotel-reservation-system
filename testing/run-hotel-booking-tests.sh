#!/bin/bash

echo "=========================================="
echo "Flower Garden Hotel Booking Test Suite"
echo "Hotel Booking Tests"
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
mkdir -p reports/HotelBooking
mkdir -p screenshots

echo "Starting Hotel Booking Tests..."
echo "Website: https://flowergarden.infinityfree.me"
echo "Browser: Chrome (will open automatically)"
echo

# Run Hotel Booking tests only
mvn test -Dtest=HotelBookingTest

echo
echo "=========================================="
echo "Hotel Booking Test execution completed!"
echo "Check reports/HotelBooking directory for detailed results."
echo "Check screenshots directory for failure screenshots."
echo "=========================================="
echo
