package com.flowergarden.tests;

import org.testng.TestNG;
import org.testng.xml.XmlSuite;

import java.util.ArrayList;
import java.util.List;

/**
 * Test runner class for executing the Flower Garden Hotel Booking test suite
 * This class provides programmatic execution of tests without Maven.
 */
public class TestRunner {
    
    public static void main(String[] args) {
        TestNG testNG = new TestNG();
        
        // Set test classes
        List<Class<?>> testClasses = new ArrayList<>();
        testClasses.add(UserRegistrationTest.class);
        testClasses.add(UserLoginTest.class);
        testClasses.add(HotelSearchTest.class);
        testClasses.add(HotelBookingTest.class);
        testClasses.add(ReservationViewTest.class);
        testClasses.add(ReservationCancellationTest.class);
        
        testNG.setTestClasses(testClasses.toArray(new Class[0]));
        
        // Set output directory for reports
        testNG.setOutputDirectory("./reports");
        
        // Set parallel execution
        testNG.setParallel(XmlSuite.ParallelMode.METHODS);
        testNG.setThreadCount(2);
        
        // Run tests
        System.out.println("Starting Flower Garden Hotel Booking Test Suite...");
        testNG.run();
        
        System.out.println("Test execution completed. Check reports directory for results.");
    }
}
