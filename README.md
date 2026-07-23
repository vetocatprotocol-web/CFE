<p align="center">
    <h1 align="center">🐾 Haland PetCare Full</h1>
    <p align="center">Sistem Operasional Terintegrasi untuk Klinik Hewan</p>
</p>

<p align="center">
    <a href="#fitur">Fitur</a> •
    <a href="#tech-stack">Tech Stack</a> •
    <a href="#instalasi">Instalasi</a> •
    <a href="#konfigurasi">Konfigurasi</a> •
    <a href="#database">Database</a> •
    <a href="#struktur-proyek">Struktur Proyek</a> •
    <a href="#api-endpoints">API</a> •
    <a href="#deployment">Deployment</a>
</p>

---

## 📋 Tentang

**Haland PetCare Full** adalah sistem operasional terintegrasi untuk klinik hewan yang dirancang dengan filosofi kesederhanaan, efisiensi, dirancang untuk kemudahan penggunaan.

### Objektif Utama
- ✅ Satu aplikasi, satu database, satu repository
- ✅ Zero complex configuration setup
- ✅ Separasi role & permission yang jelas
- ✅ Automasi billing & pricing
- ✅ Portal customer yang intuitif
- ✅ Minimal dependencies, maksimal stability

### Target Users
| Role | Akses | Deskripsi |
|------|-------|-----------|
| **Owner** | Full Access | Dashboard kontrol penuh bisnis, manage master data, user, laporan |
| **Dokter** | Medical | Input medis, tindakan, resep obat, lihat invoice |
| **Kasir** | POS & Payment | POS system, proses pembayaran, lihat billing |
| **Admin** | Limited | User management assist, stock opname, report assist |
| **Customer** | Portal | Self-service tracking via customer portal |

---

## ✨ Fitur

### Modul Operasional
| Modul | Deskripsi | Status |
|-------|-----------|--------|
| Customer Management | Registrasi, profil, data pet | ✅ |
| Medical Records | Simple visit notes, diagnosis, treatment | ✅ |
| Service Management | Tindakan, layanan dengan pricing fixed | ✅ |
| Drug Management | Obat dengan pricing fixed | ✅ |
| Product Management | Retail products dengan kategori & stock | ✅ |
| Visit Processing | Create visit, auto-generate invoice items | ✅ |
| Billing Module | Untuk perawatan bertahap (rawat inap, pet hotel) | ✅ |
| POS System | Retail sales tanpa harus registrasi customer | ✅ |
| Payment Processing | Multiple payment methods, invoice settlement | ✅ |
| Customer Portal | History, medical records, invoices, prescription view | ✅ |
| Owner Dashboard | Config master data, users, reports, analytics | ✅ |
| Stock Management | Product stock tracking & adjustment | ✅ |
| Reporting | Basic reports: daily sales, visits, inventory | ✅ |

### Backend Features
- ✅ Role-based access control (Owner, Dokter, Kasir, Admin)
- ✅ Audit trail untuk transaksi penting
- ✅ Auto-numbering untuk invoice & receipt
- ✅ Tax calculation (flat or percentage)
- ✅ Discount management (manual per-transaction)
- ✅ Invoice generation & PDF export
- ✅ Email notifications (visit confirmation, invoice, prescription)
- ✅ Basic API untuk future mobile app

### Frontend Features
- ✅ Responsive design (mobile-friendly untuk customer portal)
- ✅ Real-time data validation
- ✅ Dashboard analytics dengan charts
- ✅ Search & filter functionality
- ✅ Print-friendly invoice & receipt
- ✅ Dark mode toggle (nice-to-have)

---

## 🛠 Tech Stack

```
Frontend:
├── Laravel Blade (template engine)
├── Alpine.js (lightweight interactivity)
├── Tailwind CSS v4 (styling)
├── Chart.js (dashboard charts)
└── Vite (asset bundling)

Backend:
├── Laravel 13 (framework)
├── PHP 8.4+ (runtime)
├── PostgreSQL / SQLite (database)
├── Laravel Queue (database-based queue)
└── barryvdh/laravel-dompdf (PDF generation)

Deployment:
├── Docker Compose (optional)
├── Traditional VPS/Shared Hosting
└── Manual deployment via Git
```

---

## 📦 Instalasi

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js 18+ & NPM
- PostgreSQL / SQLite

### Quick Start

