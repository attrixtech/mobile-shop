# Mobile Shop and Repair Center Software

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

A complete, production-grade, multi-tenant **Mobile Shop Management & Repair Center ERP** engineered for smartphone retail stores, multi-branch electronics chains, and device service labs.

Built with a **high-density, compact POS layout**, instant barcode/IMEI scanning, automated device trade-in valuations, certified repair job ticketing, Indian GST compliance (HSN codes, GSTR-1), double-entry accounting, and a modern customer-facing storefront with WhatsApp ordering.

---

## 🌟 Key Highlights

- **Multi-Tenant SaaS Core:** Multi-tenant architecture with tenant scoping across catalog, inventory, customer, financial, repair, and user records.
- **Compact UI Design:** High-density, information-rich user interface optimized for fast keyboard navigation, barcode scanner operations, and compact display resolutions.
- **End-to-End Serialized IMEI Traceability:** Dual IMEI tracking (`imei1`, `imei2`, `serial_number`), unique constraint validation, lifecycle states (`in_stock`, `sold`, `under_repair`, `exchanged`), and immutable movement audit logs.
- **Indian GST Compliance:** Automatic intra-state (CGST + SGST) vs inter-state (IGST) determination based on seller state vs customer state code, HSN codes (`85171300`, `8518`, `9987`), and GSTR-1 Table 12 return reports.
- **High-Speed POS & Billing:** Instant barcode scanning, variant selector, IMEI availability picker, mobile exchange trade-in allowance deduction, split tender (Cash, UPI, Card), 80mm thermal receipt, and GST tax invoice.
- **Mobile Exchange & Buyback:** Diagnostics grading checklist (screen, battery, body, functionality), trade-in voucher generation, and separate used device resale inventory.
- **Repair & Service Center:** Service job cards with pattern/PIN codes, physical scratch inspection, technician allocation, spare parts consumption, status tracking, and printable customer job tickets.
- **Inter-Branch Stock Transfers:** Seamless transfers between store branches with dynamic in-stock filtering, interactive IMEI selection chips, and real-time ledger rebalancing.
- **Double-Entry Accounting:** Full Chart of Accounts (Assets, Liabilities, Equity, Revenue, COGS, Expenses) with balanced debit/credit journal postings for every purchase, sale, exchange, and expense.
- **Daily Cash Register Drawer:** Shift-based opening float, payment aggregation, cash drawer count, and variance reconciliation.
- **Public Storefront & Landing Page:** Customer-facing landing page with brand/category filters, trade-in calculator, repair lab highlights, and direct WhatsApp inquiry integration.
- **Automated Test Suite:** 14 automated feature tests with 47 assertions covering auth, POS checkout, IMEI duplicate prevention, inter-branch transfers, repair lifecycle, and tenant isolation.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 13 &bull; PHP 8.4+
- **Database:** MySQL 8.0+ / MariaDB 10.4+
- **Frontend:** Tailwind CSS v4 &bull; Alpine.js &bull; Chart.js
- **Asset Bundler:** Vite 8.x
- **Testing:** PHPUnit / Laravel Test Suite

---

## 🚀 Installation & Setup

### 1. Requirements
Ensure your environment has:
- PHP >= 8.2 (tested on PHP 8.4) with `pdo_mysql`, `bcmath`, `curl`, `mbstring`, `openssl`, `tokenizer`, `xml`
- Composer 2+
- Node.js 18+ and npm
- MySQL 8.0+ or MariaDB 10.4+

### 2. Clone Repository
```bash
git clone https://github.com/webotapp-academy/mobile-shop-and-repair-center-software.git
cd mobile-shop-and-repair-center-software
```

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Environment Configuration
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mobileshop_erp
DB_USERNAME=root
DB_PASSWORD=
# If using a custom socket (such as XAMPP/MariaDB):
# DB_SOCKET=/Users/pabanbhuyanmacmini/.local/share/webotapp/mysql.sock
```

### 5. Database Migrations & Seeding
Run migrations and populate demo smartphones, accessories, spare parts, repair jobs, and exchange inventory:
```bash
php artisan migrate:fresh --seed
```

### 6. Build Frontend Assets
Compile the Tailwind CSS styles and Alpine.js assets:
```bash
npm run build
```

For live development reloads:
```bash
npm run dev
```

### 7. Run Automated Tests
Verify that all 14 feature test cases pass:
```bash
php artisan test
```

### 8. Start Local Server
```bash
php artisan serve
```
Open your browser at `http://127.0.0.1:8000`.

