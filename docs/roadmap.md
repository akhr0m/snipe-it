# 📋 ROADMAP: BAST Feature Package untuk Snipe-IT

**Version:** 1.0  
**Last Updated:** 2024  
**Status:** Planning Phase

---

## 📌 EXECUTIVE SUMMARY

Proyek ini adalah implementasi **Fitur BAST (Berita Acara Serah Terima Aset)** sebagai **Package Terpisah** di dalam Snipe-IT, tanpa mengubah core files aplikasi utama. Fitur ini memungkinkan pencatatan formal dan terstruktur saat aset diserahkan dari pemberi ke penerima dengan dokumentasi lengkap dan kemampuan print.

**Keunikan Approach:**
- Fitur 100% isolated dalam `/package/feature/bast`
- Zero conflict dengan upstream Snipe-IT updates
- Modular, reusable, dan maintainable jangka panjang
- Hanya 1 baris edit di core file (sidebar menu injection)

---

## 🎯 TUJUAN FITUR

### Primary Goals
1. **Dokumentasi Resmi Aset Handover** - Menciptakan record formal setiap kali aset diserahkan antar pihak
2. **Audit Trail Lengkap** - Menyimpan snapshot data pemberi, penerima, dan aset untuk keperluan audit
3. **Kemudahan Reporting** - Menyediakan laporan daftar BAST per user dan per aset
4. **Print & Archive** - Memungkinkan print BAST untuk ditandatangani dan diarsipkan

### Secondary Goals
1. Integrasi seamless dengan data User dan Asset di Snipe-IT
2. Performance optimal untuk query besar (snapshot vs real-time)
3. Support untuk ekspor laporan (future enhancement)

---

## 🔴 PROBLEM STATEMENT

### Current State
Snipe-IT memiliki fitur assignment aset ke user, tetapi:
- **Tidak ada dokumentasi resmi** saat aset diserahkan
- **Sulit untuk tracking** siapa yang memberikan dan menerima
- **Data tidak tersimpan sebagai snapshot** - jika user/department berubah, history hilang
- **Tidak ada format print standar** untuk arsip fisik

### Implikasi
- Kesulitan audit karena tidak ada bukti formal
- Konflik saat ada claim tentang aset (siapa yang terakhir menerima?)
- Data history tidak konsisten dengan perubahan master data

---

## 💡 SOLUSI OVERVIEW

### Design Philosophy
**"Capture Once, Use Many"** - Snapshot data saat aset diserahkan, gunakan data itu untuk laporan dan print selamanya.

### Key Concepts

#### 1. **Two-Layer Data Approach**
```
Layer 1: Real-time (Live Data)
├─ Asset (dari tabel assets Snipe-IT)
├─ User (dari tabel users Snipe-IT)
└─ Relations

Layer 2: Historical (Snapshots)
├─ BastSnapshot (record formal setiap handover)
├─ BastUserSnapshot (summary user & BAST yang di-assign)
└─ Data immutable untuk audit purposes
```

#### 2. **Sub-Application Model**
BAST bukan hanya "fitur tambahan", tapi **mini-aplikasi terpisah** dengan:
- Route terpisah (`/bast`)
- View layout terpisah (`bast::`)
- Controller terpisah
- Database terpisah

#### 3. **Menu Injection Strategy**
- Sidebar Snipe-IT ditambahkan **1 submenu saja**: "BAST Manager"
- Klik submenu → masuk ke **sub-app BAST** yang punya UI dan menu sendiri
- User tidak perlu pindah-pindah ke halaman core Snipe-IT

---

## 🏗️ ARSITEKTUR & DESIGN PHILOSOPHY

### Package Structure Philosophy

```
Prinsip Isolasi:
┌─────────────────────────────────────────┐
│       SNIPE-IT CORE (Untouched)        │
│  ├─ App/Models/Asset                   │
│  ├─ App/Models/User                    │
│  └─ Resources/Views/Layouts            │
└─────────────────────────────────────────┘
              ▲ (read-only)
              │ (query relationship)
              │
┌─────────────────────────────────────────┐
│    PACKAGE/FEATURE/BAST (Custom)       │
│  ├─ ServiceProvider                    │
│  ├─ Controllers/                       │
│  ├─ Models/ (Snapshot only)            │
│  ├─ Routes/                            │
│  ├─ Resources/Views/                   │
│  └─ Database/Migrations/               │
└─────────────────────────────────────────┘
```

