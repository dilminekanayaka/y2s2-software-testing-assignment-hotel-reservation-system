package com.flowergarden.tests;

import org.testng.TestNG;
import org.testng.xml.XmlSuite;

import java.util.ArrayList;
import java.util.List;

/**
 * Individual test runner for Hotel Booking tests
 * This class runs only the Hotel Booking test suite independently
 */
public class HotelBookingTestRunner {
    
    public static void main(String[] args) {
        TestNG testNG = new TestNG();
        
        // Set test classes - only Hotel Booking tests
        List<Class<?>> testClasses = new ArrayList<>();
        testClasses.add(HotelBookingTest.class);
        
        testNG.setTestClasses(testClasses.toArray(new Class[0]));
        
        // Set output directory for reports
        testNG.setOutputDirectory("./reports/HotelBooking");
        
        // Set parallel execution
        testNG.setParallel(XmlSuite.ParallelMode.METHODS);
        testNG.setThreadCount(1);
        
        // Run tests
        System.out.println("Starting Hotel Booking Test Suite...");
        System.out.println("Testing website: https://flowergarden.infinityfree.me");
        System.out.println("Browser: Chrome (automatically opened)");
        System.out.println("==========================================");
        
        testNG.run();
        
        System.out.println("==========================================");
        System.out.println("Hotel Booking Test execution completed!");
        System.out.println("Check reports/HotelBooking directory for results.");
    }
}