```bash
# 1. Clone repository
git clone https://github.com/your-repo/haland-petcare.git
cd haland-petcare

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Create database (SQLite)
touch database/database.sqlite

# 7. Run migrations & seeders
php artisan migrate --seed

# 8. Build frontend assets
npm run build

# 9. Start development server
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

### Default Login

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@halandpetcare.com | password |
| Dokter | dokter@halandpetcare.com | password |
| Kasir | kasir@halandpetcare.com | password |
| Admin | admin@halandpetcare.com | password |

---

## ⚙️ Konfigurasi

### Environment Variables

```env
# App
APP_NAME="Haland PetCare"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite for development)
DB_CONNECTION=sqlite

# Database (PostgreSQL for production)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=haland_petcare
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=720

# Cache
CACHE_STORE=database

# Queue
QUEUE_CONNECTION=database

# Mail (for email notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@halandpetcare.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Company Configuration

Setelah login sebagai Owner, akses **Settings** untuk mengatur:
- Nama klinik & logo
- Alamat & kontak
- Tax setting (flat/percentage)
- Payment methods
- Invoice/Receipt numbering format

---

## 🗄 Database

### Schema Overview

```
┌─────────────────────────────────────────────────────────┐
│                    CORE TABLES                           │
├─────────────────────────────────────────────────────────┤
│ users, roles, permissions, role_permissions             │
│ customers, pets                                         │
├─────────────────────────────────────────────────────────┤
│                 MASTER DATA                              │
├─────────────────────────────────────────────────────────┤
│ services, drugs, product_categories, products           │
├─────────────────────────────────────────────────────────┤
│                 TRANSACTIONS                             │
├─────────────────────────────────────────────────────────┤
│ visits, visit_items                                     │
│ billings, billing_items                                 │
│ invoices, invoice_items                                 │
│ pos_orders, pos_order_items                             │
│ payments                                                │
├─────────────────────────────────────────────────────────┤
│                  SYSTEM                                  │
├─────────────────────────────────────────────────────────┤
│ audit_logs, settings, notifications                     │
│ prescriptions, prescription_items                       │
│ stock_adjustments                                       │
└─────────────────────────────────────────────────────────┘
```

### Tables (30 migrations)

| Table | Deskripsi |
|-------|-----------|
| `users` | User accounts dengan role FK |
| `roles` | Roles: OWNER, DOKTER, KASIR, ADMIN |
| `permissions` | Permissions per role |
| `customers` | Data pelanggan |
| `pets` | Data hewan peliharaan |
| `services` | Tindakan medis (Konsultasi, Vaksin, dll) |
| `drugs` | Obat-obatan |
| `product_categories` | Kategori produk retail |
| `products` | Produk retail dengan stok |
| `stock_adjustments` | History perubahan stok |
| `visits` | Kunjungan klinik |
| `visit_items` | Tindakan & obat dalam kunjungan |
| `billings` | Perawatan bertahap |
| `billing_items` | Item dalam billing |
| `invoices` | Tagihan (otomatis dari visit/billing) |
| `invoice_items` | Detail item invoice |
| `pos_orders` | Transaksi retail |
| `pos_order_items` | Item dalam transaksi retail |
| `payments` | Records pembayaran |
| `audit_logs` | Audit trail perubahan |
| `settings` | Konfigurasi aplikasi |
| `notifications` | Notifikasi in-app |
| `prescriptions` | Resep obat |
| `prescription_items` | Detail resep |

---

## 📁 Struktur Proyek

```
haland-petcare/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Owner/Admin controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ServiceController.php
│   │   │   │   ├── DrugController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── ProductCategoryController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── StockController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── Dokter/          # Doctor controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── VisitController.php
│   │   │   │   └── BillingController.php
│   │   │   ├── Kasir/           # Cashier controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── POSController.php
│   │   │   │   └── PaymentController.php
│   │   │   ├── Portal/          # Customer portal
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── PortalController.php
│   │   │   ├── Auth/            # Authentication
│   │   │   │   └── AuthController.php
│   │   │   └── InvoiceController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   ├── AuditLog.php
│   │   │   └── CheckCustomer.php
│   │   └── Requests/            # Form validation
│   │       ├── LoginRequest.php
│   │       ├── StoreServiceRequest.php
│   │       ├── StoreDrugRequest.php
│   │       ├── StoreProductRequest.php
│   │       ├── StoreCustomerRequest.php
│   │       ├── StoreVisitRequest.php
│   │       ├── StorePosOrderRequest.php
│   │       └── ProcessPaymentRequest.php
│   ├── Models/                  # Eloquent models (24)
│   ├── Services/                # Business logic
│   │   ├── InvoiceService.php
│   │   ├── PaymentService.php
│   │   ├── NumberingService.php
│   │   ├── ReportService.php
│   │   └── StockService.php
│   └── Mail/                    # Email notifications
│       ├── VisitCompletedMail.php
│       ├── InvoiceGeneratedMail.php
│       └── PaymentConfirmedMail.php
├── database/
│   ├── migrations/              # 30 migration files
│   └── seeders/                 # 10 seeder files
├── resources/
│   └── views/                   # 67 Blade templates
│       ├── layouts/             # Admin, Portal, Guest layouts
│       ├── admin/               # Owner/Admin pages
│       ├── dokter/              # Doctor pages
│       ├── kasir/               # Cashier pages
│       ├── portal/              # Customer portal
│       ├── shared/              # Shared pages (invoices)
│       ├── components/          # Reusable components
│       ├── emails/              # Email templates
│       ├── pdf/                 # PDF templates
│       └── errors/              # Error pages
├── routes/
│   └── web.php                  # 103 routes
├── public/
│   └── build/                   # Compiled assets
├── PRD.md                       # Product Requirements Document
└── README.md                    # This file
```

