# 🚨 Need Help – Emergency Resource & Life-Safety Dispatch Platform (Bangladesh)

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![MySQL 8.0](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS 4](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![Google Gemini AI](https://img.shields.io/badge/Google_Gemini-AI_Triage-4285F4?style=for-the-badge&logo=google)](https://ai.google.dev/)
[![Leaflet.js](https://img.shields.io/badge/Leaflet-1.9.4-199900?style=for-the-badge&logo=leaflet&logoColor=white)](https://leafletjs.com)
[![Tests Passing](https://img.shields.io/badge/Tests-108%20Passed%20(611%20Assertions)-success?style=for-the-badge&logo=checkmarx)](https://phpunit.de)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

A national-scale, AI-assisted emergency resource finder, incident triage, and life-safety dispatch platform engineered specifically for **Bangladesh**. 

The platform bridges critical gaps in national emergency care by enabling citizens to discover and contact nearby hospitals, ambulances, pharmacies, blood donors, police stations, fire services, diagnostic centers, and on-call specialist doctors with **zero login friction**. It features an **AI-powered bilingual triage intake engine**, **1-Tap SOS triggers**, **real-time ambulance & incident tracking**, **spatial GIS density heatmaps**, a **Community First Responder (CFR) network**, an **Emergency Operations Command Center (EOC)**, and live hospital ICU/oxygen availability.

---

## 🔄 Project Evolution: What Changes Were Made?

This project underwent a major architectural evolution, transforming from a **passive directory lookup tool** into an **active, real-time emergency dispatch and life-safety operations network**.

### 📊 Summary of What Changed (Before vs. After)

| Dimension | Initial State (v1.0 - Directory) | Current State (v2.0 - Active Dispatch) |
| :--- | :--- | :--- |
| **Core Paradigm** | Read-only directory search for static facility phone numbers | Active incident intake, automated triage, unit dispatch & tracking |
| **Emergency SOS** | Manual dialing of external phone numbers | **1-Tap Instant SOS** with live GPS & automatic P1 incident creation |
| **Intake Mechanism** | Standard search filters by district & category | **Multimodal AI Intake**: Voice audio, Bengali/English text, symptom chips |
| **Triage & Classification** | None (user browsed raw facility listings) | **Google Gemini AI + Fallback NLP** with clinical SLA tiers (P1 to P4) |
| **Incident Tracking** | None | **Live Leaflet Tracking Timeline** with ETA, unit coordinates & pre-arrival first-aid |
| **Operations Management** | Basic listing verification queue for admin | **Emergency Operations Command Center (EOC)** with live dispatch feed |
| **Hospital Matching** | Distance-only Haversine ordering | **Multi-Factor Clinical Matching**: Distance, traffic, ICU beds, oxygen, trauma care |
| **Community Involvement** | Voluntary blood donor registry | **Community First Responder (CFR) Network** with proximity alerting (2–5 km) |
| **Provider Experience** | Manage facility metadata and doctor rosters | **Provider Dispatch Operations Console** for live ambulance fleet tracking |
| **Spatial Analytics** | Basic search map with category pins | **Spatial GIS Density Heatmap Radar** with SLA audits across 64 districts |
| **Database Schema** | 13 tables (resources, doctors, blood donors, users) | **25 tables** (+12 for incidents, events, dispatches, CFRs, alerts, chatbot, platelet & blood banks) |
| **Test Coverage** | 29 tests (194 assertions) across 7 suites | **108 tests (611 assertions)** across 27 suites (100% passing) |

---

### 🛠️ Detailed Breakdown of Major Upgrades

#### 1. 🧠 AI-Powered Emergency Triage & Intake Engine
- **Voice Speech-to-Text (`SpeechToTextService`)**: Callers in acute distress can record voice descriptions, automatically transcribed and parsed.
- **Dual AI / Deterministic NLP Engine**: Integrated Google Gemini AI (`GeminiEmergencyProvider`) with a fail-safe rule-based medical lexicon (`DeterministicEmergencyParser`) containing 50+ Bengali medical emergency keywords.
- **Asynchronous Live Triage Preview (`/emergency/analyze-preview`)**: Gives instant feedback on detected incident type, severity score, and response protocol before submission.
- **Mass Casualty Incident (MCI) Protocol (`MassCasualtyProtocolService`)**: Detects multi-victim emergencies and scales hospital bed surge distribution.

#### 2. ⚡ 1-Tap SOS Emergency Trigger
- **Global SOS Component (`components/sos-modal.blade.php`)**: Accessible directly on the top navigation bar and homepage hero.
- **Zero-Friction Geolocation**: HTML5 GPS capture reverse-mapped to district and upazila without requiring account registration.
- **Immediate Action**: Instantly spawns a P1 Critical incident, alerts the EOC Command Center, and triggers proximity pings to nearby first responders.

#### 3. 📡 Real-Time Public Incident Live Tracker (`/emergency/{reference}`)
- **Auditable Reference Codes**: Unique tracking codes (`NH-YYYY-XXXXXX`) for public tracking.
- **Milestone Progress Pipeline**: Real-time progression through `REPORTED` $\rightarrow$ `TRIAGED` $\rightarrow$ `DISPATCHED` $\rightarrow$ `EN_ROUTE` $\rightarrow$ `ARRIVED_SCENE` $\rightarrow$ `TRANSPORTING` $\rightarrow$ `RESOLVED`.
- **Live Spatial Map & ETA**: Leaflet map plotting patient location, dispatched unit, and destination hospital with traffic-aware arrival times.
- **Pre-Arrival First-Aid Guidance**: Contextual emergency instructions (CPR for cardiac arrest, burn cooling, bleeding control) while responders are in transit.

#### 4. 🚨 Emergency Operations Command Center (EOC) (`/admin/emergency-command`)
- **Live Incident Stream**: Real-time operations queue featuring priority indicators, audio alerts, and SLA countdowns.
- **Smart Resource Matching (`ResourceMatchingService`)**: Automatically ranks nearest available ambulances and specialized trauma hospitals factoring travel time, ICU capacity, and central oxygen.
- **Operator Controls**: 1-Click dispatch approval, triage reassignment, and MCI declarations.

#### 5. 📊 Spatial GIS Heatmap & Response Analytics (`/admin/emergency-analytics`)
- **Bangladesh Emergency Density Radar**: Color-coded spatial clustering map of active and historical incidents.
- **District Hotspot Intelligence**: Identifies high-risk accident corridors across Bangladesh's administrative districts.
- **SLA & Latency Metrics**: Tracks dispatch latency, travel time, and SLA compliance ($P1 \le 18$m, $P2 \le 28$m).

#### 6. 🤝 Community First Responder (CFR) Network
- **Volunteer Onboarding (`/community-responders/register`)**: Trained citizens register with certifications (CPR, BLS, First Aid, Nursing).
- **Proximity Alert Engine (`CfrAlert`)**: Automatically alerts available volunteers within 2–5 km for rapid intervention during cardiac arrest or severe bleeding prior to ambulance arrival.
- **Volunteer Action Portal**: 1-Click duty toggles, alert acceptance, and on-scene arrival confirmation.

#### 7. 🚑 Provider Dispatch Console (`/provider/dispatches`)
- **Fleet Management Board**: Dedicated portal for hospital administrators and ambulance fleet operators.
- **Dispatch Lifecycle Management**: Accept or decline calls, update unit milestones, and log patient handoffs.

#### 8. 🗄️ Database Architecture & Eloquent Models
- **5 New Models Added**:
  - `EmergencyIncident`: Core incident state, location, patient details, and severity.
  - `EmergencyIncidentEvent`: Append-only audit trail logging every state transition and operator action.
  - `EmergencyDispatch`: Unit assignment, dispatch status, vehicle details, and responder info.
  - `CommunityFirstResponder`: Volunteer profile, medical skills, duty status, and coordinates.
  - `CfrAlert`: Geofenced proximity dispatch invitations and volunteer response status.
- **5 New Database Migrations**: Adding spatial indices, foreign keys, and status enums.

#### 9. 🧪 Test Suite Expansion (+158% Coverage Increase)
- Expanded from 29 tests to **75 comprehensive Feature and Unit tests** (429 assertions) covering all new triage, dispatch, EOC, CFR, and hospital recommendation services with 100% pass rate.

---

## 🌟 Key Subsystems & Features

### 1. ⚡ 1-Tap Instant SOS Emergency Trigger (`/emergency/sos`)
- **Zero-Friction Emergency Activation**: Prominent SOS button accessible instantly across the navbar and home hero section.
- **Immediate GPS Geolocation**: High-accuracy HTML5 coordinate capture with automatic administrative district and upazila reverse-mapping.
- **P1 Priority Auto-Classification**: Instantly spawns a P1 Critical incident, alerts the EOC Command Center, and triggers proximity pings to nearby registered Community First Responders.
- **Unique Reference Identifier**: Generates auditable public tracking codes (`NH-YYYY-XXXXXX`).

---

### 2. 🧠 AI-Assisted Triage & Bilingual Intake Engine (`/emergency/report`)
- **Multimodal Intake Options**:
  - **Voice Audio Input**: Speech-to-Text processing (`SpeechToTextService`) for emergency callers in distress.
  - **Bilingual Free-Text Description**: Accepts inputs in English, Standard Bengali, or Banglish (phonetic Bengali).
  - **Quick Symptom Chips**: 1-Tap symptom selection (Chest Pain, Unconscious, Severe Bleeding, Breathing Difficulty, Burn Injury, Road Crash).
- **Dual AI & Deterministic Parsing Architecture**:
  - **Google Gemini AI Provider (`GeminiEmergencyProvider`)**: Uses LLM natural language understanding to extract incident types, patient counts, trauma indicators, and urgency scores.
  - **Deterministic NLP Fallback (`DeterministicEmergencyParser`)**: Robust regex and keyword lexicon covering 50+ Bengali medical emergency terms to ensure 100% triage uptime even offline or without external API keys.
- **Live Triage Preview (`/emergency/analyze-preview`)**: Instant asynchronous pre-submission preview giving citizens dynamic feedback on severity level and expected emergency protocol.
- **Triage Severity Tiering**:
  - 🔴 **P1 Critical**: Life-threatening (Cardiac Arrest, Stroke, Mass Hemorrhage) — $\le$ 18 min SLA.
  - 🟡 **P2 Serious**: Urgent care (Severe Fractures, Respiratory Distress) — $\le$ 28 min SLA.
  - 🔵 **P3 Standard**: Semi-urgent transfer and clinical care.
  - ⚪ **P4 Minor**: Non-emergency medical consultation.
- **Mass Casualty Incident (MCI) Protocol**: Automatically detects multi-victim incidents and enables multi-hospital surge patient distribution.

---

### 3. 📡 Real-Time Public Incident Live Tracker (`/emergency/{reference}`)
- **Live Milestone Progress Timeline**:
  - `REPORTED` $\rightarrow$ `TRIAGED` $\rightarrow$ `DISPATCHED` $\rightarrow$ `EN_ROUTE` $\rightarrow$ `ARRIVED_SCENE` $\rightarrow$ `TRANSPORTING` $\rightarrow$ `RESOLVED`.
- **Interactive OpenStreetMap / Leaflet Tracking**: Displays incident location, assigned ambulance coordinates, and recommended hospital destinations.
- **Dynamic ETA & Traffic Prediction**: Uses `TrafficEstimationService` and `ResponseTimePredictionService` factoring Bangladesh rush hours and weather conditions.
- **Pre-Arrival Life-Saving Guidance**: Contextual first-aid instructions displayed while awaiting responders (e.g. CPR guidance for cardiac arrest, recovery position for unconsciousness, burn cooling protocols).
- **Cancellation Workflow**: Allows callers to safely cancel false alarms with logged audit reasoning.

---

### 4. 🚨 Emergency Operations Command Center (EOC) (`/admin/emergency-command`)
- **Live Emergency Operations Feed**: Real-time triage incident stream with priority badges, status indicators, and SLA breach countdowns.
- **Automated Resource Matching & Recommendation Engine (`ResourceMatchingService`)**:
  - Evaluates nearest ambulances and hospitals using geodesic Haversine distance, traffic congestion multipliers, and live capability scoring.
  - Verifies hospital equipment prerequisites (e.g. ICU bed availability, central oxygen, cath labs for cardiac emergencies, burn units for fire victims).
- **1-Click Dispatch Execution**: Operators dispatch units with automated SMS/web notification handoffs.
- **MCI Emergency Surge Mode**: Declare Mass Casualty status with dynamic on-scene victim counts and hospital bed allocation.

---

### 5. 📊 Spatial GIS Density Heatmap & Quality Analytics (`/admin/emergency-analytics`)
- **Bangladesh Emergency Density Radar**: Interactive Leaflet GIS clustering map plotting live incidents with color-coded severity markers (P1 Red, P2 Amber, P3 Blue, MCI Purple).
- **District Hotspot Aggregation**: Aggregated incident density across all 64 districts of Bangladesh.
- **Operational SLA & Response Quality Metrics**:
  - Total Incidents & Resolution Rate.
  - Average AI-to-Operator Dispatch Latency.
  - Average Unit Arrival Time.
  - SLA Compliance Rate ($P1 \le 18$m, $P2 \le 28$m target).
- **Incident Type & Severity Distributions**: Visual breakdown of cardiac, road accident, trauma, fire, and respiratory emergencies.

---

### 6. 🤝 Community First Responder (CFR) Network (`/community-responders/*`)
- **Volunteer Citizen Mobilization**: Registered medical volunteers, off-duty nurses, and trained citizens mobilize to provide CPR and bleeding control prior to ambulance arrival.
- **Volunteer Verification**: Supports certification tracking (CPR, BLS, First Aid, Paramedic, MBBS).
- **Proximity Alert Engine (`CfrAlert`)**: Automatically alerts available responders located within 2–5 km of a P1 cardiac or trauma incident.
- **1-Click Responder Workflow**:
  - Toggle duty availability (`toggle-availability`).
  - Accept incident alert (`alerts/{alert}/respond`).
  - Mark arrived on scene (`alerts/{alert}/arrived`).

---

### 7. 🚑 Provider Dispatch Console (`/provider/dispatches`)
- **Hospital & Ambulance Fleet Operations**: Dedicated console for service providers to manage their fleet and admissions.
- **Real-Time Dispatch Orders**: Review incoming emergency dispatches with patient condition, location, and severity.
- **Milestone Updates**: 1-Click buttons to transition units from En Route to On Scene, Transporting, and Completed.

---

### 8. ⚡ Instant Public Emergency Search (Zero Login Barrier)
- **1-Click Geolocation**: Instant HTML5 GPS detection with fallback to major Bangladeshi metropolitan hubs (Dhaka, Chattogram, Sylhet, Rajshahi, Khulna, Barishal, Rangpur, Mymensingh).
- **Haversine Distance Sorting**: Spherical distance calculation in kilometers, pre-filtered with latitude/longitude bounding boxes for peak performance.
- **7 Core Emergency Categories**:
  - 🏥 **Hospitals**: Real-time ICU bed counters, emergency ward availability, central oxygen pipelines, general bed count, and on-duty doctors.
  - 🚑 **Ambulance Services**: 24/7 dispatch hotlines, ICU ambulance support, paramedic on-board indicators.
  - 💊 **Pharmacies**: 24/7 operating indicators, emergency life-saving medicine inventory, home delivery support.
  - 🩸 **Blood Donors**: Blood group directory (A+, A-, B+, B-, AB+, AB-, O+, O-), 90-day cooldown tracker, privacy masking.
  - 👮 **Police Stations**: Thana emergency duty officer hotlines, sub-district jurisdiction coverage.
  - 🚒 **Fire Service Stations**: Rapid fire and rescue response teams, control room numbers.
  - 🔬 **Diagnostic Centers**: Urgent imaging, pathology, and digital report delivery status.
- **Interactive OpenStreetMap + Leaflet**: Split-screen responsive map view with custom SVG category pins, interactive radius adjustment (1km to 100km), auto-centering, and direct navigation links.

---

### 9. 🩺 Comprehensive Doctor & Specialist Directory (`/doctors`, `/doctors/{slug}`)
- **Universal Specialist Search**: Search doctors by name, medical specialty, qualifications/degrees (MBBS, FCPS, MRCP), department, designation, or affiliated hospital.
- **16+ Curated Medical Specializations**:
  - Cardiology, Emergency Medicine & Trauma, Neurology, Orthopedics, Pediatrics, Gynecology, General Surgery, Gastroenterology, Nephrology, Pulmonology, Dermatology, Ophthalmology, ENT, Internal Medicine, Oncology, Psychiatry.
- **Emergency & Schedule Filters**:
  - 🚨 **Emergency On-Call Doctors**: Filter doctors currently on emergency active duty (`emergency=1`).
  - 📅 **Available Today**: Filter specialists consulting today based on weekly visiting schedules (`available_today=1`).
- **Rich Doctor Profiles**: Doctor qualifications, consultation hours, fees (BDT), chamber number, 1-click appointment hotline, and recommended peer specialists from the same hospital.

---

### 10. 🩸 Privacy-Protected Blood Donor Directory (`/blood-donors`)
- **Strict Privacy Masking**: Direct phone numbers are masked to prevent spam and data scraping.
- **Emergency Contact Request Pipeline**: Citizens submit structured emergency patient requests (hospital name, units required, urgency), creating an auditable contact log.
- **90-Day Medical Cooldown**: Tracks eligibility countdown based on last donation date.
- **Donor Self-Registration**: Direct public registration form (`/blood-donors/register`).

---

### 11. 🌓 Light / Dark / System Adaptive Theme
- **Universal Theme Switcher**: Dropdown supporting **Light**, **Dark**, and **System Preference** modes.
- **Anti-FOUC Architecture**: Inline script in `<head>` checks `localStorage` and applies dark classes before DOM render, completely eliminating white flash.

---

### 12. 🇧🇩 Bangladesh Emergency Hotlines Bar
Sticky national emergency bar for instant 1-click calling:
- **999**: National Emergency Services (Police, Fire, Ambulance)
- **16163**: Shastho Batayan (National 24/7 Health Hotline)
- **109**: Women & Children Violence Helpline
- **333**: National Information & Citizen Services Helpline

---

## 🛠️ Technology Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.x (PHP 8.2+) | MVC architecture, FormRequests, Transactions, Policies |
| **AI Triage Engine** | Google Gemini API + Custom NLP | Bilingual LLM intake + Deterministic regex parser fallback |
| **Database Engine** | MySQL 8.0+ | Spatial coordinate indexes, composite search indexes, soft deletes |
| **Frontend Styling** | Tailwind CSS 4.0 | Modern utility CSS, dark mode design tokens, micro-animations |
| **Interactivity** | Alpine.js + Vanilla JS | Modal dialogs, real-time polling, async previews, audio recorder |
| **Mapping Engine** | Leaflet.js (v1.9.4) & OSM | OpenStreetMap tiles, custom SVG category pins, GIS radius circles |
| **Testing Suite** | PHPUnit 11 | **75 passed tests (429 assertions)** across 16 feature test suites |

---

## 🗺️ Key Application Routes

### 🚨 Emergency SOS, AI Intake & Live Incident Tracking
| Route | Method | Description |
| :--- | :--- | :--- |
| `/emergency/sos` | POST | 1-Tap instantaneous SOS emergency trigger with GPS |
| `/emergency/report` | GET | AI emergency report page with voice audio & symptom chips |
| `/emergency/report` | POST | Submit incident report for AI triage and dispatch |
| `/emergency/analyze-preview` | POST | Asynchronous live AI triage preview API |
| `/emergency/{reference}` | GET | Real-time incident live tracking timeline & Leaflet map |
| `/emergency/{reference}/status` | GET | JSON polling endpoint for live incident milestone updates |
| `/emergency/{reference}/cancel` | POST | Cancel active emergency incident with audit reasoning |

### 🤖 AI Emergency Assistant & Live Chatbot
| Route | Method | Description |
| :--- | :--- | :--- |
| `/emergency-assistant` | GET | Dedicated bilingual (Bengali/English) Emergency Assistant interface |
| `/emergency-assistant/conversations` | POST | Initialize a new conversational triage session |
| `/emergency-assistant/conversations/{id}`| GET | Retrieve message history and clinical assessment data |
| `/emergency-assistant/message` | POST | Process distress description & receive triage guidance |
| `/emergency-assistant/{conversation}/sos` | POST | Escalate conversation directly to live P1 SOS dispatch |
| `/emergency-assistant/resources/nearby` | GET | Query nearby verified hospitals based on user location |
| `/emergency-assistant/{conversation}/language` | POST | Toggle language preference (English / বাংলা) |

### 🤝 Community First Responders (CFR)
| Route | Method | Description |
| :--- | :--- | :--- |
| `/community-responders/register` | GET/POST | Register as a volunteer Community First Responder |
| `/community-responders/{id}/registered` | GET | Registration confirmation & status overview |
| `/community-responders/{responder}/toggle-availability` | POST | Toggle on-duty availability status |
| `/community-responders/alerts/{alert}/respond` | POST | Accept proximity emergency alert |
| `/community-responders/alerts/{alert}/arrived` | POST | Mark arrived on scene for CPR / first aid |

### 🌐 Public Resource Search & Healthcare
| Route | Method | Description |
| :--- | :--- | :--- |
| `/` | GET | Home landing page with 1-tap SOS, hero search, & category grid |
| `/search` | GET | Emergency search with Haversine distance & split map view |
| `/search/map-data` | GET | JSON GeoJSON feed for interactive map pins |
| `/resources/{slug}` | GET | Facility details (ICU counters, oxygen, affiliated doctors) |
| `/doctors` | GET | Doctor & Specialist Directory with specialty & schedule filters |
| `/doctors/{slug}` | GET | Detailed doctor profile, visiting schedule, & call hotline |
| `/blood-donors` | GET | Blood donor directory with masked phone numbers |
| `/blood-donors/register` | GET/POST | Voluntary blood donor registration |
| `/blood-donors/request` | POST | Submit emergency patient blood contact request |
| `/reports` | POST | Submit crowdsourced information correction report |

### 🏥 Emergency Service Provider (`/provider/*`)
| Route | Method | Description |
| :--- | :--- | :--- |
| `/provider/dashboard` | GET | Provider overview metrics and quick action controls |
| `/provider/resources` | GET/POST | Manage provider emergency facilities and listings |
| `/provider/resources/{id}/availability` | POST | 1-Click live facility availability toggle |
| `/provider/doctors` | GET/POST | Manage hospital doctors and visiting schedules |
| `/provider/doctors/{id}/emergency` | POST | 1-Click doctor emergency on-call status toggle |
| `/provider/dispatches` | GET | Provider emergency dispatch operations console |
| `/provider/dispatches/{id}/respond` | POST | Accept or decline emergency dispatch call |
| `/provider/dispatches/{id}/status` | POST | Transition dispatch status (En Route, Arrived, Complete) |
| `/provider/reports` | GET | Review citizen accuracy reports on facilities |

### 👑 System Administrator (`/admin/*`)
| Route | Method | Description |
| :--- | :--- | :--- |
| `/admin/dashboard` | GET | System-wide statistics and moderation overview |
| `/admin/emergency-command` | GET | **EOC Command Center**: Live emergency operations queue |
| `/admin/emergency-command/feed` | GET | Real-time JSON feed of active incidents and KPIs |
| `/admin/emergency-command/{id}/recommendations` | GET | Smart resource matching (nearest ambulance/hospital) |
| `/admin/emergency-command/{id}/dispatch` | POST | Execute emergency unit dispatch |
| `/admin/emergency-command/{id}/mci` | POST | Declare Mass Casualty Incident protocol |
| `/admin/emergency-analytics` | GET | **GIS Heatmap & Analytics**: Density radar & SLA audit |
| `/admin/emergency-analytics/heatmap-data` | GET | GeoJSON heatmap points feed |
| `/admin/resources/pending` | GET | Verification queue for provider-submitted listings |
| `/admin/resources/{id}/verify` | PATCH | Approve pending facility listing |
| `/admin/resources/{id}/reject` | PATCH | Reject listing with moderation reason |
| `/admin/users` | GET | Manage users and toggle account statuses |
| `/admin/reports` | GET | Resolve or dismiss citizen error reports |
| `/admin/categories` | GET/POST/PUT | Manage emergency categories and icon taxonomies |

---

## 🚀 Quick Setup & Installation

> [!TIP]
> For a detailed, step-by-step developer onboarding guide with troubleshooting FAQs and SQLite/MySQL options, see [setup.md](setup.md).

### Prerequisites
- PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `xml`, `bcmath` extensions
- Composer 2.x
- MySQL 8.0+ Server running on `127.0.0.1:3306`
- Node.js 18+ and npm

### 1. Clone & Install Dependencies
```bash
git clone https://github.com/your-username/Need_Help_Emergency_Resource_Finder.git
cd Need_Help_Emergency_Resource_Finder

composer install
npm install
```

### 2. Environment Configuration
Copy `.env.example` to `.env` and configure your database:
```bash
cp .env.example .env
```

Your `.env` settings:
```ini
APP_NAME="Need Help - Emergency Resource Finder"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (MySQL is default; for standard XAMPP/Laragon, DB_PASSWORD is empty "")
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=need_help
DB_USERNAME=root
DB_PASSWORD=

# Optional: Google Gemini AI for Advanced Triage (defaults safely to offline NLP if empty)
GEMINI_API_KEY=
GEMINI_MODEL=gemini-1.5-flash
```

Generate application encryption key:
```bash
php artisan key:generate
```

### 3. Run Migrations, Seeders & Link Storage
Ensure your MySQL database (`need_help`) is created (`CREATE DATABASE need_help;`), then run:
```bash
# Run database migrations and seed realistic Bangladesh emergency data
php artisan migrate:fresh --seed

# Create public storage symlink for uploaded files and media
php artisan storage:link
```

> **What gets seeded?**
> - **7 Categories**: Hospitals, Ambulance Services, Pharmacies, Blood Donors, Police Stations, Fire Service Stations, Diagnostic Centers.
> - **Emergency Facilities**: Dhaka Medical College Hospital (DMCH), Square Hospital, United Hospital, Chittagong Medical College Hospital (CMCH), 24/7 ambulances, pharmacies, and police stations.
> - **Specialist Doctors**: Senior consultants and emergency trauma physicians with visiting hours, consultation fees, chamber numbers, and emergency on-call statuses.
> - **Blood Donors**: Donors across all 8 blood groups with realistic donation dates, apheresis platelet eligibility, and masked phone privacy.
> - **Demo Incidents & CFR Responders**: Active emergency dispatches and registered first responders.

### 4. Build Assets & Start Server
```bash
# Terminal 1: Compile frontend assets (Vite + Tailwind CSS 4 + Alpine.js)
npm run build
# (or 'npm run dev' for hot module reloading during development)

# Terminal 2: Start Laravel development server
php artisan serve
```
Visit `http://localhost:8000` in your web browser.

---

## 🔑 Demo User Credentials

| Role | Email | Password | Access Area |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin@needhelp.bd` | `password123` | `/admin/dashboard`, `/admin/emergency-command` |
| **Medical Provider** | `square.hospital@needhelp.bd` | `password123` | `/provider/dashboard`, `/provider/dispatches` |
| **Citizen User** | `citizen@needhelp.bd` | `password123` | `/dashboard`, `/emergency/report` |

*(Quick 1-Click demo credential buttons are also provided directly on the Sign In page).*

---

## 🧪 Automated Testing Suite

The application includes **108 comprehensive Feature and Unit test cases** with **611 assertions** ensuring 100% test pass across all core modules:

```bash
php artisan test
```

### Test Coverage Breakdown
- 🚑 **`EmergencyIncidentTest`**: 1-Tap SOS creation, P1 auto-priority, unique reference generation, live timeline events, polling status, and cancellation auditing.
- 🧠 **`EmergencyAITriageTest`**: Deterministic AI parsing for Bengali/English cardiac, accident, and fire emergencies, intake controller processing, and preview triage API.
- 🚨 **`EmergencyCommandCenterTest`**: EOC dashboard access, active incident feed, nearest responder recommendation ranking, and operator dispatch execution.
- 📊 **`EmergencyAnalyticsTest`**: Summary metrics calculation, spatial heatmap points extraction, admin dashboard access, and heatmap JSON API.
- 🤝 **`CommunityFirstResponderTest`**: Volunteer registration, geofenced proximity alerting, alert acceptance, and on-scene arrival tracking.
- 🏥 **`HospitalRecommendationTest`**: Multi-factor hospital ranking (ICU beds, central oxygen, trauma capabilities) and bilingual pre-arrival first-aid guidance.
- 💥 **`MassCasualtyProtocolTest`**: MCI protocol declaration, multi-hospital surge patient distribution, and dynamic on-scene triage counts.
- ⏱️ **`ResponseTimePredictionTest`**: Traffic estimation with rush-hour congestion, weather delays, and urban prep latency calculations.
- 🚒 **`ResourceMatchingAndDispatchTest`**: Multi-factor resource matching, dispatch milestone transitions, and provider decline re-queuing.
- 🩺 **`DoctorDirectoryAndSearchTest`**: Specialist filtering, emergency on-call doctors, visiting schedule profiles, and provider doctor management.
- 🩸 **`BloodDonorPrivacyAndContactRequestTest`**: Phone privacy masking and emergency contact request dispatch.
- 🛡️ **`AuthenticationAndRoleAuthorizationTest`**: RBAC permissions, role middleware protection, and suspended user gating.
- 📋 **`AdminVerificationAndModerationTest`**: Facility approval/rejection workflows, citizen report resolution, and user status toggles.
- 📦 **`ProviderResourceManagementTest`**: Facility creation, availability status toggles, and unauthorized edit protection.
- 🔍 **`PublicEmergencySearchTest`**: Frictionless search, Haversine distance ordering, category filters, and GeoJSON map feed.
- 🤖 **`Chatbot/*` (11 Suites)**: Multi-turn emergency assessment, Bengali/Banglish speech loss & stroke detection, cardiac emergency FSM logic, Gemini fallback, hospital integration, chat SOS triggers, and input sanitization security.

```text
  Tests:    108 passed (611 assertions)
  Duration: 100% Passing
```

---

## 📂 Project Architecture

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── AdminResourceController.php
│   │   │   │   ├── AdminUserController.php
│   │   │   │   ├── AdminReportController.php
│   │   │   │   ├── AdminCategoryController.php
│   │   │   │   ├── EmergencyCommandCenterController.php (EOC live operations)
│   │   │   │   └── EmergencyAnalyticsController.php (GIS heatmaps & SLA audit)
│   │   │   ├── Provider/
│   │   │   │   ├── ProviderDashboardController.php
│   │   │   │   ├── ProviderResourceController.php
│   │   │   │   ├── ProviderAvailabilityController.php
│   │   │   │   ├── ProviderDoctorController.php
│   │   │   │   ├── ProviderDispatchController.php (Fleet dispatch console)
│   │   │   │   └── ProviderReportController.php
│   │   │   ├── Emergency/
│   │   │   │   └── CommunityResponderController.php (CFR volunteer network)
│   │   │   ├── Auth/ (Login, Register, Logout)
│   │   │   ├── EmergencyIntakeController.php (AI triage intake & preview)
│   │   │   ├── EmergencyIncidentPublicController.php (1-Tap SOS & Live Tracker)
│   │   │   ├── DoctorPublicController.php (Doctor directory search & profiles)
│   │   │   ├── EmergencySearchController.php (Haversine search & JSON feed)
│   │   │   ├── BloodDonorPublicController.php (Donor directory & contact requests)
│   │   │   ├── ResourcePublicController.php (Facility detail with ICU counters)
│   │   │   ├── ReportPublicController.php (Crowdsourced citizen reporting)
│   │   │   ├── FavoriteController.php (User bookmarking)
│   │   │   └── HomeController.php
│   │   ├── Middleware/ (RoleMiddleware, EnsureActiveUser)
│   │   └── Requests/ (Validation rules with auto-sanitization)
│   ├── Models/
│   │   ├── EmergencyIncident.php & EmergencyIncidentEvent.php
│   │   ├── EmergencyDispatch.php
│   │   ├── CommunityFirstResponder.php & CfrAlert.php
│   │   ├── Doctor.php
│   │   ├── Resource.php, ResourceDetail.php, ResourceOperatingHour.php, ResourceCategory.php
│   │   ├── BloodDonor.php & DonorContactRequest.php
│   │   ├── Report.php, SearchLog.php, User.php, UserFavorite.php
│   ├── Policies/ (ResourcePolicy, ReportPolicy)
│   └── Services/
│       ├── AI/
│       │   ├── GeminiEmergencyProvider.php (Google Gemini AI triage)
│       │   ├── DeterministicEmergencyParser.php (Bilingual Bengali/English fallback NLP)
│       │   ├── EmergencyIntakeService.php
│       │   └── SpeechToTextService.php (Voice audio processing)
│       ├── Dispatch/
│       │   ├── ResourceMatchingService.php (Multi-factor ranking)
│       │   ├── TrafficEstimationService.php
│       │   └── DispatchExecutionService.php
│       ├── Analytics/
│       │   ├── EmergencyAnalyticsService.php
│       │   └── ResponseTimePredictionService.php
│       ├── Emergency/
│       │   ├── CommunityResponderService.php
│       │   └── MassCasualtyProtocolService.php (MCI surge management)
│       ├── Hospital/
│       │   └── HospitalRecommendationService.php (ICU, oxygen, trauma matching)
│       ├── LocationSearchService.php (Haversine SQL + Bounding Box pre-filter)
│       ├── ResourceService.php
│       ├── BloodDonorService.php
│       └── VerificationService.php
├── database/
│   ├── migrations/ (18 structured MySQL tables with spatial and composite indexes)
│   └── seeders/ (Realistic facilities, doctors, blood donors, CFRs across Bangladesh)
├── resources/
│   ├── css/ (Tailwind styles with emergency pulse animations & Leaflet styling)
│   └── views/
│       ├── layouts/ (app, admin, provider with anti-FOUC dark mode script)
│       ├── components/ (navbar, footer, sos-modal, theme-toggle, map-view, resource-card, status-badge)
│       ├── home.blade.php (Hero search + 1-Tap SOS + Category cards + Hotlines)
│       ├── search/index.blade.php (Interactive Leaflet map + list split view)
│       ├── emergency/
│       │   ├── report.blade.php (AI triage intake form + voice input + symptom chips)
│       │   └── tracker.blade.php (Public real-time incident tracker + ETA + map)
│       ├── community/ (CFR volunteer registration & alert response)
│       ├── admin/
│       │   ├── emergency/
│       │   │   ├── command-center.blade.php (EOC live operations dashboard)
│       │   │   └── analytics.blade.php (Spatial GIS density heatmap & SLA radar)
│       │   └── (Verification queue, users, reports & categories)
│       ├── provider/
│       │   ├── dispatches/ (Provider ambulance & hospital dispatch board)
│       │   ├── doctors/ (Doctor roster management & emergency toggle)
│       │   └── resources/ (Facility management & availability toggle)
│       └── doctors/ & blood-donors/
└── tests/
    └── Feature/ (16 comprehensive feature test suites with DatabaseTransactions)
```

---

## 📄 License
This project is open-source software licensed under the **MIT License**.
