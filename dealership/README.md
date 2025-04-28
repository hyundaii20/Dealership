# Car Store Website

A web-based application for an online car store, allowing customers to browse and purchase cars.

## Automatic Database Setup

This project includes a fully automatic database setup feature. When the website is run on any device:

1. The system will automatically check if the required database exists
2. If the database doesn't exist, it will create it automatically
3. All necessary tables will be created with the correct schema
4. Sample car data will be automatically populated into the database
5. The application will then be ready to use without any manual setup

## Requirements

- PHP 7.0 or higher
- MySQL/MariaDB
- Web server (Apache, Nginx, etc.)

## Installation

1. Clone or download this repository to your web server's document root directory
2. Ensure your web server and MySQL server are running
3. Access the website through your web browser
4. The system will automatically set up the database and populate it with sample cars

## Default Database Configuration

The default database configuration uses the following parameters:
- Host: localhost
- Username: root
- Password: (empty)
- Database name: car_store

To modify these settings, edit the `Database.php` file.

## Important Files

- `index.php` - Main entry point of the application
- `Database.php` - Database connection manager with auto-setup feature
- `database_setup.php` - Script that creates the database schema when needed
- `add_sample_cars.php` - Script that populates the database with sample cars
- `cars.php` - Car browsing page with filtering functionality
- `car_details.php` - Detailed car information page
- `cart.php` - Shopping cart functionality

## Features

- Browse and search a variety of car models from different manufacturers
- Filter cars by make, model, year, and price range
- View detailed information for each car
- Add cars to shopping cart
- Manage cart items (update quantity, remove items)
- Simple checkout process

## Sharing the Project

When sharing this project for inspection, the recipient needs only to:
1. Place the files in their web server directory
2. Start their web server and MySQL
3. Access the website - everything else happens automatically 