### Separation of Concerns

| Layer | Tanggung Jawab | Owner |
|-------|---|---|
| **Presentation** | UI, Views, Frontend Logic | BastController, Blade Templates |
| **Business Logic** | Snapshot creation, Query filtering | BastService (future) |
| **Data Access** | Query & Snapshot Models | BastSnapshot, BastUserSnapshot |
| **Integration** | Bridge dengan Snipe-IT core | ServiceProvider, custom.php config |

---

## 📊 DATA MODEL & SCHEMA

### Entitas Utama

#### 1. **BastSnapshot**
**Tujuan:** Record formal setiap serah-terima aset

**Karakteristik:**
- Immutable (sekali dibuat, tidak berubah)
- Menyimpan **snapshot** data pemberi, penerima, aset (bukan reference)
- Satu record = satu event serah-terima

**Apa yang disimpan (snapshot):**
- Data asset (nama, tag, serial, model, category)
- Data penerima (nama, title, department, company)
- Data pemberi (nama, title, department)
- Tanggal serah-terima
- Nomor BAST unik
- Status (draft/final/cancelled)
- Full JSON data untuk edge cases

**Relasi:**
- `belongsTo User` (penerima)
- `belongsTo Asset` (reference ke asset original)

---

#### 2. **BastUserSnapshot**
**Tujuan:** Summary view - user mana saja yang punya BAST & statistik

**Karakteristik:**
- Digenerate secara periodik (misal saat ada BAST baru)
- Tidak real-time (bisa cache/refresh)
- Untuk laporan "Daftar User" di UI

**Apa yang disimpan:**
- User info (nama, email, title, department, company)
- Jumlah BAST yang di-assign
- List nomor BAST (array JSON)
- Last BAST date

**Relasi:**
- `belongsTo User` (one-to-one)

---

#### 3. **Supporting Data (dari Snipe-IT)**
Query relationships saja, tidak copy:
- `Asset` - ambil info aset saat membuat snapshot
- `User` - ambil info user saat membuat snapshot
- `Department` - relasi dari User
- `Company` - relasi dari User/Asset

---

### Schema Details

#### BastSnapshot Table
```
Columns:
├─ id (PK)
├─ asset_id (FK) → assets
├─ user_id (FK) → users (penerima)
├─ nomor_bast (string, UNIQUE, indexed)
├─ [Asset Data - Snapshot]
│  ├─ asset_name
│  ├─ asset_tag
│  ├─ serial_number
│  ├─ model_name
│  └─ category_name
├─ [Penerima Data - Snapshot]
│  ├─ penerima_nama
│  ├─ penerima_title
│  ├─ penerima_department
│  └─ penerima_company
├─ [Pemberi Data - Snapshot]
│  ├─ pemberi_nama
│  ├─ pemberi_title
│  └─ pemberi_department
├─ tanggal_serah (datetime, indexed with user_id)
├─ status (enum: draft|final|cancelled)
├─ snapshot_data (JSON - full data backup)
├─ created_at, updated_at
└─ deleted_at (soft delete)

Indexes:
├─ nomor_bast (UNIQUE)
├─ (user_id, tanggal_serah) - untuk query user's BAST list
└─ asset_id - untuk query asset history
```

#### BastUserSnapshot Table
```
Columns:
├─ id (PK)
├─ user_id (FK, UNIQUE) → users
├─ user_name (snapshot)
├─ user_email (snapshot)
├─ user_title (snapshot)
├─ user_department (snapshot)
├─ user_company (snapshot)
├─ jumlah_bast (int)
├─ bast_list (JSON array of nomor_bast)
├─ last_bast_date (datetime)
├─ created_at, updated_at

Indexes:
├─ user_id (UNIQUE)
└─ last_bast_date (untuk sorting)
```

---

## 🧩 KOMPONEN-KOMPONEN YANG DIPERLUKAN

### Core Components (Must Have)

#### 1. **ServiceProvider**
- Mendaftarkan routes
- Mendaftarkan views namespace
- Mendaftarkan migrations
- Publish config (if any)
- **File:** `package/feature/bast/src/ServiceProvider.php`

#### 2. **Controllers**
- `BastController` - handle BAST list, show, print
- `BastUserController` - handle user list, user detail
- **Location:** `package/feature/bast/src/Controllers/`