---

## 🔐 Authentication & Authorization

### Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Owner** | Full access: manage master data, users, settings, reports, billing, stock |
| **Dokter** | Create/edit visits, view customers, view master data |
| **Kasir** | Process payments, POS orders, view invoices |
| **Admin** | User management assist, stock management, view reports |

### Security Features
- ✅ Bcrypt password hashing
- ✅ CSRF protection
- ✅ Session-based authentication
- ✅ Role-based access control (RBAC)
- ✅ Audit trail untuk perubahan data
- ✅ Input validation (server-side)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS prevention (Blade auto-escape)

---

## 📡 API Endpoints

### Authentication
```
POST   /login              # Login
POST   /logout             # Logout
```

### Admin (Owner/Admin only)
```
GET    /admin/dashboard                    # Dashboard
GET    /admin/services                     # List services
POST   /admin/services                     # Create service
PUT    /admin/services/{id}                # Update service
DELETE /admin/services/{id}                # Archive service
# Similar for: drugs, products, customers, users
GET    /admin/stock                        # Stock management
POST   /admin/stock/adjust                 # Adjust stock
GET    /admin/settings                     # Settings
GET    /admin/reports                      # Reports overview
```

### Dokter
```
GET    /dokter/dashboard                   # Doctor dashboard
GET    /dokter/visits                      # List visits
POST   /dokter/visits                      # Create visit
GET    /dokter/visits/{id}                 # View visit
PUT    /dokter/visits/{id}                 # Update visit
POST   /dokter/visits/{id}/complete        # Complete visit
POST   /dokter/visits/{id}/items           # Add item
DELETE /dokter/visits/{id}/items/{item}    # Remove item
GET    /dokter/billings                    # List billings
POST   /dokter/billings                    # Create billing
POST   /dokter/billings/{id}/items         # Add billing item
POST   /dokter/billings/{id}/complete      # Complete billing
```

### Kasir
```
GET    /kasir/dashboard                    # Cashier dashboard
GET    /kasir/pos                          # POS interface
POST   /kasir/pos/order                    # Create POS order
POST   /kasir/pos/order/{id}/items         # Add item
DELETE /kasir/pos/order/{id}/items/{item}  # Remove item
POST   /kasir/pos/order/{id}/checkout      # Process checkout
GET    /kasir/payments                     # List payments
POST   /kasir/payments/process             # Process payment
```

### Customer Portal
```
GET    /portal/dashboard                   # Portal dashboard
GET    /portal/pets                        # My pets
GET    /portal/pets/{id}                   # Pet detail
GET    /portal/visits                      # Visit history
GET    /portal/invoices                    # My invoices
GET    /portal/prescriptions               # My prescriptions
GET    /portal/profile                     # My profile
PUT    /portal/profile                     # Update profile
PUT    /portal/password                    # Change password
```

### Shared
```
GET    /invoices                           # List invoices
GET    /invoices/{id}                      # View invoice
GET    /invoices/{id}/download             # Download PDF
POST   /invoices/{id}/email                # Email invoice
```

---

## 🎨 UI/UX Features

### Design System
- **93 custom CSS classes** untuk konsistensi desain
- **Color tokens**: Primary (blue), Secondary (emerald), Accent (violet)
- **Component library**: Buttons, Forms, Cards, Tables, Badges, Modals, Alerts
- **Animations**: fadeIn, slideInRight, slideInUp

### Layouts
| Layout | Target | Features |
|--------|--------|----------|
| Admin | Owner, Dokter, Kasir, Admin | White navbar + dark sidebar, responsive |
| Portal | Customer | Mobile-first, bottom navigation |
| Guest | Login | Split layout, branding + form |

