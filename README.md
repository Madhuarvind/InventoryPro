# InventoryPro - Modern Inventory Management System

## Overview
InventoryPro is a comprehensive web-based inventory management system designed to help businesses efficiently track products, manage orders, process payments, and generate detailed reports. With an intuitive user interface and powerful features, InventoryPro streamlines inventory operations for businesses of all sizes.

## Features

### Product Management
- Add, edit, and delete products with detailed information
- Upload product images
- Categorize products for better organization
- Barcode generation and scanning support
- Real-time stock level tracking

### Order Processing
- User-friendly shopping cart functionality
- Order creation and management
- Order status tracking (Pending, Completed, Cancelled)
- Purchase order management for restocking

### User Management
- Role-based access control (Admin, Staff, Customer)
- Secure user authentication
- User profiles and account management

### Payment Integration
- UPI payment support with QR code generation
- Razorpay payment gateway integration
- Payment status tracking

### Reporting & Analytics
- Sales reports with filtering options
- Inventory reports
- Export data to CSV and PDF formats
- Visual analytics with charts and graphs
- Sales prediction functionality

## Technical Requirements

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/LAMP stack (recommended for local development)

### PHP Extensions
- PDO PHP Extension
- MySQL PHP Extension
- GD PHP Extension (for image processing)
- Fileinfo PHP Extension

### Dependencies
- PHPMailer (for email notifications)
- TCPDF/DOMPDF (for PDF generation)
- Razorpay PHP SDK
- QR Code generation libraries
- Bootstrap 5 (for UI components)
- Chart.js (for data visualization)

## Installation

1. **Clone or download the repository**
   ```
   git clone https://github.com/Madhuaravind P/inventorypro.git
   ```

2. **Set up the database**
   - Create a MySQL database named `inventory_db2`
   - Import the database schema from `database/db.sql`

3. **Configure database connection**
   - Open `db.php` and update the database credentials if needed:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "MYSQL";
     $dbname = "inventory_db2";
     ```

4. **Install dependencies**
   ```
   composer install
   ```

5. **Set up file permissions**
   - Ensure the `uploads/` and `qr_codes/` directories are writable by the web server

6. **Access the application**
   - Navigate to `http://localhost/inventory_system` in your web browser
   - Default admin login: 
     - Username: admin
     - Password: admin123

## Usage

### Admin Dashboard
- Access comprehensive system overview
- Monitor sales, inventory levels, and user activity
- Generate and view reports

### Product Management
- Add new products with images, prices, and stock quantities
- Update existing product information
- Monitor low stock items

### Order Processing
- Create new orders
- Update order status
- Process payments
- Generate invoices

### Reporting
- Generate sales reports by date range
- Export inventory data
- Analyze sales trends with visual charts

## Security

InventoryPro implements several security measures:
- Password hashing
- Input validation and sanitization
- Role-based access control
- Session management

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact support@inventorypro.com or open an issue in the GitHub repository.