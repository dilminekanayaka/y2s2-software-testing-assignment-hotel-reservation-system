package com.flowergarden.tests;

import org.testng.TestNG;
import org.testng.xml.XmlSuite;

import java.util.ArrayList;
import java.util.List;

/**
 * Individual test runner for Reservation Cancellation tests
 * This class runs only the Reservation Cancellation test suite independently
 */
public class ReservationCancellationTestRunner {
    
    public static void main(String[] args) {
        TestNG testNG = new TestNG();
        
        // Set test classes - only Reservation Cancellation tests
        List<Class<?>> testClasses = new ArrayList<>();
        testClasses.add(ReservationCancellationTest.class);
        
        testNG.setTestClasses(testClasses.toArray(new Class[0]));
        
        // Set output directory for reports
        testNG.setOutputDirectory("./reports/ReservationCancellation");
        
        // Set parallel execution
        testNG.setParallel(XmlSuite.ParallelMode.METHODS);
        testNG.setThreadCount(1);
        
        // Run tests
        System.out.println("Starting Reservation Cancellation Test Suite...");
        System.out.println("Testing website: https://flowergarden.infinityfree.me");
        System.out.println("Browser: Chrome (automatically opened)");
        System.out.println("==========================================");
        
        testNG.run();
        
        System.out.println("==========================================");
        System.out.println("Reservation Cancellation Test execution completed!");
        System.out.println("Check reports/ReservationCancellation directory for results.");
    }
}
