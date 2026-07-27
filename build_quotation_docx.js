const fs = require('fs');
const {
  Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType,
  Table, TableRow, TableCell, WidthType, BorderStyle, ShadingType,
  PageBreak, TableOfContents, PositionalTab, PositionalTabAlignment,
  PositionalTabLeader, LevelFormat, Footer, PageNumber
} = require('docx');

// ---------- palette ----------
const BRAND = '1F4E79';   // deep blue
const BRAND2 = '2E75B6';  // lighter blue
const HEADER_FILL = '1F4E79';
const ZEBRA = 'EAF1F8';
const LIGHT = 'F2F6FA';
const GREY = '595959';
const CHECK = '2E7D32';
const CROSS = 'C62828';

// ---------- helpers ----------
const FULL = 9360; // usable width DXA (Letter, 1" margins)

function h1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    spacing: { before: 320, after: 140 },
    children: [new TextRun({ text, bold: true, color: BRAND, size: 30 })],
  });
}
function h2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    spacing: { before: 220, after: 100 },
    children: [new TextRun({ text, bold: true, color: BRAND2, size: 24 })],
  });
}
function h3(text) {
  return new Paragraph({
    spacing: { before: 160, after: 60 },
    children: [new TextRun({ text, bold: true, color: '333333', size: 22 })],
  });
}
function p(text, opts = {}) {
  return new Paragraph({
    spacing: { after: opts.after ?? 100, line: 276 },
    alignment: opts.align,
    children: [new TextRun({ text, size: opts.size ?? 21, color: opts.color ?? '2A2A2A', italics: opts.italics, bold: opts.bold })],
  });
}
function bullet(text, level = 0) {
  return new Paragraph({
    numbering: { reference: 'bullets', level },
    spacing: { after: 40, line: 264 },
    children: [new TextRun({ text, size: 21, color: '2A2A2A' })],
  });
}
function runsBullet(runs, level = 0) {
  return new Paragraph({
    numbering: { reference: 'bullets', level },
    spacing: { after: 40, line: 264 },
    children: runs,
  });
}
function rule() {
  return new Paragraph({
    spacing: { before: 60, after: 120 },
    border: { bottom: { color: 'C9D6E4', style: BorderStyle.SINGLE, size: 8, space: 2 } },
    children: [new TextRun({ text: '', size: 2 })],
  });
}
function callout(text) {
  return new Table({
    width: { size: FULL, type: WidthType.DXA },
    columnWidths: [FULL],
    borders: {
      top: { style: BorderStyle.SINGLE, size: 4, color: BRAND2 },
      bottom: { style: BorderStyle.SINGLE, size: 4, color: BRAND2 },
      left: { style: BorderStyle.SINGLE, size: 24, color: BRAND2 },
      right: { style: BorderStyle.SINGLE, size: 4, color: BRAND2 },
      insideHorizontal: { style: BorderStyle.NONE }, insideVertical: { style: BorderStyle.NONE },
    },
    rows: [new TableRow({
      children: [new TableCell({
        width: { size: FULL, type: WidthType.DXA },
        shading: { type: ShadingType.CLEAR, fill: 'E8F0F9' },
        margins: { top: 120, bottom: 120, left: 200, right: 200 },
        children: [new Paragraph({ children: [new TextRun({ text, size: 20, color: '1F3A5F' })] })],
      })],
    })],
  });
}

