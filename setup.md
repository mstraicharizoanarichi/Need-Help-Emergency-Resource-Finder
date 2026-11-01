# 🚀 Project Setup & Execution Guide
## Need Help – Emergency Resource & Life-Safety Dispatch Platform (Bangladesh)

Welcome Richi! This document provides a complete, foolproof, step-by-step walkthrough to get this Laravel 12 application running on your local development machine with **zero errors**.

---

## 📋 System Prerequisites

Ensure you have the following installed on your machine before beginning:

| Software | Minimum Version | Recommended Tool / Distribution |
| :--- | :--- | :--- |
| **PHP** | 8.2 or higher | [XAMPP](https://www.apachefriends.org/), [Laragon](https://laragon.org/), or PHP standalone |
| **Composer** | 2.x | [getcomposer.org](https://getcomposer.org/) |
| **Node.js & npm** | Node 18+ / npm 9+ | [nodejs.org](https://nodejs.org/) (LTS version) |
| **Database** | MySQL 8.0+ or MariaDB 10.4+ | Bundled with XAMPP / Laragon, or standalone MySQL |
| **Git** | 2.x+ | [git-scm.com](https://git-scm.com/) |

### Required PHP Extensions
Make sure the following extensions are enabled in your `php.ini` file:
- `pdo_mysql`
- `mbstring`
- `openssl`
- `curl`
- `fileinfo`
- `xml`
- `bcmath`

*(If using XAMPP or Laragon on Windows, these are enabled by default, or you can verify they are uncommented in `php.ini`).*

---

## ⚡ Quick Start: Step-by-Step Installation

### Step 1: Clone or Navigate to the Project
```bash
# If cloning via Git:
git clone https://github.com/Rahad-Max/Need-Emergency-service-platform.git
cd Need-Emergency-service-platform

# Or if you received the project folder directly:
cd Need_Help_Emergency_Resource_Finder
```

---

### Step 2: Install PHP (Composer) Dependencies
Run Composer to install all backend packages:
```bash
composer install
```
> [!NOTE]
> This installs Laravel 12 core framework dependencies, database utilities, and testing tools.

---

### Step 3: Install Frontend (npm) Dependencies
Run npm to install all JavaScript, Tailwind CSS 4, and Alpine.js packages:
```bash
npm install
```

---

### Step 4: Configure the Environment File (`.env`)
Create your local environment file by copying the provided `.env.example` template:

**On Windows (PowerShell / Command Prompt):**
```powershell
copy .env.example .env
```

**On macOS / Linux / Git Bash:**
```bash
cp .env.example .env
```

---

### Step 5: Generate Application Encryption Key
Generate a secure, unique `APP_KEY` in your `.env` file:
```bash
php artisan key:generate
```

---

### Step 6: Configure the Database

#### Option A: MySQL (Default & Recommended)
1. Open your MySQL client (e.g., phpMyAdmin at `http://localhost/phpmyadmin`, HeidiSQL, MySQL Workbench, or CLI).
2. Create a new empty database named **`need_help`**:
   ```sql
   CREATE DATABASE need_help CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Open the `.env` file in your editor and verify your database settings:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=need_help
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > [!TIP]
   > - If you use **XAMPP** or **Laragon**, the default MySQL username is `root` with an **empty** password (`DB_PASSWORD=`).
   > - If you have set a custom MySQL password, enter it in `DB_PASSWORD=your_password`.

#### Option B: SQLite (Quick Setup without MySQL)
If you don't have MySQL running, you can use SQLite:
1. In your terminal, create an empty SQLite file:
   ```bash
   # Windows PowerShell:
   New-Item -ItemType File -Path database\database.sqlite
   
   # Linux / macOS / Git Bash:
   touch database/database.sqlite
   ```
2. Update `.env`:
   ```ini
   DB_CONNECTION=sqlite
   # Comment out or remove DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD
   ```

---

### Step 7: Run Migrations and Seed Realistic Demo Data
Run the database migrations and seed all initial data (categories, hospitals, ambulances, specialist doctors, blood donors, and demo accounts):
```bash
php artisan migrate:fresh --seed
```

> **What gets seeded automatically?**
> - **7 Categories**: Hospitals, Ambulances, Pharmacies, Blood Donors, Police, Fire Services, Diagnostic Centers.
> - **Major Emergency Hospitals**: Dhaka Medical College Hospital (DMCH), Square Hospital, United Hospital, Chittagong Medical College Hospital (CMCH).
> - **Specialist & On-Call Doctors**: Senior cardiologists, trauma physicians, burn surgeons, and visiting schedules.
> - **Blood Donors**: Donors across all 8 blood groups with realistic donation intervals and apheresis platelet eligibility.
> - **Demo Incidents & First Responders**: Active emergency dispatches and registered first responders.

---

### Step 8: Create the Public Storage Symlink
Link the `storage/app/public` folder to `public/storage` so that uploaded images, doctor photos, and documents load properly:
```bash
php artisan storage:link
```

---

### Step 9: Build Frontend Assets
Compile Tailwind CSS 4, Alpine.js, and Leaflet assets using Vite:
```bash
npm run build
```

---

## 🏃‍♂️ Running the Project Locally

To run the application with live hot-reloading for development, open two terminal windows:

### Terminal 1: Frontend Asset Server (Vite)
```bash
npm run dev
```

### Terminal 2: Backend Laravel Server
```bash
php artisan serve
```

Now open your web browser and navigate to:
👉 **[http://localhost:8000](http://localhost:8000)** (or `http://127.0.0.1:8000`)

---

## 🔑 Pre-Configured Demo Accounts

You can test any role immediately. On the **Sign In** page (`/login`), there are **1-Click quick fill buttons**, or you can enter these credentials manually:

| User Role | Email Address | Password | Accessible Areas & Features |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@needhelp.bd` | `password123` | `/admin/dashboard`, `/admin/emergency-command` (EOC Command Center), `/admin/emergency-analytics` (GIS Heatmap), Verification Queues |
| **Medical Provider** | `square.hospital@needhelp.bd` | `password123` | `/provider/dashboard`, `/provider/dispatches` (Live Dispatch Console), Facility & Doctor Roster Management |
| **Citizen / General User**| `citizen@needhelp.bd` | `password123` | `/dashboard`, 1-Tap SOS (`/emergency/sos`), Voice Triage Intake (`/emergency/report`), Blood Donor Contact Requests |

---

## 🤖 Optional: Google Gemini AI Triage Configuration

The platform features an intelligent emergency intake and triage engine.

- **Offline Mode (Default)**: If no Gemini API key is configured, the system uses its built-in clinical **Deterministic NLP parser** with 50+ Bengali and English medical emergency keywords. The application will work **100% smoothly without an API key**.
- **Gemini AI Mode (Optional)**: If you want to enable Google Gemini LLM triage, get a free API key from [Google AI Studio](https://aistudio.google.com/) and add it to your `.env` file:
  ```ini
  GEMINI_API_KEY=your_gemini_api_key_here
  GEMINI_MODEL=gemini-1.5-flash
  ```

---

## 🧪 Verifying with the Automated Test Suite

We maintain a comprehensive automated test suite with **100% pass rate** across all modules.

To verify your installation:
```bash
php artisan test
```

Expected result:
```text
  Tests:    108 passed (611 assertions)
  Duration: ~100s
```

You can also run specific test suites:
```bash
# Test the Emergency Assistant & Chatbot modules
php artisan test tests/Feature/Chatbot

# Test Emergency SOS & Incident Live Tracker
php artisan test tests/Feature/EmergencyIncidentTest.php

# Test EOC Command Center & Smart Dispatch
php artisan test tests/Feature/EmergencyCommandCenterTest.php
```

---

## 🛠️ Common Troubleshooting & FAQ

### Q1: `SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'`
- **Cause**: MySQL password mismatch in `.env`.
- **Solution**: Open `.env` and set `DB_PASSWORD=` to empty if you are using standard XAMPP or Laragon. If your MySQL user has a password, enter that exact password.

### Q2: `SQLSTATE[HY000] [1049] Unknown database 'need_help'`
- **Cause**: The database has not been created yet in MySQL.
- **Solution**: Open your MySQL terminal or phpMyAdmin and run `CREATE DATABASE need_help;`, then re-run `php artisan migrate:fresh --seed`.

### Q3: `Vite manifest not found at: .../public/build/manifest.json`
- **Cause**: Frontend assets have not been built yet.
- **Solution**: Run `npm run build` once, or keep `npm run dev` running in a separate terminal.

### Q4: `The "public/storage" directory already exists`
- **Cause**: The storage link was already created previously.
- **Solution**: This is harmless. If you need to refresh it:
  - Windows: Delete the `public\storage` folder and run `php artisan storage:link`.
  - Linux/Mac: Run `rm public/storage && php artisan storage:link`.

### Q5: Images or avatars uploaded in storage return 404
- **Solution**: Run `php artisan storage:link`.

### Q6: Changes to `.env` or configurations are not taking effect
- **Solution**: Clear Laravel's configuration and route caches:
  ```bash
  php artisan config:clear
  php artisan route:clear
  php artisan cache:clear
  php artisan view:clear
  ```

---

## 📁 Key Directories & Architecture Overview

```text
Need_Help_Emergency_Resource_Finder/
├── app/
│   ├── Http/Controllers/    # Route controllers (SOS, Assistant, Search, Admin, Provider)
│   ├── Models/                 # Eloquent models (Resource, Incident, BloodDonor, Doctor, etc.)
│   └── Services/               # Core business logic:
│       ├── AI/                 # GeminiEmergencyProvider & SpeechToTextService
│       ├── Chatbot/            # Conversational triage FSM, intent classifiers & knowledge base
│       └── Emergency/          # DispatchExecution, ResourceMatching, ResponseTimePrediction
├── database/
│   ├── migrations/             # 25 database schema migrations
│   └── seeders/                # Idempotent realistic seed data for Bangladesh
├── resources/
│   ├── views/                  # Blade templates (Tailwind CSS 4 + Alpine.js)
│   │   ├── components/         # Reusable UI components (Navbar, SOS Modal, Chatbot widget)
│   │   ├── emergency/          # SOS, report intake & live tracker timeline
│   │   ├── admin/              # EOC command center & GIS heatmap analytics
│   │   └── provider/           # Hospital & ambulance dispatch fleet console
│   ├── css/app.css             # Tailwind CSS 4 theme rules & animations
│   └── js/                     # Alpine.js & theme dark-mode engine
├── routes/
│   ├── web.php                 # Web application routes
│   └── console.php             # CLI artisan commands
└── tests/
    └── Feature/                # 108 automated feature test cases
```

---

✨ **Happy Coding! If you encounter any unexpected issues, check the FAQ section above or ask the team.**
