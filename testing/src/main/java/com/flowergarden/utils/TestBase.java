package com.flowergarden.utils;

import io.github.bonigarcia.wdm.WebDriverManager;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.FirefoxOptions;
import org.openqa.selenium.edge.EdgeDriver;
import org.openqa.selenium.edge.EdgeOptions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.BeforeSuite;

import java.io.FileInputStream;
import java.io.IOException;
import java.time.Duration;
import java.util.Properties;

/**
 * Base test class providing common setup and teardown functionality
 * for all test classes in the Flower Garden Hotel Booking test suite.
 */
public class TestBase {
    
    protected WebDriver driver;
    protected WebDriverWait wait;
    protected Properties config;
    protected String baseUrl;
    
    @BeforeSuite
    public void setupSuite() {
        // Load configuration properties
        loadConfig();
        
        // Setup WebDriverManager
        setupWebDriverManager();
    }
    
    @BeforeMethod
    public void setupTest() {
        // Initialize WebDriver
        initializeDriver();
        
        // Setup WebDriverWait
        wait = new WebDriverWait(driver, Duration.ofSeconds(getExplicitWait()));
        
        // Navigate to base URL
        driver.get(baseUrl);
        
        // Set implicit wait
        driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(getImplicitWait()));
        
        // Maximize window if configured
        if (isWindowMaximize()) {
            driver.manage().window().maximize();
        }
    }
    
    @AfterMethod
    public void teardownTest() {
        if (driver != null) {
            driver.quit();
        }
    }
    
    /**
     * Load configuration properties from config.properties file
     */
    private void loadConfig() {
        config = new Properties();
        try {
            FileInputStream input = new FileInputStream("src/test/resources/config.properties");
            config.load(input);
            baseUrl = config.getProperty("base.url");
        } catch (IOException e) {
            throw new RuntimeException("Failed to load configuration file", e);
        }
    }
    
    /**
     * Setup WebDriverManager based on browser configuration
     */
    private void setupWebDriverManager() {
        String browser = config.getProperty("browser", "chrome").toLowerCase();
        
        switch (browser) {
            case "chrome":
                WebDriverManager.chromedriver().setup();
                break;
            case "firefox":
                WebDriverManager.firefoxdriver().setup();
                break;
            case "edge":
                WebDriverManager.edgedriver().setup();
                break;
            default:
                throw new IllegalArgumentException("Unsupported browser: " + browser);
        }
    }
    
    /**
     * Initialize WebDriver based on browser configuration
     */
    private void initializeDriver() {
        String browser = config.getProperty("browser", "chrome").toLowerCase();
        boolean headless = Boolean.parseBoolean(config.getProperty("headless", "false"));
        
        switch (browser) {
            case "chrome":
                ChromeOptions chromeOptions = new ChromeOptions();
                if (headless) {
                    chromeOptions.addArguments("--headless");
                }
                chromeOptions.addArguments("--no-sandbox");
                chromeOptions.addArguments("--disable-dev-shm-usage");
                chromeOptions.addArguments("--disable-gpu");
                driver = new ChromeDriver(chromeOptions);
                break;
                
            case "firefox":
                FirefoxOptions firefoxOptions = new FirefoxOptions();
                if (headless) {
                    firefoxOptions.addArguments("--headless");
                }
                driver = new FirefoxDriver(firefoxOptions);
                break;
                
            case "edge":
                EdgeOptions edgeOptions = new EdgeOptions();
                if (headless) {
                    edgeOptions.addArguments("--headless");
                }
                driver = new EdgeDriver(edgeOptions);
                break;
                
            default:
                throw new IllegalArgumentException("Unsupported browser: " + browser);
        }
    }
    
    // Configuration getter methods
    public int getImplicitWait() {
        return Integer.parseInt(config.getProperty("implicit.wait", "10"));
    }
    
    public int getExplicitWait() {
        return Integer.parseInt(config.getProperty("explicit.wait", "20"));
    }
    
    public boolean isWindowMaximize() {
        return Boolean.parseBoolean(config.getProperty("window.maximize", "true"));
    }
    
    public String getProperty(String key) {
        return config.getProperty(key);
    }
    
    public String getProperty(String key, String defaultValue) {
        return config.getProperty(key, defaultValue);
    }
}
