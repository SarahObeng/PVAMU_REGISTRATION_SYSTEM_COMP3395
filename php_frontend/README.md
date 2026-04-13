# PVAMU Registration Analytics Admin Dashboard

A real-time, interactive analytics dashboard for Prairie View A&M University's
course registration system. Built with PHP, MySQL, Chart.js, and custom CSS
using PVAMU's official purple-and-gold brand identity.

---

## Table of Contents

1. [Project Structure](#project-structure)
2. [Prerequisites](#prerequisites)
3. [Quick Start — XAMPP (Local)](#quick-start--xampp-local)
4. [Quick Start — Replit](#quick-start--replit)
5. [Database Setup](#database-setup)
6. [Configuration](#configuration)
7. [Features](#features)
8. [Business Questions Answered](#business-questions-answered)
9. [API Endpoints](#api-endpoints)
10. [Tech Stack](#tech-stack)

---

## Project Structure

```
pvamu_dashboard/
├── config/
│   └── db.php                  ← Database credentials (edit this)
├── api/
│   ├── kpi.php                 ← KPI headline stats
│   ├── waitlist_by_course.php  ← Q1: Courses with largest waitlists
│   ├── under_enrolled.php      ← Q2: Under-enrolled courses
│   ├── seat_utilisation.php    ← Q3: Seat fill percentage per course
│   ├── waitlist_by_major.php   ← Q4: Waitlist impact by major
│   ├── recommendations.php     ← Q5: Course recommendations
│   └── filters.php             ← Filter dropdown options
├── assets/
│   ├── css/style.css           ← Full PVAMU-branded stylesheet
│   └── js/dashboard.js         ← Chart.js, AJAX polling, interactivity
├── admin_dashboard.php         ← Main dashboard page
├── index.php                   ← Login / landing page
└── README.md
```

---

## Prerequisites

- PHP 7.4+ or PHP 8.x
- MySQL 5.7+ / MariaDB 10.4+
- Apache web server (XAMPP, LAMP, or Replit)
- Internet connection (CDN assets: Chart.js, Font Awesome, Google Fonts)

---

## Quick Start — XAMPP (Local)

1. **Install XAMPP** from https://www.apachefriends.org/

2. **Copy project** to XAMPP's web root:
   ```
   C:\xampp\htdocs\pvamu_dashboard\   (Windows)
   /Applications/XAMPP/htdocs/pvamu_dashboard/  (macOS)
   ```

3. **Start Apache and MySQL** from the XAMPP Control Panel.

4. **Import the database** via phpMyAdmin:
   - Open http://localhost/phpmyadmin
   - Create a new database named `pvamu_registration`
   - Click **Import** and upload `pvamu_registration.sql`

5. **Update credentials** in `config/db.php`:
   ```php
   define('DB_USER', 'root');   // your MySQL username
   define('DB_PASS', '');       // your MySQL password (blank by default in XAMPP)
   ```

6. **Open the dashboard** at:
   ```
   http://localhost/pvamu_dashboard/
   ```

---

## Quick Start — Replit

1. Create a new **PHP** Repl at https://replit.com

2. Upload all project files preserving the folder structure.

3. In the Replit shell, set up MySQL (if using Replit's built-in DB, switch to
   the PDO adapter or connect to a remote MySQL instance).

4. Update `config/db.php` with your Replit database credentials.

5. Click **Run** — Replit will serve `index.php` automatically.

---

## Database Setup

The dashboard connects to the `pvamu_registration` MySQL database which
contains these tables:

| Table         | Description                                  |
|---------------|----------------------------------------------|
| `student`     | Student profiles, classification, major, graduation |
| `major`       | Degree programs                              |
| `course`      | Course catalogue                             |
| `section`     | Sections offered per semester                |
| `enrollment`  | Student enrollments and grades               |
| `waitlist`    | Waitlisted students with priority scores     |
| `degree_plan` | Required/elective courses per major          |

Import the provided SQL dump (`pvamu_registration.sql`) to create and seed all tables.

---

## Configuration

All database settings live in **`config/db.php`**:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // ← your MySQL username
define('DB_PASS', '');               // ← your MySQL password
define('DB_NAME', 'pvamu_registration');
define('DB_PORT', 3306);
```

That is the **only file** you need to edit.

---

## Features

- **Real-Time Polling** — AJAX fetch every 10 seconds keeps all charts current
- **4 Interactive Bar Charts** — built with Chart.js with hover tooltips
- **KPI Stat Cards** — animated counters for 4 headline metrics
- **Course Recommendations Table** — searchable, driven by degree plan data
- **Semester & Major Filters** — dropdown filters on all charts simultaneously
- **Toast Notifications** — lightweight feedback after each data refresh
- **Settings Panel** — toggle auto-refresh, animations, and live indicator
- **Responsive Layout** — mobile-friendly with collapsible sidebar
- **PVAMU Brand** — official purple (#4B2E83) and gold (#FFD100) colour palette

---

## Business Questions Answered

| # | Question | Visualisation |
|---|----------|---------------|
| Q1 | Which courses have the largest waitlists? | Bar chart (top 10) |
| Q2 | Which courses are under-enrolled (<50% capacity)? | Bar chart |
| Q3 | What percentage of seats are filled per course? | Horizontal bar (colour-coded) |
| Q4 | Which majors are most affected by long waitlists? | Bar chart |
| Q5 | What courses should a student take next? | Searchable table |

---

## API Endpoints

All endpoints return JSON. Optional query parameters are shown in brackets.

| Endpoint | Parameters | Description |
|----------|------------|-------------|
| `api/kpi.php` | — | Four KPI headline numbers |
| `api/waitlist_by_course.php` | `[semester]` | Waitlist counts per course |
| `api/under_enrolled.php` | `[semester]` | Sections below 50% fill |
| `api/seat_utilisation.php` | `[semester]` | Fill % per course |
| `api/waitlist_by_major.php` | — | Waitlist impact per major |
| `api/recommendations.php` | `[student_id]`, `[major_id]` | Next-course suggestions |
| `api/filters.php` | — | Dropdown option lists |

All queries use **prepared statements** to prevent SQL injection.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.x |
| Database | MySQL / MariaDB |
| Frontend | HTML5, CSS3, JavaScript (ES2022) |
| Charts | Chart.js 4.x |
| Icons | Font Awesome 6 |
| Fonts | Playfair Display, DM Sans (Google Fonts) |
| Realtime | AJAX polling (Fetch API) |
| Server | Apache (XAMPP / LAMP) |

---

© Prairie View A&M University — Office of the Registrar
