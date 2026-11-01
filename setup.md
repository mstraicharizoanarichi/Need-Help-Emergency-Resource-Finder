# 🚀 Project Setup & Execution Guide

## Need Help – Emergency Resource & Life-Safety Dispatch Platform (Bangladesh)

This guide explains how to install, configure, run, and test the Laravel application locally.

---

## 📋 1. System Requirements

| Software | Minimum Version |
| -------- | --------------- |
| PHP      | 8.2 or higher   |
| Composer | 2.x             |
| Node.js  | 18 or higher    |
| npm      | 9 or higher     |
| MySQL    | 8.0 or higher   |
| MariaDB  | 10.4 or higher  |
| Git      | 2.x or higher   |

### Required PHP Extensions

Ensure these PHP extensions are enabled:

* `pdo_mysql`
* `mbstring`
* `openssl`
* `curl`
* `fileinfo`
* `xml`
* `bcmath`

---

## ⚡ 2. Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/mstraicharizoanarichi/Need-Help-Emergency-Resource-Finder.git
cd Need-Help-Emergency-Resource-Finder
```

Alternatively, open the extracted project directory in your terminal.

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Frontend Dependencies

```bash
npm install
```

### Step 4: Create the Environment File

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

For macOS/Linux:

```bash
cp .env.example .env
```

### Step 5: Generate the Application Key

```bash
php artisan key:generate
```

---

## 🗄️ 3. Database Configuration

### Option A: MySQL / MariaDB

1. Start MySQL or MariaDB using XAMPP, Laragon, or your database server.
2. Open phpMyAdmin or another database management tool.
3. Create an empty database.

Example:

```sql
CREATE DATABASE web_project_need_help
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

4. Update the database settings in `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_project_need_help
DB_USERNAME=root
DB_PASSWORD=
```

Use your actual database name, username, and password if they are different.

### Option B: SQLite

If you prefer SQLite:

```powershell
New-Item -ItemType File -Path database\database.sqlite
```

Set the following in `.env`:

```ini
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Make sure the PHP SQLite extension is enabled.

---

## 🛠️ 4. Run Database Migrations

Run:

```bash
php artisan migrate --seed
```

This creates the database tables and executes the configured seeders.

**Important:** If you intentionally want to reset the database and recreate all tables, you can use:

```bash
php artisan migrate:fresh --seed
```

Warning: `migrate:fresh` deletes existing tables and their data. Do not use it on a database containing data you want to keep.

---

## 🔗 5. Create the Storage Link

Run:

```bash
php artisan storage:link
```

This makes publicly accessible uploaded files available through `public/storage`.

---

## 🎨 6. Build Frontend Assets

Run:

```bash
npm run build
```

This compiles the frontend assets using Vite.

---

## ▶️ 7. Run the Application

For local development, open two terminal windows in the project directory.

**Terminal 1 – Vite development server**

```bash
npm run dev
```

**Terminal 2 – Laravel server**

```bash
php artisan serve
```

Open the application in your browser:

http://127.0.0.1:8000

---

## 🔐 8. Demo Accounts

If the database seeders create these accounts, use the following credentials:

| Role                   | Email                         | Password      |
| ---------------------- | ----------------------------- | ------------- |
| System Administrator   | `admin@needhelp.bd`           | `password123` |
| Medical Provider       | `square.hospital@needhelp.bd` | `password123` |
| Citizen / General User | `citizen@needhelp.bd`         | `password123` |

These accounts are intended for local development and testing. Do not use demo credentials in production.

---

## 🤖 9. Optional Gemini AI Configuration

The application supports an optional Gemini API integration.

Without an API key, the application can use its built-in deterministic emergency parsing functionality where configured.

To enable Gemini:

1. Obtain an API key from Google AI Studio.
2. Add the following settings to `.env`:

```ini
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=your_supported_gemini_model
```

Use a model supported by the configured Gemini API integration.

Never commit your actual API key to GitHub.

---

## 🧪 10. Run Automated Tests

Run the complete test suite:

```bash
php artisan test
```

The verified project test result is:

```text
Tests:    108 passed (611 assertions)
```

You can also run individual test groups:

**Chatbot tests**

```bash
php artisan test tests/Feature/Chatbot
```

**Emergency incident tests**

```bash
php artisan test tests/Feature/EmergencyIncidentTest.php
```

**Emergency command center tests**

```bash
php artisan test tests/Feature/EmergencyCommandCenterTest.php
```

Test results may change as the project is updated.

---

## 🔧 11. Troubleshooting

### Q1: MySQL Access Denied

Error:

```text
SQLSTATE[HY000] [1045] Access denied
```

Check the database username and password in `.env`.

For a default XAMPP installation, the username is often `root` and the password is empty.

### Q2: Unknown Database

Error:

```text
SQLSTATE[HY000] [1049] Unknown database
```

Create the database specified in `DB_DATABASE` in `.env`.

### Q3: PDO MySQL Driver Not Found

Error:

```text
could not find driver
```

Enable the `pdo_mysql` extension in the active PHP configuration file and restart the terminal or web server.

Verify the extension:

```bash
php -m
```

### Q4: Vite Manifest Not Found

Error:

```text
Vite manifest not found
```

Run:

```bash
npm install
npm run build
```

Alternatively, keep `npm run dev` running during development.

### Q5: Storage Images Return 404

Run:

```bash
php artisan storage:link
```

### Q6: Configuration Changes Are Not Taking Effect

Clear the Laravel configuration and application caches:

```bash
php artisan optimize:clear
```

---

## 📁 12. Main Project Structure

```text
Need-Help-Emergency-Resource-Finder/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   ├── Policies/
│   ├── Providers/
│   └── Services/
│       ├── AI/
│       ├── Analytics/
│       ├── Chatbot/
│       ├── Dispatch/
│       ├── Emergency/
│       └── Hospital/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── setup.md
```

---

✨ **Happy Coding!**