#### 3. **Models**
- `BastSnapshot` - model untuk bast_snapshots table
- `BastUserSnapshot` - model untuk bast_user_snapshots table
- **Location:** `package/feature/bast/src/Models/`

#### 4. **Routes**
- Route group dengan prefix `/bast`
- Routes untuk: dashboard, list BAST, list user, show detail, print
- **Location:** `package/feature/bast/src/Routes/routes.php`

#### 5. **Migrations**
- Create bast_snapshots table
- Create bast_user_snapshots table
- **Location:** `package/feature/bast/database/migrations/`

#### 6. **Views**
- `layout.blade.php` - main layout sub-app BAST
- `dashboard.blade.php` - landing page BAST
- `bast-list.blade.php` - daftar BAST milik user login
- `bast-detail.blade.php` - detail satu BAST
- `bast-print.blade.php` - print BAST (siap print ke kertas)
- `user-list.blade.php` - daftar semua user yang punya BAST
- `user-detail.blade.php` - detail user + BAST list-nya
- **Location:** `package/feature/bast/resources/views/`

#### 7. **Menu Integration (Core File Edit - 1 line)**
- `config/custom-menu.php` - add BAST menu entry
- `resources/views/layouts/partials/sidebar.blade.php` - inject custom menu (1 baris)
- **Location:** Root config & views (Snipe-IT core)

### Supporting Components (Nice to Have)

#### 8. **Services/Repositories** (Future)
- `BastService` - business logic (create snapshot, update stats)
- `BastRepository` - query optimization

#### 9. **Requests/Validation** (Future)
- `CreateBastRequest` - validation saat create BAST
- `UpdateBastRequest` - validation saat update

#### 10. **Jobs** (Future)
- `GenerateBastUserSnapshot` - queued job untuk refresh user snapshots
- `ExportBastReport` - export BAST data to CSV/Excel

#### 11. **API Resources** (Future)
- `BastResource` - API response formatting (jika nanti pakai API)
- `BastUserResource` - API response formatting

---

## 👥 USER JOURNEY & WORKFLOWS

### User Persona

#### 1. **Admin/IT Manager**
- Peran: **Pemberi Aset** (yang menyerahkan aset)
- Akses: Seluruh BAST, bisa lihat semua user
- Flow: Create BAST → Assign user → Print → Archive

#### 2. **Regular User**
- Peran: **Penerima Aset**
- Akses: BAST yang di-assign ke dirinya saja
- Flow: View my BAST → Print → Sign & Archive

#### 3. **HR/Supervisor**
- Peran: **Reporter**
- Akses: Lihat laporan user & BAST
- Flow: View all users with BAST → Export report

---

### Workflow 1: Serah Terima Aset (Admin)

```
START
  ↓
[Admin Dashboard] → Navigate ke BAST Manager
  ↓
[BAST Dashboard] → Klik "Create New BAST" (future enhancement)
  ↓
[Create Form] → Input data:
                 ├─ Asset selection
                 ├─ Recipient user selection
                 └─ Serah terima date
  ↓
[System] → Create BastSnapshot record
  ↓
[System] → Update BastUserSnapshot (refresh stats)
  ↓
[View BAST Detail] → Show created BAST with all data
  ↓
[Print Button] → Generate PDF-ready view
  ↓
[User Print] → Print ke kertas → Sign → Archive
  ↓
END
```

---

### Workflow 2: Lihat BAST Saya (Regular User)

```
START
  ↓
[Dashboard/Menu] → Klik "BAST Manager"
  ↓
[BAST Dashboard] → Welcome screen dengan quick stats
  ↓
[Click "Daftar BAST"] → List semua BAST yang di-assign ke user
  ↓
[Filter/Search] → (optional) cari BAST tertentu
  ↓
[Select BAST] → Klik salah satu BAST
  ↓
[View Detail] → Lihat lengkap:
              ├─ Asset info
              ├─ Pemberi info
              └─ Tanggal serah
  ↓
[Print Button] → Print untuk arsip/signature
  ↓
END
```

---

### Workflow 3: Lihat Daftar User (Admin/HR)

```
START
  ↓
[BAST Dashboard] → Klik "Daftar User"
  ↓
[User List Page] → Tampilkan semua user + stats:
                   ├─ Nama user
                   ├─ Jumlah BAST
                   ├─ Last BAST date
                   └─ Action: View detail
  ↓
[Sort/Filter] → Urutkan by last date, search by name, etc.
  ↓
[Select User] → Klik user tertentu
  ↓
[User Detail] → Tampilkan:
              ├─ User info snapshot
              ├─ List semua BAST user ini
              └─ Export option (future)
  ↓
[From List] → Klik salah satu BAST
  ↓
[View BAST Detail] → (sama seperti workflow 2)
  ↓
END
```

