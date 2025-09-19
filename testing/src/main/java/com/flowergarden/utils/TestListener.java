package com.flowergarden.utils;

import org.testng.ITestListener;
import org.testng.ITestResult;
import org.testng.Reporter;

/**
 * Test listener for handling test execution events
 * and generating reports for the Flower Garden Hotel Booking test suite.
 */
public class TestListener implements ITestListener {
    
    @Override
    public void onTestStart(ITestResult result) {
        System.out.println("Starting test: " + result.getMethod().getMethodName());
        Reporter.log("Starting test: " + result.getMethod().getMethodName());
    }
    
    @Override
    public void onTestSuccess(ITestResult result) {
        System.out.println("Test passed: " + result.getMethod().getMethodName());
        Reporter.log("Test passed: " + result.getMethod().getMethodName());
        
        // Take screenshot on success if configured
        if (shouldTakeScreenshotOnSuccess()) {
            takeScreenshot(result, "SUCCESS");
        }
    }
    
    @Override
    public void onTestFailure(ITestResult result) {
        System.out.println("Test failed: " + result.getMethod().getMethodName());
        Reporter.log("Test failed: " + result.getMethod().getMethodName());
        
        // Take screenshot on failure if configured
        if (shouldTakeScreenshotOnFailure()) {
            takeScreenshot(result, "FAILURE");
        }
        
        // Log exception details
        if (result.getThrowable() != null) {
            System.err.println("Exception: " + result.getThrowable().getMessage());
            Reporter.log("Exception: " + result.getThrowable().getMessage());
        }
    }
    
    @Override
    public void onTestSkipped(ITestResult result) {
        System.out.println("Test skipped: " + result.getMethod().getMethodName());
        Reporter.log("Test skipped: " + result.getMethod().getMethodName());
    }
    
    @Override
    public void onTestFailedButWithinSuccessPercentage(ITestResult result) {
        System.out.println("Test failed but within success percentage: " + 
                         result.getMethod().getMethodName());
        Reporter.log("Test failed but within success percentage: " + 
                   result.getMethod().getMethodName());
    }
    
    /**
     * Check if screenshots should be taken on test failure
     */
    private boolean shouldTakeScreenshotOnFailure() {
        String screenshotOnFailure = System.getProperty("screenshot.on.failure", "true");
        return Boolean.parseBoolean(screenshotOnFailure);
    }
    
    /**
     * Check if screenshots should be taken on test success
     */
    private boolean shouldTakeScreenshotOnSuccess() {
        String screenshotOnSuccess = System.getProperty("screenshot.on.success", "false");
        return Boolean.parseBoolean(screenshotOnSuccess);
    }
    
    /**
     * Take screenshot for test result
     */
    private void takeScreenshot(ITestResult result, String status) {
        try {
            String testName = result.getMethod().getMethodName();
            String className = result.getTestClass().getName();
            String timestamp = java.time.LocalDateTime.now()
                .format(java.time.format.DateTimeFormatter.ofPattern("yyyyMMdd_HHmmss"));
            
            String fileName = className + "_" + testName + "_" + status + "_" + timestamp;
            
            // This would need to be implemented with access to WebDriver
            // For now, we'll just log the intention
            System.out.println("Screenshot would be saved as: " + fileName + ".png");
            Reporter.log("Screenshot would be saved as: " + fileName + ".png");
            
        } catch (Exception e) {
            System.err.println("Failed to take screenshot: " + e.getMessage());
        }
    }
}