---

## 🔐 Default Demo Logins

All demo accounts use the default password: `password123`

| Role | Email | Password | Access Scope |
|---|---|---|---|
| **SaaS Super Admin** | `superadmin@webotapp.com` | `password123` | Multi-tenant platform dashboard, tenant plans, usage limits |
| **Store Owner** | `owner@webotapp.com` | `password123` | Full access across all store branches, accounting, P&L, settings |
| **Store Manager** | `manager@webotapp.com` | `password123` | Branch management, purchases, inventory, sales, team members |
| **Cashier / POS** | `sales@webotapp.com` | `password123` | POS billing, returns, customer lookup, cash drawer register |
| **Repair Technician** | `tech@webotapp.com` | `password123` | Repair job cards, diagnostics, spare parts, ticket updates |

---

## 📦 Module Overview & Routes

### 1. Multi-Tenant Administration & RBAC
- **Super Admin Overview:** `/superadmin/dashboard`
- **Tenant Directory:** `/superadmin/tenants`
- **Subscription Plans:** `/superadmin/plans`

### 2. POS & Billing
- **Compact POS Register:** `/pos`
  - Real-time catalog search & barcode scanning
  - RAM / Storage variant selection
  - Available serialized IMEI picker
  - Exchange trade-in voucher deduction
  - Cash, UPI, Card, and Split tender payments
- **Sales Invoices:** `/sales`
- **80mm Thermal Receipt:** `/sales/{id}/thermal` (Auto-printing)
- **A4 Indian GST Tax Invoice:** `/sales/{id}/invoice`
- **Sales Return & Restocking:** `/sales/{id}/return`

### 3. Product Catalog & Serialized IMEI
- **Products Catalog:** `/products`
- **IMEI Serial Registry:** `/imei`
- **IMEI Audit Trail:** `/imei/{id}` (Complete movement & ownership history)
- **Warranty Lookup:** `/warranty`

### 4. Purchasing & Suppliers
- **Suppliers Management:** `/suppliers`
- **Purchase Orders & GRN:** `/purchases`
- **New Inward Stock Purchase:** `/purchases/create` (With bulk IMEI input)

### 5. Inventory & Transfers
- **Branch Stock Balance:** `/inventory`
- **Stock Ledger Movements:** `/inventory/movements`
- **Inter-Branch Stock Transfer:** `/inventory/transfers` (Interactive IMEI selection)
- **Physical Stock Adjustments:** `/inventory/adjustments`

### 6. Mobile Exchange & Buyback
- **Exchange Transactions:** `/exchange`
- **Device Diagnostics Appraisal:** `/exchange/create` (Screen, body, battery, functional grading)
- **Refurbished Used Device Stock:** `/exchange`

### 7. Repair & Service Center
- **Job Cards Index:** `/repairs`
- **Create Job Card:** `/repairs/create` (Interactive customer search, PIN/pattern capture, condition inspection)
- **Job Card Details & Status Update:** `/repairs/{id}` (Parts consumption, item removal, cost recalculation)
- **Printable Service Ticket:** `/repairs/{id}/print`

### 8. Finance & Double-Entry Accounting
- **Financial Overview:** `/accounting`
- **Double-Entry Journal:** `/accounting/journal` (Balanced Debits = Credits)
- **Daily Cash Register Drawer:** `/accounting/cash-register` (Opening float, drawer cash count, variance)
- **Store Expenses:** `/expenses`

### 9. Business Intelligence & Compliance Reports
- **Sales Analytics:** `/reports/sales` (Date range, branch, top-selling items)
- **Stock Valuation:** `/reports/inventory` (Live inventory asset value at cost)
- **Profitability Analysis:** `/reports/profitability` (Gross margin %, COGS, expenses, net profit)
- **GSTR-1 Tax Return:** `/reports/gst` (Table 12 HSN summary, CGST, SGST, IGST)

### 10. Public Storefront & WhatsApp Ordering
- **Public Online Catalog / Landing Page:** `/shop` (also aliased at `/store`)
- **Product Details & Specs:** `/shop/{id}`
- **Direct WhatsApp Chat:** Click-to-chat with pre-filled product inquiries

---

## 📄 License

This software is released under the [MIT License](LICENSE).

Copyright (c) 2026 Webotapp Academy.