---

## 🔄 DATA FLOW

### Flow 1: Creating BAST (Serah Terima)

```
Request dari Admin
    ↓
BastController::create()
    ↓
Validate input
    ↓
Query Snipe-IT:
├─ Asset::find($assetId)
├─ User::find($userId)
├─ Department dari User
└─ Company dari User
    ↓
Create BastSnapshot:
├─ Store dari data real-time
├─ Generate nomor_bast (unique)
├─ Set status = 'draft'
└─ Snapshot semua data ke JSON
    ↓
Update BastUserSnapshot:
├─ Check apakah user ini ada di snapshot
├─ Update jumlah_bast
├─ Update bast_list array
└─ Update last_bast_date
    ↓
Response Success + Redirect to detail
    ↓
END
```

---

### Flow 2: Querying BAST List (User's BAST)

```
Request dari User Login
    ↓
BastController::index()
    ↓
Query BastSnapshot:
├─ WHERE user_id = Auth::id()
├─ ORDER BY tanggal_serah DESC
├─ PAGINATE 15
    ↓
Loop setiap BAST:
├─ Format data untuk display
└─ Add action buttons (view, print)
    ↓
Render View (bast::bast-list)
    ↓
Response HTML
    ↓
END
```

---

### Flow 3: Print BAST

```
Request BastController::print($bastId)
    ↓
Retrieve BastSnapshot
    ↓
Authorize (user own BAST or admin?)
    ↓
If fail → Abort 403
    ↓
If ok → Render view (bast::bast-print)
    ↓
View contains:
├─ Company header
├─ BAST number & date
├─ Asset details
├─ Penerima info
├─ Pemberi info
├─ Signature boxes
└─ CSS @media print
    ↓
Client browser trigger print dialog
    ↓
User print ke paper
    ↓
END
```

---

## 📅 IMPLEMENTATION PHASES

### Phase 1: Foundation (Week 1-2)
**Output:** Package structure + Database + Models

#### Milestones:
- [ ] Create `/package/feature/bast` directory structure
- [ ] Create `ServiceProvider.php`
- [ ] Create migrations untuk `bast_snapshots` dan `bast_user_snapshots`
- [ ] Create models: `BastSnapshot`, `BastUserSnapshot`
- [ ] Register ServiceProvider di `config/app.php`
- [ ] Run migrations (verify schema)
- [ ] **Deliverable:** Database ready, models accessible

---

### Phase 2: Backend Logic (Week 2-3)
**Output:** Controllers + Routes + Business Logic

#### Milestones:
- [ ] Create `BastController` dengan methods: dashboard, index, show, print
- [ ] Create `BastUserController` dengan methods: index, show
- [ ] Create routes file dan register semua endpoints
- [ ] Add relationships di models
- [ ] Test controller responses (Postman/manual)
- [ ] **Deliverable:** All endpoints working, return correct data

---

### Phase 3: Frontend - Views (Week 3-4)
**Output:** All Blade templates + UI

#### Milestones:
- [ ] Create base layout (`layout.blade.php`)
- [ ] Create dashboard view
- [ ] Create BAST list view (bast-list.blade.php)
- [ ] Create BAST detail view (bast-detail.blade.php)
- [ ] Create BAST print view (bast-print.blade.php) - CSS print-friendly
- [ ] Create user list view (user-list.blade.php)
- [ ] Create user detail view (user-detail.blade.php)
- [ ] Add styling (Bootstrap/Tailwind - sesuai Snipe-IT theme)
- [ ] **Deliverable:** All pages render correctly, responsive design

---

### Phase 4: Integration & Menu (Week 4)
**Output:** Sidebar menu + Config + Testing

#### Milestones:
- [ ] Create `config/custom-menu.php`
- [ ] Create custom menu component view
- [ ] Add View Composer di ServiceProvider
- [ ] Edit sidebar.blade.php (+1 line menu injection)
- [ ] Register custom menu di `config/app.php`
- [ ] Test menu appearance di sidebar
- [ ] Test all navigation flows
- [ ] **Deliverable:** Menu integrated, no conflicts

---

