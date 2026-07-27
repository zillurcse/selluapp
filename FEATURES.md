# Platform Features — Complete List

A multi-tenant **SaaS E‑Commerce Platform** built on a Laravel 12 REST API with
two Nuxt 4 (Vue 3) front-ends — an Admin/Vendor dashboard and a public
Storefront. Features are grouped into five areas: **SaaS Platform**,
**Super Admin**, **Vendor**, **Landing Pages**, and **Customer**.

---

## 1. SaaS Platform (Multi-Tenancy & Core)

The backbone that turns the store software into a rentable, multi-vendor product.

- **Multi-tenant architecture** — every record is scoped to its vendor/tenant for full data isolation
- **Package / plan system** — subscription tiers that gate access and usage
- **Subscriptions** — vendor subscriptions tied to packages, with lifecycle management
- **Payments & transactions** — platform-level billing records and transaction ledger
- **Vendor onboarding** — provision new vendor stores and assign packages
- **"Login as vendor"** — super admin can impersonate any vendor for support
- **Per-vendor shop domain** — each vendor gets its own storefront address
- **Global platform settings** — branding, mail, appearance, payments controlled centrally
- **Dynamic payment methods** — configurable, platform-managed payment gateways
- **Authentication suite** — email/password, **magic-link** login, **PIN** login, and **Google** social login
- **Role-Based Access Control (RBAC)** — roles and granular permissions (Spatie)
- **API-first design** — decoupled REST API (Laravel Sanctum tokens) serving both front-ends

---

## 2. Super Admin

The platform operator's control center.

- **Dashboard** — platform-wide statistics and KPIs
- **Plans / Packages management** — create, edit, and price subscription plans
- **Vendor management** — list, create, edit, delete vendors; assign packages; login-as-vendor
- **Finance — Payments** — record, update, delete, and view payment stats
- **Finance — Subscriptions** — manage vendor subscriptions with stats
- **Finance — Transactions** — full transaction history and stats
- **Users management** — platform users, roles, and access
- **Roles & permissions** — define system-wide roles and capabilities
- **Global settings**
  - General settings
  - Mail / SMTP configuration
  - Appearance / branding
  - Advanced settings
  - Payment settings
  - File / asset uploads
- **Dynamic payment methods** — enable and configure gateways offered to vendors

---

## 3. Vendor

The full store-management suite each vendor gets. This is the largest surface.

### Catalog & Products
- **Products** — full CRUD with slugs, status, and rich content
- **Product variants** — multiple variants per product
- **Product attributes & attribute values** — configurable attribute system
- **Categories** — nested categories with drag-and-drop **sorting**
- **Brands** — brand management with sorting
- **Units** — measurement/selling units
- **Suppliers** — supplier directory (CRUD)

### Inventory & Warehouse
- **Stock logs** — full stock movement history
- **Restock** — add inventory
- **Stock adjustment** — manual corrections
- **Barcodes** — scan, generate, print labels, mark-as-printed
- **Warehouse audit** — physical-count auditing workflow

### Point of Sale (POS)
- **POS terminal** — in-store sales screen
- POS product/category/brand lookup & customer selection
- Place POS orders, view POS sales and stats

### Orders & Fulfilment
- **Orders** — full order management (CRUD)
- **Invoices** — printable per-order invoice
- **Courier integrations** — Steadfast, Pathao, RedX
  - Send orders to Pathao / Steadfast
  - Sync individual and bulk courier statuses
  - Steadfast webhook + balance & status checks
  - Pathao city/zone/area lookups and bulk location sync

### Shipping & Delivery Zones
- **Countries, states, cities** management (CRUD)
- **Shipping methods** configuration
- **Delivery zones** and shop delivery setup
- Pathao location bulk sync

### Marketing & Customer Engagement
- **Coupons** — create, manage, and send to newsletter subscribers
- **Promotions** — campaigns, send to subscribers
- **Loyalty program** — configurable points, rules, and loyalty logs
- **Newsletter** — subscriber list and management
- **Customers** — vendor customer directory (CRUD)
- **Reviews** — moderate product reviews
- **Banners & sliders** — storefront promotional imagery

### Fraud & Risk
- **Fraud check** — courier-based fraud/success-rate lookups with stats
- Fraud-check settings and per-order actions
- **Block fake orders** / spider intelligence tooling

