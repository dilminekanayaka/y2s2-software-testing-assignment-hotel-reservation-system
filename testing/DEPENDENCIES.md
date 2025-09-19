# 📦 **Selenium Test Dependencies**

This document lists all the dependencies included in the `pom.xml` file for the Selenium WebDriver testing project.

## 🎯 **Core Selenium Dependencies**

### **Selenium WebDriver (4.35.0)**

```xml
<dependency>
    <groupId>org.seleniumhq.selenium</groupId>
    <artifactId>selenium-java</artifactId>
    <version>4.35.0</version>
</dependency>
```

- **Purpose**: Main Selenium WebDriver library for browser automation
- **Includes**: WebDriver, WebElement, Actions, Wait, etc.

### **Selenium Grid (4.35.0)**

```xml
<dependency>
    <groupId>org.seleniumhq.selenium</groupId>
    <artifactId>selenium-grid</artifactId>
    <version>4.35.0</version>
</dependency>
```

- **Purpose**: Distributed testing across multiple machines/browsers
- **Use Case**: Parallel test execution, cross-browser testing

### **Selenium Support (4.35.0)**

```xml
<dependency>
    <groupId>org.seleniumhq.selenium</groupId>
    <artifactId>selenium-support</artifactId>
    <version>4.35.0</version>
</dependency>
```

- **Purpose**: Additional utilities and helper classes
- **Includes**: ExpectedConditions, Select, etc.

### **Browser-Specific Drivers**

#### **Chrome Driver (4.35.0)**

```xml
<dependency>
    <groupId>org.seleniumhq.selenium</groupId>
    <artifactId>selenium-chrome-driver</artifactId>
    <version>4.35.0</version>
</dependency>
```

#### **Firefox Driver (4.35.0)**

```xml
<dependency>
    <groupId>org.seleniumhq.selenium</groupId>
    <artifactId>selenium-firefox-driver</artifactId>
    <version>4.35.0</version>
</dependency>
```

#### **Edge Driver (4.35.0)**

```xml
<dependency>
    <groupId>org.seleniumhq.selenium</groupId>
    <artifactId>selenium-edge-driver</artifactId>
    <version>4.35.0</version>
</dependency>
```

## 🧪 **Testing Framework Dependencies**

### **TestNG (7.10.2)**

```xml
<dependency>
    <groupId>org.testng</groupId>
    <artifactId>testng</artifactId>
    <version>7.10.2</version>
</dependency>
```

- **Purpose**: Primary testing framework
- **Features**: Test annotations, data providers, parallel execution, reporting

### **JUnit Jupiter (5.11.0)**

```xml
<dependency>
    <groupId>org.junit.jupiter</groupId>
    <artifactId>junit-jupiter</artifactId>
    <version>5.11.0</version>
    <scope>test</scope>
</dependency>
```

- **Purpose**: Additional testing capabilities
- **Features**: Modern JUnit 5 annotations and assertions

## 🔧 **Driver Management**

### **WebDriverManager (5.8.0)**

```xml
<dependency>
    <groupId>io.github.bonigarcia</groupId>
    <artifactId>webdrivermanager</artifactId>
    <version>5.8.0</version>
</dependency>
```

- **Purpose**: Automatic browser driver management
- **Features**: Downloads and manages ChromeDriver, GeckoDriver, EdgeDriver automatically

## 📚 **Utility Libraries**

### **Apache Commons IO (2.16.1)**

```xml
<dependency>
    <groupId>commons-io</groupId>
    <artifactId>commons-io</artifactId>
    <version>2.16.1</version>
</dependency>
```

- **Purpose**: File operations and I/O utilities
- **Use Case**: Reading configuration files, handling file uploads/downloads

### **Apache Commons Lang (3.17.0)**

```xml
<dependency>
    <groupId>org.apache.commons</groupId>
    <artifactId>commons-lang3</artifactId>
    <version>3.17.0</version>
</dependency>
```

- **Purpose**: String manipulation and common utilities
- **Features**: StringUtils, RandomStringUtils, etc.

## 📄 **JSON Processing**

### **Jackson Databind (2.18.2)**

```xml
<dependency>
    <groupId>com.fasterxml.jackson.core</groupId>
    <artifactId>jackson-databind</artifactId>
    <version>2.18.2</version>
</dependency>
```

- **Purpose**: JSON serialization/deserialization
- **Use Case**: Reading test data from JSON files, API testing

## 📝 **Logging Dependencies**

### **SLF4J API (2.0.16)**

```xml
<dependency>
    <groupId>org.slf4j</groupId>
    <artifactId>slf4j-api</artifactId>
    <version>2.0.16</version>
</dependency>
```

- **Purpose**: Logging facade
- **Features**: Simple logging interface

### **Logback Classic (1.5.6)**

```xml
<dependency>
    <groupId>ch.qos.logback</groupId>
    <artifactId>logback-classic</artifactId>
    <version>1.5.6</version>
</dependency>
```