### Phase 5: Polish & Testing (Week 5)
**Output:** QA + Documentation + Optimization

#### Milestones:
- [ ] E2E testing (create BAST → list → print)
- [ ] Authorization testing (user hanya lihat BAST sendiri)
- [ ] Permission testing (admin bisa lihat semua)
- [ ] Print testing (actual browser print)
- [ ] Performance check (query optimization if needed)
- [ ] Responsive design testing (mobile/tablet)
- [ ] Create documentation (for future developers)
- [ ] **Deliverable:** Stable, tested, documented

---

### Phase 6: Enhancement & Iteration (Optional)
**Output:** Extended features

#### Future Enhancements:
- [ ] Create BAST from UI (currently manual DB insert)
- [ ] Edit/Update BAST (if needed)
- [ ] Bulk operations (print multiple BAST)
- [ ] Export to Excel/PDF
- [ ] Email notifications
- [ ] Approval workflow
- [ ] Signature capture (digital)
- [ ] API endpoints
- [ ] Dashboard analytics/charts

---

## 📁 FILE STRUCTURE

```
project-root/
│
├── package/
│   └── feature/
│       └── bast/
│           ├── src/
│           │   ├── ServiceProvider.php          [Create from scratch]
│           │   ├── Controllers/
│           │   │   ├── BastController.php        [CRUD + Print BAST]
│           │   │   └── BastUserController.php    [User listing & detail]
│           │   ├── Models/
│           │   │   ├── BastSnapshot.php          [BAST record model]
│           │   │   └── BastUserSnapshot.php      [User BAST summary]
│           │   └── Routes/
│           │       └── routes.php                [All BAST routes]
│           │
│           ├── resources/
│           │   └── views/
│           │       ├── layout.blade.php          [Sub-app main layout]
│           │       ├── dashboard.blade.php       [BAST dashboard/welcome]
│           │       ├── bast-list.blade.php       [Daftar BAST user]
│           │       ├── bast-detail.blade.php     [Detail 1 BAST]
│           │       ├── bast-print.blade.php      [Print-friendly BAST]
│           │       ├── user-list.blade.php       [Daftar semua user with BAST]
│           │       └── user-detail.blade.php     [Detail user + BAST list]
│           │
│           ├── database/
│           │   └── migrations/
│           │       ├── 2024_01_01_000000_create_bast_snapshots_table.php
│           │       └── 2024_01_01_000001_create_bast_user_snapshots_table.php
│           │
│           ├── composer.json                     [Package metadata]
│           └── README.md                         [Package documentation]
│
├── config/
│   └── custom-menu.php                          [BAST menu config] [EDIT OR CREATE]
│
├── routes/
│   └── custom.php                               [Custom routes] [CREATE IF NOT EXISTS]
│
├── resources/views/
│   ├── layouts/
│   │   └── partials/
│   │       └── sidebar.blade.php                [ADD 1 LINE INJECTION] [CORE EDIT]
│   └── custom/
│       └── components/
│           └── sidebar-menu.blade.php           [Custom menu component] [CREATE]
│
└── config/
    └── app.php                                  [Register ServiceProvider] [CORE EDIT]
```

---

## 📦 DEPENDENCIES & REQUIREMENTS

### System Requirements
- Laravel 11+ (Snipe-IT uses Laravel)
- PHP 8.2+
- MySQL/PostgreSQL dengan support JSON

### Laravel Packages
- `illuminate/support` (built-in)
- `illuminate/database` (built-in)
- No external dependencies needed ✅

### Custom Integration Points
- Snipe-IT `App\Models\User`
- Snipe-IT `App\Models\Asset`
- Snipe-IT `App\Models\Department` (optional relation)
- Snipe-IT `App\Models\Company` (optional relation)
- Snipe-IT Auth system (middleware)

---

## ✅ SUCCESS CRITERIA

### Functional Requirements (Must Pass)
1. **Sidebar Menu**
   - [ ] "BAST Manager" menu muncul di sidebar
   - [ ] Click menu → redirect ke `/bast`
   - [ ] No sidebar changes visible (clean injection)

2. **Dashboard Page**
   - [ ] Load `/bast` → display dashboard
   - [ ] Show welcome message & quick stats
   - [ ] Navigation link ke "Daftar BAST" dan "Daftar User"

3. **BAST List Page**
   - [ ] User lihat hanya BAST yang di-assign ke dia
   - [ ] Pagination works (15 per page)
   - [ ] Display: asset name, date, status
   - [ ] Action links: view, print

