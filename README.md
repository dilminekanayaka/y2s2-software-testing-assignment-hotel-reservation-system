# **🌸 Flower Garden Hotels - Complete Hotel Reservation System**

**A comprehensive hotel reservation system built for the Year 2 Semester 2 Software Testing Assignment. Experience luxury hospitality across Sri Lanka's most beautiful destinations.**

![Flower Garden Hotels](uploads/Hotels/Colombo/Hotel-1.png)

## **📌 Table of Contents**

- [Overview](#-overview)
- [Features](#-features)
- [Hotels & Locations](#-hotels--locations)
- [Technologies Used](#-technologies-used)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Usage Guide](#-usage-guide)
- [Admin Portal](#-admin-portal)
- [API Endpoints](#-api-endpoints)
- [Security Features](#-security-features)
- [Contributing](#-contributing)
- [License](#-license)

## **🌟 Overview**

Flower Garden Hotels is a complete hotel reservation system featuring four luxury properties across Sri Lanka. The system provides seamless booking experiences for guests while offering comprehensive management tools for administrators.

### **🏨 Our Properties**

- **Colombo** - Modern city hotel in the business district
- **Ella** - Boutique hotel surrounded by tea plantations
- **Matara** - Beachfront resort with ocean views
- **Nuwara Eliya** - Hill station hotel in tea country

## **✨ Features**

### **👤 User Features**

- ✅ **User Registration & Authentication** - Secure account creation and login
- ✅ **Advanced Hotel Search** - Filter by location, dates, and guest count
- ✅ **Room Selection** - Multiple room types with detailed amenities
- ✅ **Real-time Booking** - Instant reservation confirmation
- ✅ **Payment Processing** - Secure card payment integration
- ✅ **Coupon System** - Discount codes with percentage/fixed discounts
- ✅ **Email Notifications** - Automated booking confirmations and updates
- ✅ **Reservation Management** - View, modify, and cancel bookings
- ✅ **User Dashboard** - Personal profile and booking history

### **👨‍💼 Admin Features**

- ✅ **Reservation Management** - View and manage all bookings
- ✅ **Status Updates** - Real-time booking status changes
- ✅ **Admin Notes** - Internal notes system for reservations
- ✅ **Hotel Management** - Complete CRUD operations
- ✅ **Room Management** - Manage room types and availability
- ✅ **Email System** - Send notifications to guests
- ✅ **Analytics Dashboard** - Booking statistics and reports

### **🎨 User Experience**

- ✅ **Responsive Design** - Mobile-first, works on all devices
- ✅ **Image Galleries** - Dynamic hotel and room image sliders
- ✅ **Modern UI/UX** - Professional design with smooth animations
- ✅ **Dropdown Navigation** - Intuitive user interface
- ✅ **Error Handling** - Clear feedback and validation messages
- ✅ **Loading States** - Smooth user interactions

## **🏨 Hotels & Locations**

### **Colombo - Business District**

- **Location**: 123 Business District, Colombo
- **Rating**: 4.4/5
- **Room Types**: Standard Room, Deluxe Suite, Presidential Suite
- **Amenities**: WiFi, City Views, Business Center, Gym, Restaurant

### **Ella - Tea Plantations**

- **Location**: 456 Flower Garden Road, Ella
- **Rating**: 4.8/5
- **Room Types**: Garden View Room, Heritage Suite
- **Amenities**: WiFi, Mountain Views, Restaurant, Spa, Garden

### **Matara - Beachfront**

- **Location**: 789 Beach Road, Matara
- **Rating**: 4.6/5
- **Room Types**: Beach View Room, Ocean Suite
- **Amenities**: WiFi, Beach Access, Restaurant, Pool, Garden

### **Nuwara Eliya - Hill Station**

- **Location**: 321 Tea Estate Road, Nuwara Eliya
- **Rating**: 4.7/5
- **Room Types**: Tea Garden Room, Mountain Suite
- **Amenities**: WiFi, Mountain Views, Restaurant, Spa, Tea Plantation Tours

## **🛠 Technologies Used**

### **Frontend**

- **HTML5** - Semantic markup and structure
- **CSS3** - Flexbox, Grid, Animations, Responsive Design
- **JavaScript** - DOM manipulation, AJAX, Form validation
- **Font Awesome** - Professional icons
- **Google Fonts** - Modern typography

### **Backend**

- **PHP 7.4+** - Server-side processing and logic
- **MySQL/MariaDB** - Relational database management
- **PDO** - Secure database interactions
- **Session Management** - User authentication and state

### **Security & Features**

- **CSRF Protection** - Cross-site request forgery prevention
- **Input Sanitization** - XSS and injection protection
- **Password Hashing** - Secure password storage
- **Email System** - Automated notifications
- **Image Management** - Local file handling

## **⚙ Installation**

### **Prerequisites**

- PHP >= 7.4
- MySQL/MariaDB
- Apache/Nginx Server
- Web browser with JavaScript enabled

### **Setup Steps**

1. **Clone the repository**

   ```bash
   git clone https://github.com/dilminekanayaka/y2s2-software-testing-assignment-hotel-reservation-system.git
   cd y2s2-software-testing-assignment-hotel-reservation-system
   ```

2. **Configure Database**

   - Create a MySQL database named `ellaflowergarden`
   - Update `config.php` with your database credentials:

   ```php
   $host = 'localhost';
   $dbname = 'ellaflowergarden';
   $username = 'your_username';
   $password = 'your_password';
   ```

3. **Import Database Schema**

   ```bash
   mysql -u your_username -p ellaflowergarden < complete_database_setup.sql
   ```

4. **Set Permissions**

   ```bash
   chmod 755 uploads/Hotels/
   chmod 644 uploads/Hotels/*/*
   ```

5. **Run the Project**
   - Place in web server root directory (e.g., `htdocs` or `/var/www/html`)
   - Access via `http://localhost/flowergarden`

## **🗄 Database Setup**

The system uses a comprehensive database schema with the following tables:

- **`users`** - User accounts and profiles
- **`hotels`** - Hotel information and images
- **`room_types`** - Room categories and pricing
- **`reservations`** - Booking records and status
- **`payments`** - Payment transactions
- **`coupons`** - Discount codes and rules
- **`admin_users`** - Administrator accounts

### **Sample Data Included**

- 4 hotels across Sri Lanka
- 9 room types with pricing
- 5 sample coupon codes
- Default admin account (admin/admin123)

## **🚀 Usage Guide**

### **For Guests**

1. **Register** - Create an account with email and password
2. **Search Hotels** - Use the homepage search or browse hotels
3. **Select Dates** - Choose check-in and check-out dates
4. **Choose Room** - Select room type and view amenities
5. **Apply Coupons** - Enter discount codes for savings
6. **Make Payment** - Complete booking with card details
7. **Receive Confirmation** - Get email confirmation and booking reference

### **For Administrators**

1. **Login** - Access admin portal at `/admin/login.php`
2. **View Reservations** - See all bookings and their status
3. **Update Status** - Change booking status (confirmed, checked-in, etc.)
4. **Add Notes** - Include internal notes for reservations
5. **Send Emails** - Notify guests of status changes

## **👨‍💼 Admin Portal**

Access the admin portal at `/admin/login.php`

**Default Admin Credentials:**

- Username: `admin`
- Password: `admin123`

**Admin Features:**

- Dashboard with booking statistics
- Reservation management with status updates
- Hotel and room management
- Email notification system
- Admin notes for internal communication

## **🔌 API Endpoints**

### **Coupon Validation**

- **Endpoint**: `/validate_coupon.php`
- **Method**: POST
- **Parameters**: `coupon_code`, `total_amount`
- **Response**: JSON with discount details

### **Email Notifications**

- **Booking Confirmation**: Automatic email on successful booking
- **Status Updates**: Email notifications for booking changes
- **Cancellation**: Confirmation email for cancellations

## **🔒 Security Features**

- **CSRF Protection** - All forms include CSRF tokens
- **Input Sanitization** - All user inputs are sanitized
- **SQL Injection Prevention** - Prepared statements used throughout
- **XSS Protection** - Output escaping for all dynamic content
- **Session Security** - Secure session management
- **Password Hashing** - bcrypt password hashing
- **File Upload Security** - Restricted file types and validation

## **📱 Responsive Design**

The system is fully responsive and optimized for:

- **Desktop** - Full-featured experience
- **Tablet** - Touch-friendly interface
- **Mobile** - Optimized for small screens

## **🤝 Contributing**

Contributions are welcome! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Commit your changes** (`git commit -m 'Add amazing feature'`)
4. **Push to the branch** (`git push origin feature/amazing-feature`)
5. **Open a Pull Request**

### **Development Guidelines**

- Follow PSR-12 coding standards
- Add comments for complex logic
- Test all features before submitting
- Update documentation for new features

## **📜 License**

This project is licensed under the **MIT License**. See [LICENSE](LICENSE) for details.

## **📧 Contact & Support**

- **Developer**: Dilmin Ekanayaka
- **Email**: nisadiwijerathna@gmail.com
- **GitHub**: [@dilminekanayaka](https://github.com/dilminekanayaka)
- **Repository**: [Hotel Reservation System](https://github.com/dilminekanayaka/y2s2-software-testing-assignment-hotel-reservation-system)

## **🎯 Project Status**

✅ **Completed Features**

- User authentication and registration
- Hotel search and booking system
- Payment processing with coupons
- Admin portal with reservation management
- Email notification system
- Responsive design and UI/UX
- Security features and validation
- Local image management system

## **🌟 Acknowledgments**

- Built for Year 2 Semester 2 Software Testing Assignment
- Inspired by Sri Lanka's beautiful hospitality industry
- Uses modern web development best practices
- Implements comprehensive security measures

---

**🌸 Experience luxury hospitality with Flower Garden Hotels! 🌸**

_Your gateway to Sri Lanka's most beautiful destinations_
