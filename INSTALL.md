# Installation Guide

This guide provides instructions for setting up the Community Weather Forecast Dashboard on both Windows (using a WAMP stack) and Linux (using a LAMP stack).

---

## Prerequisites

Before you begin, you will need:

*   A web server environment (Apache is recommended).
*   PHP (version 7.4 or higher).
*   A MySQL or MariaDB database server.
*   API keys for the weather services you intend to use (Pirate Weather requires one).

---

## Option 1: Windows Installation (using WAMP)

This guide assumes you are using a pre-packaged WAMP (Windows, Apache, MySQL, PHP) server like [WampServer](https://www.wampserver.com/en/) or [XAMPP](https://www.apachefriends.org/).

### 1. Set Up Your WAMP Environment

-   Install WampServer or XAMPP on your machine.
-   Start the services (Apache and MySQL).

### 2. Get the Application Files

-   Download the project files as a ZIP and extract them, or use Git to clone the repository.
-   Place the entire `weather_app` folder inside your web server's root directory (e.g., `C:\wamp64\www\` or `C:\xampp\htdocs\`).

### 3. Create the Database

-   Open `phpMyAdmin` from your WAMP control panel (usually accessible via `http://localhost/phpmyadmin`).
-   Create a new database. For this guide, we will use the name `weather_feedback`.
-   Create a new database user (e.g., `weather_feedback_user`) and grant it all privileges on the `weather_feedback` database.

### 4. Configure Database Connection

-   Open the `weather_app/dbParams.php` file in a text editor.
-   Update the `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` constants with the credentials you just created.

### 5. Import the Database Schema

-   In `phpMyAdmin`, select your `weather_feedback` database.
-   Click the "Import" tab.
-   Click "Choose File" and select the `weather_app/schema.sql` file.
-   Click "Go" at the bottom of the page to run the import.

### 6. Run the Initial Admin Setup

-   Open your web browser and navigate to `http://localhost/weather_app/new_admin.php`.
-   Fill in the form to create your primary administrator account.
-   **SECURITY WARNING:** Immediately after creating the account, **delete the `new_admin.php` file** from your `weather_app` directory.

### 7. Configure the Application

-   Navigate to `http://localhost/weather_app/admin.php` and log in with your new admin credentials.
-   You will be redirected to the setup page. Fill in your default location, API keys, and other settings.
-   Click "Create / Update config.php".

Your application is now set up and ready to use!

---

## Option 2: Linux Installation (using LAMP)

This guide is for Debian-based systems like Ubuntu.

### 1. Install the LAMP Stack

If you don't have a LAMP stack, install it via the terminal:
```bash
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
```

### 2. Get the Application Files

Navigate to your web server's root directory and clone the repository:
```bash
cd /var/www/html
sudo git clone https://github.com/your-username/your-repo-name.git weather_app
```

### 3. Create the Database

-   Log in to MySQL:
    ```bash
    sudo mysql -u root -p
    ```
-   Create the database and user with the following SQL commands:
    ```sql
    CREATE DATABASE weather_feedback;
    CREATE USER 'weather_feedback_user'@'localhost' IDENTIFIED BY 'your_strong_password';
    GRANT ALL PRIVILEGES ON `weather_feedback`.* TO 'weather_feedback_user'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
    ```

### 4. Configure Database Connection

-   Edit the `dbParams.php` file:
    ```bash
    sudo nano /var/www/html/weather_app/dbParams.php
    ```
-   Update the file with the credentials you just created.

### 5. Import the Database Schema

Run the import command from your terminal:
```bash
sudo mysql -u weather_feedback_user -p weather_feedback < /var/www/html/weather_app/schema.sql
```

### 6. Set File Permissions

The web server needs permission to write to certain files and directories. The web server user is typically `www-data`.

```bash
# Change ownership of the app directory to the web server user
sudo chown -R www-data:www-data /var/www/html/weather_app/

# Allow the server to write to cache and archive directories
sudo chmod -R 755 /var/www/html/weather_app/cache/
sudo chmod -R 755 /var/www/html/weather_app/staff_cache/
sudo chmod -R 755 /var/www/html/weather_app/archive/

# Allow the server to write the config file during setup
sudo chmod 664 /var/www/html/weather_app/config.php
```
_Note: You may need to create `config.php` first: `sudo touch /var/www/html/weather_app/config.php`_

### 7. Run the Initial Admin Setup

-   Open your web browser and navigate to `http://your_server_ip/weather_app/new_admin.php`.
-   Create your primary administrator account.
-   **SECURITY WARNING:** Immediately delete the `new_admin.php` file:
    ```bash
    sudo rm /var/www/html/weather_app/new_admin.php
    ```

### 8. Configure the Application

-   Navigate to `http://your_server_ip/weather_app/admin.php` and log in.
-   Fill out the configuration form to create your `config.php`.

Your application is now set up!