// generic table builder. rows[0] = header. widths array sums to FULL.
function makeTable(widths, header, rows, opts = {}) {
  const noBorder = { style: BorderStyle.SINGLE, size: 2, color: 'D0DAE6' };
  const headerCells = header.map((t, i) => new TableCell({
    width: { size: widths[i], type: WidthType.DXA },
    shading: { type: ShadingType.CLEAR, fill: HEADER_FILL },
    margins: { top: 70, bottom: 70, left: 110, right: 110 },
    children: [new Paragraph({
      alignment: (opts.center && i > 0) ? AlignmentType.CENTER : AlignmentType.LEFT,
      children: [new TextRun({ text: t, bold: true, color: 'FFFFFF', size: 19 })],
    })],
  }));
  const bodyRows = rows.map((cells, r) => new TableRow({
    children: cells.map((cell, i) => {
      const val = typeof cell === 'object' ? cell.text : cell;
      let color = '2A2A2A', bold = false;
      if (val === '✓') { color = CHECK; bold = true; }
      if (val === '✗') { color = CROSS; bold = true; }
      return new TableCell({
        width: { size: widths[i], type: WidthType.DXA },
        shading: { type: ShadingType.CLEAR, fill: r % 2 ? ZEBRA : 'FFFFFF' },
        margins: { top: 60, bottom: 60, left: 110, right: 110 },
        children: [new Paragraph({
          alignment: (opts.center && i > 0) ? AlignmentType.CENTER : AlignmentType.LEFT,
          children: [new TextRun({ text: val, size: 19, color, bold: bold || (cell.bold ?? false) })],
        })],
      });
    }),
  }));
  return new Table({
    width: { size: FULL, type: WidthType.DXA },
    columnWidths: widths,
    borders: {
      top: noBorder, bottom: noBorder, left: noBorder, right: noBorder,
      insideHorizontal: noBorder, insideVertical: noBorder,
    },
    rows: [new TableRow({ tableHeader: true, children: headerCells }), ...bodyRows],
  });
}

const C = '✓', X = '✗';

// ================= BUILD =================
const children = [];

// ---- Cover ----
children.push(new Paragraph({ spacing: { before: 600 } , children: [] }));
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 60 },
  children: [new TextRun({ text: '[ YOUR COMPANY NAME ]', bold: true, size: 30, color: BRAND2 })],
}));
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { before: 240, after: 40 },
  children: [new TextRun({ text: 'SOFTWARE PROJECT', bold: true, size: 56, color: BRAND })],
}));
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 200 },
  children: [new TextRun({ text: 'QUOTATION', bold: true, size: 56, color: BRAND })],
}));
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 300 },
  children: [new TextRun({ text: 'Multi-Tenant SaaS E-Commerce Platform', italics: true, size: 26, color: GREY })],
}));
children.push(rule());
// meta table on cover
children.push(makeTable(
  [2600, 6760],
  ['Detail', 'Value'],
  [
    ['Prepared By', '[ Your Company Name ]'],
    ['Prepared For', '[ Client Name / Company ]'],
    ['Quotation No.', 'Q-[YYYY]-[####]'],
    ['Date', '[ DD Month YYYY ]'],
    ['Valid Until', '[ DD Month YYYY ]  (30 days from issue)'],
  ]
));
children.push(new Paragraph({ children: [new PageBreak()] }));

// ---- TOC ----
children.push(h1('Table of Contents'));
children.push(new TableOfContents('Contents', { hyperlink: true, headingStyleRange: '1-1' }));
children.push(new Paragraph({ children: [new PageBreak()] }));

// ---- 1 Executive Summary ----
children.push(h1('1. Executive Summary'));
children.push(p('[Your Company Name] is pleased to present this quotation for the delivery of a production-ready, multi-tenant SaaS E-Commerce Platform. The solution is an already-developed, market-proven software product built for the Bangladesh retail and online-commerce market, with native support for local couriers, payment workflows, and BDT currency.'));
children.push(p('This document sets out the scope of the software, the deliverables, and a range of commercial options so that you can select the engagement model that best fits your business needs — from a licensed deployment through to full source-code ownership and exclusive rights.'));
children.push(callout('Purpose of this quotation: to provide a transparent, itemized commercial proposal for acquiring the platform, along with clearly defined deliverables, support terms, warranty, and ownership rights.'));

// ---- 2 Project Overview ----
children.push(h1('2. Project Overview'));
children.push(h2('2.1 Description'));
children.push(p('A cloud-ready, multi-tenant SaaS E-Commerce platform that allows a platform operator to onboard multiple independent vendors, each running their own online store, point-of-sale, inventory, staff, and order-fulfilment operations under a subscription-based billing model. The system is delivered as three coordinated applications:'));
children.push(makeTable(
  [2400, 4560, 2400],
  ['Application', 'Role', 'Technology'],
  [
    ['Backend API', 'Central business logic, data, auth, integrations', 'Laravel 12 (PHP 8.2)'],
    ['Admin Panel', 'Vendor & super-admin dashboard', 'Nuxt 4 (Vue 3)'],
    ['Storefront', 'Public store & landing pages', 'Nuxt 4 (Vue 3)'],
  ]
));
children.push(h2('2.2 Technology Stack'));
children.push(makeTable(
  [3000, 6360],
  ['Layer', 'Technology'],
  [
    ['Backend Framework', 'Laravel 12 (PHP 8.2+), REST API'],
    ['API & Authentication', 'Laravel Sanctum, magic-link, PIN, Google OAuth'],
    ['Permissions', 'Spatie Laravel Permission (RBAC)'],
    ['Admin Front-End', 'Nuxt 4, Vue 3, Pinia, Tailwind CSS'],
    ['Storefront Front-End', 'Nuxt 4, Vue 3, Pinia, Tailwind CSS'],
    ['Media & Storage', 'Intervention Image, Flysystem (S3-compatible)'],
    ['Courier Integrations', 'Steadfast, Pathao, RedX'],
    ['Marketing', 'Google / Facebook / TikTok pixels & feeds, FB Conversion API'],
    ['Database', 'MySQL / MariaDB'],
  ]
));

