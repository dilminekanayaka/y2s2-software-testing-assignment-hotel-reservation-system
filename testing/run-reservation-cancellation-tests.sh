#!/bin/bash

echo "=========================================="
echo "Flower Garden Hotel Booking Test Suite"
echo "Reservation Cancellation Tests"
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
mkdir -p reports/ReservationCancellation
mkdir -p screenshots

echo "Starting Reservation Cancellation Tests..."
echo "Website: https://flowergarden.infinityfree.me"
echo "Browser: Chrome (will open automatically)"
echo

# Run Reservation Cancellation tests only
mvn test -Dtest=ReservationCancellationTest

echo
echo "=========================================="
echo "Reservation Cancellation Test execution completed!"
echo "Check reports/ReservationCancellation directory for detailed results."
echo "Check screenshots directory for failure screenshots."
echo "=========================================="
echo