- **Purpose**: Logging implementation
- **Features**: File logging, console logging, log levels

## 🎭 **Assertion Libraries**

### **Hamcrest (2.2)**

```xml
<dependency>
    <groupId>org.hamcrest</groupId>
    <artifactId>hamcrest</artifactId>
    <version>2.2</version>
    <scope>test</scope>
</dependency>
```

- **Purpose**: Matcher library for assertions
- **Features**: Flexible assertion syntax

### **AssertJ (3.26.0)**

```xml
<dependency>
    <groupId>org.assertj</groupId>
    <artifactId>assertj-core</artifactId>
    <version>3.26.0</version>
    <scope>test</scope>
</dependency>
```

- **Purpose**: Fluent assertion library
- **Features**: Readable assertion syntax, extensive matchers

## 🎪 **Mocking Framework**

### **Mockito (5.12.0)**

```xml
<dependency>
    <groupId>org.mockito</groupId>
    <artifactId>mockito-core</artifactId>
    <version>5.12.0</version>
    <scope>test</scope>
</dependency>
```

- **Purpose**: Mocking framework for unit tests
- **Features**: Mock objects, stubbing, verification

## 🏗️ **Maven Build Plugins**

### **Maven Compiler Plugin (3.13.0)**

- **Purpose**: Compiles Java source code
- **Configuration**: Java 11, UTF-8 encoding

### **Maven Surefire Plugin (3.5.0)**

- **Purpose**: Runs unit tests
- **Configuration**: TestNG XML execution, UTF-8 encoding

### **Maven Failsafe Plugin (3.5.0)**

- **Purpose**: Runs integration tests
- **Configuration**: TestNG XML execution, UTF-8 encoding

### **Exec Maven Plugin (3.1.1)**

- **Purpose**: Executes Java classes
- **Configuration**: TestRunner main class

## 🌐 **Browser Profiles**

### **Chrome Profile (Default)**

```xml
<profile>
    <id>chrome</id>
    <activation>
        <activeByDefault>true</activeByDefault>
    </activation>
    <properties>
        <browser>chrome</browser>
    </properties>
</profile>
```

### **Firefox Profile**

```xml
<profile>
    <id>firefox</id>
    <properties>
        <browser>firefox</browser>
    </properties>
</profile>
```

### **Edge Profile**

```xml
<profile>
    <id>edge</id>
    <properties>
        <browser>edge</browser>
    </properties>
</profile>
```

## 🚀 **Quick Start Commands**

### **Download Dependencies**

```bash
mvn clean compile
```

### **Run All Tests**

```bash
mvn test
```

### **Run Tests with Specific Browser**

```bash
mvn test -Pchrome    # Chrome browser
mvn test -Pfirefox   # Firefox browser
mvn test -Pedge      # Edge browser
```

### **Run Individual Test Suite**

```bash
mvn test -Dtest=UserRegistrationTest
```

## 📋 **Dependency Summary**

| Category              | Dependencies                                                    | Versions                    |
| --------------------- | --------------------------------------------------------------- | --------------------------- |
| **Selenium**          | selenium-java, selenium-grid, selenium-support, browser drivers | 4.35.0                      |
| **Testing**           | TestNG, JUnit Jupiter                                           | 7.10.2, 5.11.0              |
| **Driver Management** | WebDriverManager                                                | 5.8.0                       |
| **Utilities**         | Apache Commons IO, Apache Commons Lang                          | 2.16.1, 3.17.0              |
| **JSON**              | Jackson Databind                                                | 2.18.2                      |
| **Logging**           | SLF4J API, Logback Classic                                      | 2.0.16, 1.5.6               |
| **Assertions**        | Hamcrest, AssertJ                                               | 2.2, 3.26.0                 |
| **Mocking**           | Mockito                                                         | 5.12.0                      |
| **Build**             | Maven Compiler, Surefire, Failsafe, Exec                        | 3.13.0, 3.5.0, 3.5.0, 3.1.1 |

## ✅ **Benefits of This Setup**

1. **Latest Versions**: All dependencies use the most recent stable versions
2. **Comprehensive Coverage**: Includes all necessary libraries for robust testing
3. **Cross-Browser Support**: Chrome, Firefox, and Edge drivers included
4. **Multiple Testing Frameworks**: TestNG and JUnit Jupiter support
5. **Rich Assertions**: Hamcrest and AssertJ for flexible assertions
6. **Automatic Driver Management**: WebDriverManager handles driver downloads
7. **Professional Logging**: SLF4J and Logback for comprehensive logging
8. **JSON Support**: Jackson for data-driven testing
9. **Utility Libraries**: Apache Commons for common operations
10. **Maven Profiles**: Easy browser switching via profiles

This comprehensive dependency setup ensures you have everything needed for professional Selenium WebDriver testing! 🎉