// ---- 2.3 Feature Set ----
children.push(h2('2.3 Complete Feature Set'));
children.push(p('The platform ships with a comprehensive feature set across five functional areas:', { after: 60 }));

children.push(h3('A. SaaS Platform (Multi-Tenancy & Core)'));
[
  'Multi-tenant architecture with full per-vendor data isolation',
  'Package / plan system and subscription tiers',
  'Payments and transactions ledger at the platform level',
  'Vendor onboarding and "login as vendor" impersonation for support',
  'Per-vendor shop domain and centralized global settings',
  'Dynamic, platform-managed payment methods',
  'Authentication: email/password, magic-link, PIN, and Google login',
  'Role-Based Access Control (RBAC) and an API-first (Sanctum) design',
].forEach(t => children.push(bullet(t)));

children.push(h3('B. Super Admin'));
[
  'Platform dashboard with KPIs and statistics',
  'Plans / packages management and pricing',
  'Vendor management — create, edit, delete, assign packages, login-as',
  'Finance — payments, subscriptions, and transactions (with stats)',
  'Users, roles, and permissions management',
  'Global settings — general, mail/SMTP, appearance, advanced, payments',
].forEach(t => children.push(bullet(t)));

children.push(h3('C. Vendor (Store Management Suite)'));
[
  'Catalog — products, variants, attributes, categories, brands, units, suppliers',
  'Inventory & warehouse — stock logs, restock, adjustments, barcodes, audit',
  'Point of Sale (POS) — in-store sales, lookups, sales stats',
  'Orders & fulfilment — orders, invoices, courier sync (Steadfast/Pathao/RedX)',
  'Shipping & delivery zones — countries, states, cities, shipping methods',
  'Marketing — coupons, promotions, loyalty program, newsletter, reviews, banners/sliders',
  'Fraud & risk — courier fraud checks, block fake orders, spider intelligence',
  'Shop configuration — SEO/social, pixels & feeds, gateways, SMS/email templates, custom pages',
  'HRM — departments, designations, employees, attendance, leaves, payroll',
  'Reports — sales, analytics, returns, cancels, expenses, coupons, product performance, stock, customers, earnings, tax',
  'Staff & access, notifications, media library, package upgrades, AI automation, help center',
].forEach(t => children.push(bullet(t)));

children.push(h3('D. Landing Pages'));
[
  'Single-product and multiple-product high-conversion landing pages',
  'Multiple theme variations (single v1/v2/v3, multiple)',
  'Slug-based public routes and a landing-page builder with settings',
  'Custom standalone pages, optimized direct-to-checkout for ad traffic',
].forEach(t => children.push(bullet(t)));

children.push(h3('E. Customer (Storefront)'));
[
  'Accounts — register/login, magic-link, Google login, PIN, OTP checkout',
  'Browsing — home, shop, categories, product detail, reviews, vendor storefronts',
  'Cart & checkout — cart, coupons/discounts, payment methods, shipping estimate, OTP',
  'Account panel — dashboard, profile, addresses, orders, order tracking, wishlist, settings',
  'Engagement — newsletter, contact messages, policy pages, SEO feeds (sitemap, Google/FB)',
].forEach(t => children.push(bullet(t)));
children.push(callout('A detailed, itemized feature breakdown is available as a separate appendix (FEATURES) on request.'));