4. **BAST Detail Page**
   - [ ] Load BAST data lengkap
   - [ ] Authorization check (user only see own BAST)
   - [ ] Display: penerima, pemberi, asset, tanggal
   - [ ] Print button works

5. **Print BAST**
   - [ ] Render print-friendly view
   - [ ] CSS media print applied
   - [ ] Actual browser print works
   - [ ] Signature boxes visible
   - [ ] Output looks professional

6. **User List Page**
   - [ ] Load all users with BAST
   - [ ] Show: nama, jumlah BAST, last date
   - [ ] Sorting by date works
   - [ ] Search/filter (future nice-to-have)

7. **User Detail Page**
   - [ ] Load user snapshot data
   - [ ] Display user info
   - [ ] Show list of user's BAST
   - [ ] Can click BAST to view detail

### Non-Functional Requirements
1. **Code Quality**
   - [ ] No errors di logs
   - [ ] Controllers follow PSR-12
   - [ ] Models have proper relationships
   - [ ] Views are DRY (no duplicate code)

2. **Performance**
   - [ ] Dashboard load < 2 seconds
   - [ ] List queries use proper indexes
   - [ ] Pagination optimized

3. **Git Strategy**
   - [ ] All code in `/package/feature/bast`
   - [ ] Core file edits minimal (config + 1 blade line)
   - [ ] No conflicts saat rebase upstream

4. **Security**
   - [ ] Authorization checks on all routes
   - [ ] User not see other user's BAST
   - [ ] Admin can see all

5. **Documentation**
   - [ ] Inline code comments
   - [ ] README.md for the package
   - [ ] This roadmap updated

---

## ⚠️ RISKS & MITIGATION

### Risk 1: Database Migration Order
**Risk:** Migrations run before ServiceProvider registered
**Mitigation:** Register ServiceProvider early in `config/app.php`
**Check:** Run `php artisan migrate` successfully

### Risk 2: View Namespace Collision
**Risk:** Custom view namespace `bast::` conflicts with other packages
**Mitigation:** Use unique namespace that unlikely to collide
**Check:** Test view loading manually

### Risk 3: Snipe-IT Core Update
**Risk:** Sidebar structure changes in upstream, breaking menu injection
**Mitigation:** Keep menu injection simple (1 line), use View Composer
**Check:** Monitor Snipe-IT changelog, test rebase scenarios

### Risk 4: Authorization Bypass
**Risk:** User can view other user's BAST via direct URL
**Mitigation:** Check authorization in every controller method
**Check:** Manual testing with different users

### Risk 5: Query N+1 Problem
**Risk:** Listing 100 BAST triggers 100+ queries
**Mitigation:** Use `with()` to eager load relationships
**Check:** Monitor DB queries in development

### Risk 6: Permission Model Complexity
**Risk:** Snipe-IT uses specific permission system, custom BAST permissions might not integrate
**Mitigation:** Align with existing Snipe-IT permission model or use simple role check
**Check:** Test with different user roles

---

## 📋 DEVELOPMENT CHECKLIST

### Pre-Development
- [ ] Read this entire roadmap
- [ ] Understand the two-layer data approach
- [ ] Review Snipe-IT directory structure
- [ ] Understand Laravel Service Provider pattern
- [ ] Setup git branch: `git checkout -b custom/bast-feature`

### Phase 1: Setup
- [ ] Create `/package/feature/bast` directory
- [ ] Create subdirectories (src, resources, database/migrations)
- [ ] Create `ServiceProvider.php`
- [ ] Test: ServiceProvider registers in `config/app.php`

### Phase 2: Database
- [ ] Write migration for `bast_snapshots`
- [ ] Write migration for `bast_user_snapshots`
- [ ] Run migrations
- [ ] Verify tables exist in DB

### Phase 3: Models
- [ ] Create `BastSnapshot` model
- [ ] Add relationships (belongsTo User, Asset)
- [ ] Create `BastUserSnapshot` model
- [ ] Test model queries in tinker

### Phase 4: Controllers
- [ ] Create `BastController` with all methods
- [ ] Create `BastUserController`
- [ ] Add authorization checks
- [ ] Test endpoints manually

### Phase 5: Routes
- [ ] Create `routes.php`
- [ ] Register all BAST routes
- [ ] Test: Route::list shows all BAST routes

