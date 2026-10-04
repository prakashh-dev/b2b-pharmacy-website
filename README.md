# MediTrade — B2B Pharmacy Website

A local portfolio/demo project using HTML, CSS, JavaScript, PHP, MySQL and XAMPP.

## Features
- Public home page and medicine catalogue with search/category filters
- Buyer registration and login
- Password hashing, PDO prepared statements, CSRF tokens
- Buyer cart and order placement
- Buyer order history
- Admin dashboard: product CRUD, stock management, order status updates
- Light/dark theme toggle
- Responsive layout

## Requirements
- XAMPP with Apache and MySQL/MariaDB
- PHP 8.0+ recommended
- A modern browser

## Install on Windows/XAMPP
1. Extract the project folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Import `database.sql` using the **Import** tab.
5. If needed, update database settings in `config.php`.
6. Open `http://localhost/b2b-pharmacy-website/`.

## Demo admin account
- Email: `admin@meditrade.local`
- Password: `Admin@12345`

Create buyer accounts using the Register page.

## Important security notes
- This is a learning/portfolio project, not production-ready medical commerce software.
- Change the demo admin password before sharing or deploying.
- For production, add pharmacy licence verification, business approval workflow, audit logs, rate limiting, secure HTTPS cookies, email verification, backups, payment gateway integration and applicable medicine/prescription controls.
- The demo uses a simplified order flow and does not process real payments.

If your `database.sql` did not seed an admin account, visit `http://localhost/b2b-pharmacy-website/setup_admin.php` once, then delete `setup_admin.php` immediately.