// ---- 3 Deliverables ----
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('3. Deliverables'));
children.push(makeTable(
  [640, 2600, 6120],
  ['#', 'Deliverable', 'Description'],
  [
    ['1', 'Software', 'Fully functional platform (Backend API, Admin Panel, Storefront) per the selected option'],
    ['2', 'Deployment', 'Installation & configuration on the client production server / hosting'],
    ['3', 'Documentation', 'Installation, configuration, and user/admin docs (scope varies by option)'],
    ['4', 'Source Code', 'Complete source code (Options B, C, and D only)'],
    ['5', 'Training', 'Live handover / training sessions (Options C and D; optional add-on for others)'],
  ]
));

// ---- 4 Commercial Options ----
children.push(h1('4. Commercial Options'));
children.push(p('The platform is available under four commercial models. Please select the option that best matches your requirements.'));
children.push(makeTable(
  [3160, 1550, 1550, 1550, 1550],
  ['Feature', 'A — License', 'B — Source', 'C — Source + Support', 'D — Exclusive'],
  [
    ['Production Deployment', C, C, C, C],
    ['Installation & Configuration', C, C, C, C],
    ['Full Source Code', X, C, C, C],
    ['Full Ownership (excl. OSS libs)', X, C, C, C],
    ['Documentation', 'Basic', 'Full', 'Full', 'Full'],
    ['Bug-Fixing Period', '30 days', '90 days', '90 days', '90 days'],
    ['Technical Support', 'Email', 'Basic', 'Priority', 'Priority'],
    ['Maintenance', X, X, '6 months', '6 months'],
    ['Security Updates', X, X, C, C],
    ['Performance Optimization', X, X, C, C],
    ['Minor Feature Enhancements', X, X, C, C],
    ['Training Sessions', X, X, C, C],
    ['Exclusive Ownership', X, X, X, C],
    ['Full IP Transfer', X, X, X, C],
    [{ text: 'Price (BDT)', bold: true }, { text: 'BDT 500,000', bold: true }, { text: 'BDT 1,200,000', bold: true }, { text: 'BDT 1,600,000', bold: true }, { text: 'Negotiated', bold: true }],
  ],
  { center: true }
));

children.push(h3('Option A — License Only'));
children.push(p('A licensed, ready-to-use deployment without source code. Includes production deployment, installation, basic documentation, 30 days of bug fixing, and email support. Lowest price point.'));
children.push(h3('Option B — Source Code Purchase'));
children.push(p('Includes the full source code with full ownership (excluding reusable third-party and open-source libraries, which remain under their respective licenses), deployment, full documentation, 90 days of bug fixing, and basic technical support.'));
children.push(h3('Option C — Source Code + Premium Support'));
children.push(p('Everything in Option B, plus priority support, 6 months maintenance, security updates, performance optimization, minor feature enhancements, and training sessions.'));
children.push(h3('Option D — Exclusive Ownership'));
children.push(p('Everything in Option C, plus exclusive ownership — the software will not be sold or licensed to any other client — and full intellectual property transfer. Price is negotiated separately and typically starts from BDT 2,500,000, depending on scope and exclusivity terms.'));
children.push(callout('Custom Dropshipping Modules: custom-built dropshipping functionality (supplier sourcing, automated order routing, catalog sync, margin management, etc.) is available as an add-on and is quoted separately. See Section 9 — Customization Policy.'));

// ---- 5 Implementation ----
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('5. Implementation'));
children.push(h3('5.1 Installation Process'));
['Environment and server-requirement verification','Provisioning of database and storage','Backend API deployment and environment configuration','Admin Panel and Storefront build and deployment','Integration of third-party credentials (courier, SMS, email, payment)'].forEach(t => children.push(bullet(t)));
children.push(h3('5.2 Deployment'));
['Deployment to the client-provided production server / hosting environment','SSL, domain, and DNS configuration support','Seeding of initial data and administrator account setup'].forEach(t => children.push(bullet(t)));
children.push(h3('5.3 Go-Live'));
['Smoke testing of core flows (catalog, checkout, POS, courier booking)','Final configuration sign-off','Handover and go-live confirmation'].forEach(t => children.push(bullet(t)));