### Phase 6: Views
- [ ] Create layout template
- [ ] Create all views (7 views total)
- [ ] Add Bootstrap/Tailwind classes
- [ ] Test: All pages render without errors

### Phase 7: Integration
- [ ] Create `config/custom-menu.php`
- [ ] Create menu component view
- [ ] Add View Composer to ServiceProvider
- [ ] Edit `sidebar.blade.php` (+1 line)
- [ ] Test: Menu appears in sidebar

### Phase 8: Testing
- [ ] Test user flow: navigate → list → view → print
- [ ] Test authorization (try access other user's data)
- [ ] Test print functionality
- [ ] Mobile responsive test
- [ ] Check browser console (no JS errors)

### Phase 9: Cleanup & Documentation
- [ ] Remove debug code (dd, var_dump)
- [ ] Add proper comments
- [ ] Update roadmap (mark done)
- [ ] Write developer documentation

### Phase 10: Git Commit
- [ ] Stage all files
- [ ] Commit with clear message
- [ ] Push to branch
- [ ] Create git log summary

---

## 🚀 GO-LIVE CHECKLIST

Before deploying to production:

- [ ] All tests passed locally
- [ ] Database migrations tested on staging
- [ ] Staging environment mirrors production
- [ ] Staging tested with real data volume
- [ ] Backup created before migration
- [ ] Rollback plan documented
- [ ] Stakeholders notified
- [ ] Help desk briefed
- [ ] Documentation shared
- [ ] Monitoring setup (logs, errors)

---

## 📚 ADDITIONAL RESOURCES

### Reference Documents
- [Laravel Service Providers](https://laravel.com/docs/11.x/providers)
- [Laravel Models & Relationships](https://laravel.com/docs/11.x/eloquent)
- [Blade Templating](https://laravel.com/docs/11.x/blade)
- [Laravel Migrations](https://laravel.com/docs/11.x/migrations)

### Snipe-IT Specific
- Check: `app/Models/Asset.php` for asset relationships
- Check: `app/Models/User.php` for user relationships
- Check: `resources/views/layouts/app.blade.php` for app layout pattern
- Check: `resources/views/layouts/partials/sidebar.blade.php` for menu structure

### Testing
- Use Laravel Tinker untuk test queries
- Use browser DevTools untuk test print view
- Use Postman untuk test API (jika ada)

---

## 📝 NOTES FOR AI ASSISTANTS

### When Building This Package:

1. **Follow the Phase Approach** - Don't skip phases. Each phase builds on previous.

2. **Keep Package Isolated** - All code goes in `/package/feature/bast/`. Don't scatter files.

3. **Minimize Core Edits** - Only edit:
   - `config/app.php` (register ServiceProvider)
   - `resources/views/layouts/partials/sidebar.blade.php` (+1 line)

4. **Use Snapshot Pattern** - Don't query real-time data in views. Use snapshots.

5. **Test Authorization** - Every controller method needs `authorize()` check.

6. **Query Optimization** - Always use `with()` for eager loading.

7. **View Naming** - Use `bast::` namespace consistently. Never hardcode view paths.

8. **Error Handling** - Add try-catch for database operations.

9. **Documentation** - Comment why, not what. Code should be self-explanatory.

10. **Git Strategy** - Commit often, write clear messages, rebase cleanly.

---

## 📞 QUESTIONS TO ANSWER BEFORE DEVELOPMENT

1. **Authorization Model**: Apakah regular user boleh edit BAST mereka? Atau read-only?
2. **Create Flow**: BAST dibuat dari mana? Admin form? Atau auto-generate dari assignment?
3. **Snapshot Timing**: Snapshot dibuat kapan? Saat BAST created? Atau setiap assignment?
4. **Approval Workflow**: Ada approval step? Atau langsung finalize?
5. **Print Format**: Sudah ada design template? Atau generic HTML?
6. **Multi-language**: BAST content perlu multi-language? Atau cukup Bahasa Indonesia?
7. **Company Multi-tenancy**: Snipe-IT sudah multi-company? BAST perlu filter by company?
8. **Future Export**: Akan ada export BAST ke Excel? Perlu design untuk itu?
9. **Digital Signature**: Akan ada fitur digital signature? Atau manual signature saja?
10. **Audit Log**: Perlu track siapa yang print, kapan?

---

**End of Roadmap Document**

Version 1.0 | Created for BAST Feature Implementation