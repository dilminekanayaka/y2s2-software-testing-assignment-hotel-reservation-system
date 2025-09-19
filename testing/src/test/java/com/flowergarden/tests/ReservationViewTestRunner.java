package com.flowergarden.tests;

import org.testng.TestNG;
import org.testng.xml.XmlSuite;

import java.util.ArrayList;
import java.util.List;

/**
 * Individual test runner for Reservation View tests
 * This class runs only the Reservation View test suite independently
 */
public class ReservationViewTestRunner {
    
    public static void main(String[] args) {
        TestNG testNG = new TestNG();
        
        // Set test classes - only Reservation View tests
        List<Class<?>> testClasses = new ArrayList<>();
        testClasses.add(ReservationViewTest.class);
        
        testNG.setTestClasses(testClasses.toArray(new Class[0]));
        
        // Set output directory for reports
        testNG.setOutputDirectory("./reports/ReservationView");
        
        // Set parallel execution
        testNG.setParallel(XmlSuite.ParallelMode.METHODS);
        testNG.setThreadCount(1);
        
        // Run tests
        System.out.println("Starting Reservation View Test Suite...");
        System.out.println("Testing website: https://flowergarden.infinityfree.me");
        System.out.println("Browser: Chrome (automatically opened)");
        System.out.println("==========================================");
        
        testNG.run();
        
        System.out.println("==========================================");
        System.out.println("Reservation View Test execution completed!");
        System.out.println("Check reports/ReservationView directory for results.");
    }
}