### Storefront / Shop Configuration ("Managed Shop" & "Managed Website")
- **Shop settings** and **business settings**
- **Shop domain** setup
- **SEO & social** metadata
- **Google / Facebook / TikTok** pixels & feeds; Facebook Conversion API relay
- **Chat plugins** integration
- **Payment gateway** configuration
- **SMTP settings**
- **SMS templates** (with test send)
- **Email templates** (with test send)
- **Custom pages**
- **Third-party APIs**
- Website appearance settings

### Landing Pages
- Build **single-product** and **multiple-product** landing pages
- Landing page listing, creation, and settings

### Human Resource Management (HRM)
- **HRM dashboard** — stats and recent activities
- **Departments** and **designations** (CRUD)
- **Employees** management
- **Attendance** — single, bulk, and summary
- **Leaves** — requests, approve/reject actions
- **Payroll** — generate, edit, mark paid, summaries

### Reports & Analytics
- Overview dashboard
- **Sales** & **sales analytics**
- **Returns** and **cancellations**
- **Expenses** (view & record)
- **Coupons** performance
- **Product performance**
- **Stock** report
- **Customers** report
- **Earnings**
- **Tax** report

### Staff & Access
- **Staff management** (CRUD)
- **Roles & permissions** per vendor

### System & Utilities
- **Notifications** — list, unread count, mark read / read-all
- **Media library** — uploads management
- **Profile & settings**
- **Package upgrade** — view packages, purchase, and history
- **AI automation** tools
- **Help center** and "How to set up your e‑commerce" guides

---

## 4. Landing Pages

A dedicated high-conversion landing-page system, separate from the main store.

- **Single-product landing pages** — focused, one-product sales pages
- **Multiple-product landing pages** — bundle/collection style pages
- **Theme variations** — multiple single-product templates (v1, v2, v3) and a multiple-product theme
- **Slug-based public routes** — `/[slug]` and `/l/[slug]` for published pages
- **Landing-page builder & settings** — configure content, layout, and behavior from the vendor dashboard
- **Custom pages** — additional standalone content pages
- Direct-to-checkout flow optimized for ad traffic (Facebook/Google/TikTok)

---

## 5. Customer (Storefront)

The public shopping experience for end buyers.

### Accounts & Authentication
- **Register** and **login**
- **Magic-link** login and **login verification**
- **Google** social login
- **PIN** set/verify for fast checkout
- **OTP verification** at checkout (with resend)

### Browsing & Discovery
- **Home / storefront** landing
- **Shop** listing with infinite categories
- **Product detail** pages with slugs
- **Product reviews** display and submission (verified purchase check)
- **Search & category** browsing
- **Vendor storefronts** by slug

### Cart & Checkout
- **Cart** — add, update, remove items
- **Checkout** — place order
- **Discount / coupon** calculation
- **Payment method** selection
- **Shipping estimation**
- **OTP-verified** order placement
- **Thank-you** confirmation page

### Customer Account Panel
- **Dashboard** — account overview
- **Profile** — view & update, change password
- **Addresses** — full CRUD
- **Orders** — history and order detail
- **Order tracking** — courier tracking by order
- **Wishlist** — save products
- **Settings** — preferences
- **Delete account** — self-service removal
- **Submit reviews** for purchased products

### Engagement & Content
- **Newsletter subscription**
- **Contact / customer messages**
- **Returns policy** and **shipping policy** pages
- **Sitemap.xml**, **robots.txt**, and **Google/Facebook product feeds** for SEO & ads

---

## Technology Summary

| Layer | Stack |
|-------|-------|
| Backend | Laravel 12, PHP 8.2+, REST API |
| Auth | Laravel Sanctum, magic-link, PIN, Google OAuth |
| Permissions | Spatie Laravel Permission (RBAC) |
| Admin / Vendor UI | Nuxt 4, Vue 3, Pinia, Tailwind CSS |
| Storefront UI | Nuxt 4, Vue 3, Pinia, Tailwind CSS |
| Media | Intervention Image, Flysystem (S3-compatible) |
| Couriers | Steadfast, Pathao, RedX |
| Marketing | Google / Facebook / TikTok pixels & feeds, Facebook Conversion API |
| Database | MySQL / MariaDB |