// ---- 6 Support & Maintenance ----
children.push(h1('6. Support & Maintenance'));
children.push(makeTable(
  [2400, 6960],
  ['Item', 'Details'],
  [
    ['Bug Fixes', 'Defects in delivered functionality corrected free of charge within the warranty period'],
    ['Technical Support', 'Guidance on configuration, deployment, and usage (channel & priority vary by option)'],
    ['Response Time', 'Standard: within [X] business days · Priority (C/D): within [X] business hours'],
    ['Working Hours', '[Day]–[Day], [HH:MM]–[HH:MM] ([Time Zone]); excluding public holidays'],
    ['Exclusions', 'New features, third-party outages, server/infrastructure issues, out-of-scope changes'],
  ]
));

// ---- 7 Warranty ----
children.push(h1('7. Warranty'));
children.push(p('The software is warranted against defects in the delivered functionality for the bug-fixing period applicable to the selected option:'));
children.push(bullet('Option A: 30 days from go-live'));
children.push(bullet('Options B, C, D: 90 days from go-live'));
children.push(p('During this period, reproducible bugs in the originally delivered scope will be corrected at no additional cost. The warranty does not cover issues arising from unauthorized code modifications, third-party service failures, server/hosting misconfiguration, or new feature requests.'));

// ---- 8 Source Code Ownership ----
children.push(h1('8. Source Code Ownership'));
children.push(makeTable(
  [2600, 6760],
  ['Model', 'What the Client Receives'],
  [
    ['License Only (A)', 'A right to use the deployed software. No source code, ownership, or resale rights are granted.'],
    ['Source Code (B / C)', 'The complete source code with full ownership for the client’s own use, excluding reusable third-party / OSS libraries. The vendor retains the right to license or sell the same base product to other clients.'],
    ['Exclusive (D)', 'Full source code with exclusive rights and complete IP transfer. The vendor will not sell, license, or reuse the product with any other client after transfer.'],
  ]
));

// ---- 9 Customization Policy ----
children.push(h1('9. Customization Policy'));
children.push(p('Any functionality beyond the delivered scope — including the custom dropshipping modules, new integrations, workflow changes, or bespoke features — will be treated as a separate work item. For each request:'));
children.push(runsBullet([new TextRun({ text: 'The client submits a written requirement.', size: 21 })]));
children.push(runsBullet([new TextRun({ text: '[Your Company Name] provides a separate written estimate (scope, effort, cost, timeline).', size: 21 })]));
children.push(runsBullet([new TextRun({ text: 'Work commences only after written approval and any applicable advance payment.', size: 21 })]));
children.push(p('Customization pricing is not included in the option prices in Section 4.', { italics: true, color: GREY }));

// ---- 10 Client Responsibilities ----
children.push(h1('10. Client Responsibilities'));
children.push(p('To ensure smooth delivery and go-live, the client is responsible for providing:'));
['Production hosting / server meeting the stated system requirements','Domain name and DNS access','SSL certificate','API credentials for third-party services (couriers, SMS, email, payment)','Timely feedback, approvals, and access required during deployment','Any content, branding, or data required for configuration'].forEach(t => children.push(bullet(t)));

// ---- 11 Payment Terms ----
children.push(h1('11. Payment Terms'));
children.push(p('The schedule below is illustrated for Option B — Source Code Purchase (BDT 1,200,000). For any other option, the same percentages apply to the selected package price.', { after: 80 }));
children.push(makeTable(
  [4560, 1800, 3000],
  ['Milestone', 'Percentage', 'Amount (BDT)'],
  [
    ['Package Price (Total)', '100%', 'BDT 1,200,000'],
    ['Advance (on agreement)', '40%', 'BDT 480,000'],
    ['Second Payment (on deployment)', '40%', 'BDT 480,000'],
    ['Final Payment (on go-live / handover)', '20%', 'BDT 240,000'],
  ],
  { center: true }
));
children.push(callout('Payment schedule may be customized by mutual agreement. All amounts are quoted in BDT (Bangladeshi Taka) and are exclusive of applicable taxes, VAT, and third-party fees.'));

// ---- 12 Assumptions ----
children.push(h1('12. Assumptions'));
['The software is delivered as-is based on its current, existing feature set.','The client’s environment meets the documented server and software requirements.','Third-party services (couriers, SMS, email, payment) are active and correctly credentialed by the client.','Requirements provided at the time of agreement are complete and stable.','Work outside the agreed scope is subject to separate quotation (Section 9).'].forEach(t => children.push(bullet(t)));

