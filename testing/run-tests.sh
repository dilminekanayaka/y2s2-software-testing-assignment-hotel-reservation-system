#!/bin/bash

echo "Starting Flower Garden Hotel Booking Test Suite..."
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
mkdir -p reports
mkdir -p screenshots

echo "Running all tests..."
mvn clean test

echo
echo "Test execution completed!"
echo "Check the reports directory for detailed results."
echo "Check the screenshots directory for failure screenshots."
echo
