package com.flowergarden.tests;

import org.testng.TestNG;
import org.testng.xml.XmlSuite;

import java.util.ArrayList;
import java.util.List;

/**
 * Individual test runner for User Login tests
 * This class runs only the User Login test suite independently
 */
public class UserLoginTestRunner {
    
    public static void main(String[] args) {
        TestNG testNG = new TestNG();
        
        // Set test classes - only User Login tests
        List<Class<?>> testClasses = new ArrayList<>();
        testClasses.add(UserLoginTest.class);
        
        testNG.setTestClasses(testClasses.toArray(new Class[0]));
        
        // Set output directory for reports
        testNG.setOutputDirectory("./reports/UserLogin");
        
        // Set parallel execution
        testNG.setParallel(XmlSuite.ParallelMode.METHODS);
        testNG.setThreadCount(1);
        
        // Run tests
        System.out.println("Starting User Login Test Suite...");
        System.out.println("Testing website: https://flowergarden.infinityfree.me");
        System.out.println("Browser: Chrome (automatically opened)");
        System.out.println("==========================================");
        
        testNG.run();
        
        System.out.println("==========================================");
        System.out.println("User Login Test execution completed!");
        System.out.println("Check reports/UserLogin directory for results.");
    }
}
