# Software Project Quotation

> **Prepared by:** [Your Company Name]
> **Prepared for:** [Client Name / Company]
> **Quotation No.:** Q-[YYYY]-[####]
> **Date:** [DD Month YYYY]
> **Valid Until:** [DD Month YYYY] (30 days from issue date)

---

## 1. Executive Summary

[Your Company Name] is pleased to present this quotation for the delivery of a
production-ready, multi-tenant **SaaS E‑Commerce Platform**. The solution is an
already-developed, market-proven software product built for the Bangladesh
retail and online-commerce market, with native support for local couriers,
payment workflows, and BDT currency.

This document sets out the scope of the software, the deliverables, and a range
of commercial options so that you can select the engagement model that best fits
your business needs — from a licensed deployment through to full source-code
ownership and exclusive rights.

> **Purpose of this quotation:** to provide a transparent, itemized commercial
> proposal for acquiring the platform, along with clearly defined deliverables,
> support terms, warranty, and ownership rights.

---

## 2. Project Overview

### 2.1 Description

A cloud-ready, **multi-tenant SaaS E‑Commerce platform** that allows a platform
operator to onboard multiple independent vendors, each running their own online
store, point-of-sale, inventory, staff, and order-fulfilment operations under a
subscription-based billing model. The system is delivered as three coordinated
applications:

| Application | Role | Technology |
|-------------|------|------------|
| **Backend API** | Central business logic, data, authentication, integrations | Laravel 12 (PHP 8.2) REST API |
| **Admin Panel** | Vendor & super-admin management dashboard | Nuxt 4 (Vue 3) SPA |
| **Storefront / Marketing Site** | Public-facing store and landing pages | Nuxt 4 (Vue 3) |

### 2.2 Main Modules

- **Multi-Tenant & Subscription (SaaS)** — vendor onboarding, packages, subscriptions, payments, and per-tenant data isolation
- **Product Catalog** — products, variants, attributes, brands, categories, units, suppliers
- **Inventory & Stock** — stock logs, barcode generation, supplier management
- **Orders & Checkout** — cart, coupons, promotions, order lifecycle, invoicing
- **Point of Sale (POS)** — in-store sales, POS sale items, barcode scanning
- **Courier & Fulfilment** — Steadfast, Pathao, and RedX integrations
- **Fraud & Risk** — customer fraud checks before dispatch
- **Customer Engagement** — loyalty points, wishlist, product reviews, newsletters
- **Human Resource Management (HRM)** — employees, departments, designations, attendance, leave, payroll
- **Communication** — email templates, SMS templates, support tickets, notifications
- **Content** — configurable landing pages and store settings
- **Roles & Permissions** — granular role-based access control (RBAC)

### 2.3 Key Features

- Multi-vendor / multi-tenant architecture with data isolation
- Subscription and package-based billing for platform monetization
- Integrated local courier booking and tracking
- Built-in fraud verification for cash-on-delivery markets
- Loyalty, coupon, and promotion engines
- Integrated POS for hybrid online + offline retail
- Fully API-driven, decoupled front-ends
- Responsive, modern admin and storefront interfaces
- Role and permission management for staff and vendors

### 2.4 Technology Stack

| Layer | Technology |
|-------|------------|
| Backend Framework | Laravel 12 (PHP 8.2+) |
| API & Auth | REST API, Laravel Sanctum |
| Permissions | Spatie Laravel Permission (RBAC) |
| Admin Front-End | Nuxt 4, Vue 3, Pinia, Tailwind CSS |
| Storefront Front-End | Nuxt 4, Vue 3, Pinia, Tailwind CSS |
| Media & Storage | Intervention Image, Flysystem (S3-compatible) |
| Integrations | Steadfast, Pathao, RedX couriers; barcode generation |
| Database | MySQL / MariaDB |

---

## 3. Deliverables

| # | Deliverable | Description |
|---|-------------|-------------|
| 1 | **Software** | Fully functional platform (Backend API, Admin Panel, Storefront) as per the selected commercial option |
| 2 | **Deployment** | Installation and configuration on the client's production server / hosting environment |
| 3 | **Documentation** | Installation guide, configuration guide, and user/admin documentation (scope varies by option) |
| 4 | **Source Code** | Complete source code (Options B, C, and D only) |
| 5 | **Training** | Live handover / training sessions (Options C and D; optional add-on for others) |

---

## 4. Commercial Options

The platform is available under four commercial models. Please select the option
that best matches your requirements.

| Feature | **A — License Only** | **B — Source Code** | **C — Source + Premium Support** | **D — Exclusive Ownership** |
|---|:---:|:---:|:---:|:---:|
| Production Deployment | ✅ | ✅ | ✅ | ✅ |
| Installation & Configuration | ✅ | ✅ | ✅ | ✅ |
| Full Source Code | ❌ | ✅ | ✅ | ✅ |
| Full Ownership *(excl. third-party/OSS libraries)* | ❌ | ✅ | ✅ | ✅ |
| Documentation | Basic | Full | Full | Full |
| Bug-Fixing Period | 30 days | 90 days | 90 days | 90 days |
| Technical Support | Email only | Basic | **Priority** | **Priority** |
| Maintenance | ❌ | ❌ | 6 months | 6 months |
| Security Updates | ❌ | ❌ | ✅ | ✅ |
| Performance Optimization | ❌ | ❌ | ✅ | ✅ |
| Minor Feature Enhancements | ❌ | ❌ | ✅ | ✅ |
| Training Sessions | ❌ | ❌ | ✅ | ✅ |
| Exclusive Ownership *(not resold to others)* | ❌ | ❌ | ❌ | ✅ |
| Full IP Transfer | ❌ | ❌ | ❌ | ✅ |
| **Price** | `[Price A]` | `[Price B]` | `[Price C]` | `[Negotiated]` |

### Option A — License Only
A licensed, ready-to-use deployment **without source code**. Includes production
deployment, installation, basic documentation, **30 days** of bug fixing, and
email support. *Lowest price point.*

### Option B — Source Code Purchase
Includes the **full source code** with **full ownership** (excluding reusable
third-party and open-source libraries, which remain under their respective
licenses), deployment, full documentation, **90 days** of bug fixing, and basic
technical support.

### Option C — Source Code + Premium Support
Everything in **Option B**, plus **priority support**, **6 months maintenance**,
security updates, performance optimization, minor feature enhancements, and
**training sessions**.

### Option D — Exclusive Ownership
Everything in **Option C**, plus **exclusive ownership** — the software will
**not be sold or licensed to any other client** — and **full intellectual
property transfer**. *Price negotiated separately.*

> ℹ️ **Custom Dropshipping Modules:** Custom-built dropshipping functionality
> (supplier sourcing, automated order routing, supplier catalog sync, margin
> management, etc.) is available as an **add-on and is quoted separately** from
> the options above. See **Section 9 — Customization Policy**.

---

## 5. Implementation

### 5.1 Installation Process
1. Environment and server-requirement verification
2. Provisioning of database and storage
3. Backend API deployment and environment configuration
4. Admin Panel and Storefront build and deployment
5. Integration of third-party credentials (courier, SMS, email, payment)

### 5.2 Deployment
- Deployment to the client-provided production server / hosting environment
- SSL, domain, and DNS configuration support
- Seeding of initial data and administrator account setup

### 5.3 Go-Live
- Smoke testing of core flows (catalog, checkout, POS, courier booking)
- Final configuration sign-off
- Handover and go-live confirmation

---

## 6. Support & Maintenance

| Item | Details |
|------|---------|
| **Bug Fixes** | Defects in delivered functionality corrected free of charge within the warranty period |
| **Technical Support** | Guidance on configuration, deployment, and usage (channel & priority vary by option) |
| **Response Time** | Standard: within [X] business days · Priority (Options C/D): within [X] business hours |
| **Working Hours** | [Day]–[Day], [HH:MM]–[HH:MM] ([Time Zone]); excluding public holidays |
| **Exclusions** | New features, third-party outages, server/infrastructure issues, and changes outside the delivered scope |

---

## 7. Warranty

The software is warranted against defects in the delivered functionality for the
bug-fixing period applicable to the selected option:

- **Option A:** 30 days from go-live
- **Options B, C, D:** 90 days from go-live

During this period, reproducible bugs in the originally delivered scope will be
corrected at no additional cost. The warranty does not cover issues arising from
unauthorized code modifications, third-party service failures, server/hosting
misconfiguration, or new feature requests.

---

## 8. Source Code Ownership

| Model | What the Client Receives |
|-------|--------------------------|
| **License Only (A)** | A right to use the deployed software. **No source code** is provided, and no ownership or resale rights are granted. |
| **Source Code Purchase (B / C)** | The **complete source code** with full ownership for the client's own use, **excluding** reusable third-party and open-source libraries, which remain governed by their original licenses. The vendor **retains the right** to license or sell the same base product to other clients. |
| **Exclusive Ownership (D)** | Full source code with **exclusive rights** and **complete intellectual property transfer**. The vendor **will not sell, license, or reuse** the product with any other client after transfer. |

---

## 9. Customization Policy

Any functionality beyond the delivered scope — including but not limited to the
**custom dropshipping modules**, new integrations, workflow changes, or bespoke
features — will be treated as a separate work item. For each request:

1. The client submits a written requirement.
2. [Your Company Name] provides a **separate written estimate** (scope, effort, cost, and timeline).
3. Work commences only after written approval and any applicable advance payment.

Customization pricing is **not included** in the option prices in Section 4.

---

## 10. Client Responsibilities

To ensure smooth delivery and go-live, the client is responsible for providing:

- Production **hosting / server** meeting the stated system requirements
- **Domain** name and DNS access
- **SSL** certificate
- **API credentials** for third-party services (couriers, SMS gateway, email service, payment gateway)
- **Timely feedback**, approvals, and access required during deployment
- Any content, branding, or data required for configuration

Delays in providing the above may affect the delivery timeline.

---

## 11. Payment Terms

| Milestone | Percentage | Amount |
|-----------|:----------:|--------|
| Package Price (Total) | 100% | `__________` |
| **Advance** (on agreement) | 40% | `__________` |
| **Second Payment** (on deployment) | 40% | `__________` |
| **Final Payment** (on go-live / handover) | 20% | `__________` |

> Payment schedule may be customized by mutual agreement. All amounts are quoted
> in **[Currency]** and are exclusive of applicable taxes and third-party fees.

---

## 12. Assumptions

- The software is delivered **as-is** based on its current, existing feature set.
- The client's environment meets the documented server and software requirements.
- Third-party services (couriers, SMS, email, payment) are active and correctly credentialed by the client.
- Requirements provided at the time of agreement are complete and stable.
- Work outside the agreed scope is subject to separate quotation (Section 9).

---

## 13. Exclusions

The following are **not included** in the quoted price and are borne by the client:

- Third-party software or plugin **licenses**
- **Hosting / server** fees
- **SMS** gateway charges
- **Email** service charges
- **Payment gateway** fees and transaction charges
- **Government fees**, taxes, and regulatory charges
- Custom feature development (quoted separately per Section 9)

---

## 14. Terms & Conditions

- **Project Acceptance:** The project is deemed accepted upon successful go-live and completion of the agreed deliverables for the selected option.
- **Confidentiality:** Both parties agree to keep commercial, technical, and business information exchanged under this engagement strictly confidential.
- **Intellectual Property:** IP rights are governed by the selected commercial option (Section 8). Until full payment is received, all rights remain with [Your Company Name].
- **Force Majeure:** Neither party is liable for delays or failures caused by events beyond reasonable control (natural disasters, outages, government action, etc.).
- **Limitation of Liability:** Total liability under this engagement shall not exceed the total amount paid by the client for the selected option. [Your Company Name] is not liable for indirect, incidental, or consequential damages.
- **Cancellation Policy:** Either party may cancel with written notice. Work completed and costs incurred up to the cancellation date remain payable.
- **Refund Policy:** The advance payment is non-refundable once work has commenced. Subsequent payments correspond to delivered milestones and are non-refundable once the milestone is delivered.
- **Validity of Quotation:** This quotation is valid for **30 days** from the date of issue, after which prices and terms are subject to revision.

---

## 15. Acceptance

By signing below, the client accepts this quotation and its terms and conditions.

| | Client | [Your Company Name] |
|---|--------|---------------------|
| **Name** | ____________________ | ____________________ |
| **Signature** | ____________________ | ____________________ |
| **Date** | ____________________ | ____________________ |
| **Company Stamp** | | |

---

*Thank you for considering [Your Company Name]. We look forward to partnering with you.*
*For questions regarding this quotation, please contact: [Email] · [Phone] · [Website]*