// ---- 13 Exclusions ----
children.push(h1('13. Exclusions'));
children.push(p('The following are not included in the quoted price and are borne by the client:'));
['Third-party software or plugin licenses','Hosting / server fees','SMS gateway charges','Email service charges','Payment gateway fees and transaction charges','Government fees, taxes, and regulatory charges','Custom feature development (quoted separately per Section 9)'].forEach(t => children.push(bullet(t)));

// ---- 14 Terms & Conditions ----
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('14. Terms & Conditions'));
const terms = [
  ['Project Acceptance', 'The project is deemed accepted upon successful go-live and completion of the agreed deliverables for the selected option.'],
  ['Confidentiality', 'Both parties agree to keep commercial, technical, and business information exchanged under this engagement strictly confidential.'],
  ['Intellectual Property', 'IP rights are governed by the selected commercial option (Section 8). Until full payment is received, all rights remain with [Your Company Name].'],
  ['Force Majeure', 'Neither party is liable for delays or failures caused by events beyond reasonable control (natural disasters, outages, government action, etc.).'],
  ['Limitation of Liability', 'Total liability shall not exceed the total amount paid for the selected option. No liability for indirect, incidental, or consequential damages.'],
  ['Cancellation Policy', 'Either party may cancel with written notice. Work completed and costs incurred up to the cancellation date remain payable.'],
  ['Refund Policy', 'The advance payment is non-refundable once work has commenced. Milestone payments are non-refundable once the milestone is delivered.'],
  ['Validity of Quotation', 'This quotation is valid for 30 days from the date of issue, after which prices and terms are subject to revision.'],
];
terms.forEach(([k, v]) => children.push(runsBullet([
  new TextRun({ text: k + ': ', bold: true, color: BRAND2, size: 21 }),
  new TextRun({ text: v, size: 21 }),
])));

// ---- 15 Acceptance ----
children.push(h1('15. Acceptance'));
children.push(p('By signing below, the client accepts this quotation and its terms and conditions.'));
children.push(makeTable(
  [2360, 3500, 3500],
  ['', 'Client', '[Your Company Name]'],
  [
    ['Name', '____________________', '____________________'],
    ['Signature', '____________________', '____________________'],
    ['Date', '____________________', '____________________'],
    ['Company Stamp', '', ''],
  ]
));
children.push(new Paragraph({ spacing: { before: 300 }, alignment: AlignmentType.CENTER, children: [new TextRun({ text: 'Thank you for considering [Your Company Name]. We look forward to partnering with you.', italics: true, size: 20, color: GREY })] }));
children.push(new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ text: 'Contact: [Email] · [Phone] · [Website]', size: 20, color: GREY })] }));

// ================= DOCUMENT =================
const doc = new Document({
  creator: '[Your Company Name]',
  title: 'Software Project Quotation',
  description: 'SaaS E-Commerce Platform Quotation',
  styles: {
    default: { document: { run: { font: 'Calibri', size: 21 } } },
  },
  numbering: {
    config: [{
      reference: 'bullets',
      levels: [
        { level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT, style: { paragraph: { indent: { left: 420, hanging: 220 } } } },
        { level: 1, format: LevelFormat.BULLET, text: '◦', alignment: AlignmentType.LEFT, style: { paragraph: { indent: { left: 840, hanging: 220 } } } },
      ],
    }],
  },
  features: { updateFields: true },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 },
      },
    },
    footers: {
      default: new Footer({
        children: [
          new Paragraph({
            border: { top: { color: 'C9D6E4', style: BorderStyle.SINGLE, size: 6, space: 6 } },
            tabStops: [{ type: AlignmentType.RIGHT, position: FULL }],
            children: [
              new TextRun({ text: '[Your Company Name]  ·  Software Project Quotation', size: 16, color: GREY }),
              new TextRun({ children: [new PositionalTab({ alignment: PositionalTabAlignment.RIGHT, leader: PositionalTabLeader.NONE, relativeTo: 'margin' })], size: 16 }),
              new TextRun({ children: ['Page ', PageNumber.CURRENT], size: 16, color: GREY }),
            ],
          }),
        ],
      }),
    },
    children,
  }],
});

Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync('Software_Project_Quotation.docx', buf);
  console.log('WROTE Software_Project_Quotation.docx (' + buf.length + ' bytes)');
});
