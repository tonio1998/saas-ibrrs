# 🏛️ Integrated Barangay Records and Services System (IBRSS)

A centralized, scalable web-based platform designed to digitize and streamline barangay operations, including resident records, household management, and service tracking.

---

## 🚀 Overview

The Integrated Barangay Records and Services System (IBRSS) is built to:

- Improve data organization and retrieval
- Streamline barangay operations
- Replace manual processes with a digital system
- Provide scalable and modular architecture for future expansion

---

## 🎯 Objectives

- Digitize barangay records
- Centralize data access
- Enable advanced filtering and reporting
- Improve transparency and efficiency
- Support modular system expansion

---

## 🛠️ Tech Stack

### Backend
- Laravel
- MySQL
- Yajra DataTables

### Frontend
- Blade Components
- Bootstrap 5
- jQuery
- Vite

---

## 📦 Core Modules

### 🏠 Household Management
- Create and manage households
- Assign household head
- Associate households with Purok
- Track number of residents
- Advanced filtering:
    - Purok
    - Head status
    - Date created

### 👤 Resident Management
- Full resident profiling
- Link residents to households
- Demographics:
    - Gender
    - Civil Status
    - Occupation
    - Voter status
- Auto-computed age
- Advanced filtering:
    - Name
    - Age range
    - Gender
    - Civil status
    - Household
    - Voter status

---

## 🧭 Planned Modules

- Barangay Certificates Issuance
- Clearance Management
- Financial Tracking
- Document Tracking System
- Dashboard & Analytics
- Mobile Integration (React Native)

---

## 🧠 System Architecture

```
Frontend (Blade + JS)
    ↓
Reusable Components (Modal, DataTable)
    ↓
AJAX (DataTables)
    ↓
Laravel Controllers
    ↓
Eloquent ORM
    ↓
MySQL Database
```

---

## 🔑 Key Features

### Reusable Component System
- `<x-modal>` for dialogs
- `<x-datatable>` for tables
- Modular UI architecture

### Advanced Filtering System
- Uses `data-filter="field"` convention
- Dynamic and reusable across modules
- Modal-based filtering

Example:
```
<input data-filter="name">
<select data-filter="gender">
```

### Cache-First Data Loading
```
1. Check cache
2. If valid → render
3. Else → fetch from API
```

### Server-Side Processing
- Efficient handling of large datasets
- Pagination, sorting, filtering
- Optimized queries

### Premium UI/UX
- macOS-style modal design
- Apply-based filtering (no auto-fetch)
- Keyboard support (Enter / ESC)

---

## ⚙️ Installation

### 1. Clone Repository
```
git clone <repository-url>
cd ibrss
```

### 2. Install Dependencies
```
composer install
npm install
```

### 3. Environment Setup
```
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
```
DB_DATABASE=ibrss
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
```
php artisan migrate
```

### 6. Run Application
```
php artisan serve
npm run dev
```

---

## 🗄️ Database Structure

### Households
- id
- household_code
- purok_id
- head_id
- created_at

### Residents
- id
- FirstName
- LastName
- BirthDate
- gender
- CivilStatus
- Occupation
- is_voter
- household_id
- created_by

---

## ⚡ Performance Optimization

### Database Indexing
```
INDEX(gender)
INDEX(CivilStatus)
INDEX(is_voter)
INDEX(BirthDate)
INDEX(created_at)
```

### Query Optimization
- Use `with()` to avoid N+1 queries
- Use `whereHas()` for relationships
- Minimize repeated computations

### Frontend Optimization
- Cache-first rendering
- Apply-only filtering
- Efficient DOM updates

---

## 🧪 Debugging Guide

### Check Filters
```
console.log(getFilters())
```

### Check Network Requests
- Open DevTools → Network → XHR
- Inspect request payload

### Common Issues

| Issue | Cause |
|------|------|
| Filters not working | Missing `data-filter` |
| No results | Backend mismatch |
| Slow queries | Missing indexes |
| Duplicate rows | DataTable re-init |

---

## 🔄 Scalability

- Easily add new modules
- Reuse modal + datatable system
- Ready for API and mobile integration

---

## 🔐 Security

- CSRF protection (Laravel)
- Encrypted route parameters
- Input validation
- Role-based access (future)

---

## 🔮 Future Enhancements

- Role & Permission System
- Audit Logs
- File Uploads
- Offline Sync (mobile)
- GIS Integration
- Multi-barangay support (multi-tenant)

---

## 🧑‍💻 Developer Notes

- Use `data-filter` convention
- Avoid hardcoded selectors
- Use Blade components
- Keep logic modular

---

## 📌 Development Commands

To continue development with ChatGPT:

- continue datatable system
- continue residents module
- continue households module
- continue modal filter system

---

## ✅ Summary

IBRSS provides:

- Centralized barangay data management
- Scalable modular architecture
- High-performance data handling
- Clean and modern UI/UX
- Future-ready system design