### Key Pages
- **Admin Dashboard**: Real-time stats, animated charts, pending actions
- **POS Interface**: Touch-friendly product grid, cart management, payment processing
- **Doctor Visit**: Step-by-step dynamic form, service/drug selection
- **Customer Portal**: Mobile-first cards, bottom navigation, self-service

### Responsive Breakpoints
- Mobile: < 640px (sm)
- Tablet: 640px - 1024px (md, lg)
- Desktop: > 1024px (xl, 2xl)

---

## 📧 Email Notifications

| Event | Recipient | Content |
|-------|-----------|---------|
| Visit completed | Customer | Visit selesai, invoice created |
| Invoice generated | Customer | Invoice siap, link download |
| Payment received | Customer | Payment confirmed, receipt attached |
| Low stock | Owner | Product stock < reorder_point |

### Email Templates
- `visit-completed.blade.php` - Visit completion notification
- `invoice-generated.blade.php` - Invoice notification
- `payment-confirmed.blade.php` - Payment confirmation

---

## 📄 PDF Generation

### Invoice PDF
- Professional format dengan klinik header
- Item detail dengan harga satuan & subtotal
- Tax calculation transparent
- Payment status & remaining amount

### Receipt PDF
- Compact receipt format
- Items, totals, payment details
- Print-ready layout

### Prescription PDF
- Drug list dengan dosis & durasi
- Doctor signature area
- Patient/pet info

---

## 🚀 Deployment

### Traditional VPS (Recommended)

```bash
# Server requirements
OS: Ubuntu 20.04 LTS+
Server: Nginx
PHP: 8.2+
Database: PostgreSQL 14+

# Deploy
git clone https://repo.git haland-petcare
cd haland-petcare
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed
npm install && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Docker Compose (Development)

```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    depends_on:
      - db
  db:
    image: postgres:14
    environment:
      POSTGRES_DB: haland_petcare
      POSTGRES_USER: user
      POSTGRES_PASSWORD: password
    volumes:
      - db_data:/var/lib/postgresql/data
volumes:
  db_data:
```

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

### Deployment Checklist

- [ ] Clone repo & install dependencies
- [ ] Configure .env (database, mail, etc)
- [ ] Run migrations & seeders
- [ ] Build frontend assets
- [ ] Cache config, routes, views
- [ ] Set file permissions
- [ ] Configure web server (Nginx/Apache)
- [ ] Setup SSL certificate
- [ ] Configure backup cronjob
- [ ] Test all functionality

---

## 🧪 Testing

### Manual Testing Checklist

**Authentication:**
- [ ] Login dengan semua role
- [ ] Login dengan password salah → error message
- [ ] Logout → redirect ke login

**Customer Management:**
- [ ] Create customer baru
- [ ] Add pet ke customer
- [ ] Edit customer profile
- [ ] Soft delete customer

**Visit Workflow:**
- [ ] Create visit baru (DRAFT)
- [ ] Add tindakan & obat
- [ ] Complete visit → invoice generated
- [ ] Invoice status: UNPAID → PAID

**POS System:**
- [ ] Start new transaction
- [ ] Add multiple items
- [ ] Process payment
- [ ] Stock berkurang

**Master Data (Owner Only):**
- [ ] CRUD services, drugs, products
- [ ] Manage categories
- [ ] Stock adjustment
- [ ] Dokter tidak bisa edit harga

---

## 📊 Statistics

| Component | Count |
|-----------|-------|
| Migrations | 30 |
| Models | 24 |
| Controllers | 21 |
| Services | 5 |
| Form Requests | 8 |
| Middleware | 3 |
| Mail Classes | 3 |
| Seeders | 10 |
| Views | 67 |
| Components | 3 |
| Routes | 103 |
| PDF Templates | 3 |
| Email Templates | 3 |

---

## 📝 Changelog

### v1.0.0 (2026-07-23)
- ✅ Initial release
- ✅ Full CRUD for master data
- ✅ Visit workflow with auto-invoice
- ✅ Billing module for staged treatment
- ✅ POS system with stock management
- ✅ Payment processing
- ✅ Customer portal
- ✅ PDF generation (invoice, receipt, prescription)
- ✅ Email notifications
- ✅ Modern UI/UX with responsive design

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 🤝 Contributing

Untuk kontribusi, silakan buat issue atau pull request di repository GitHub.

---

## 📞 Support

Untuk bantuan dan pertanyaan:
- Email: support@halandpetcare.com
- Documentation: Lihat PRD.md untuk detail lengkap

---

<p align="center">
    Made with ❤️ for Haland PetCare
</p>
