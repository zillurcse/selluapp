<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Package;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Reset permission cache ─────────────────────────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ────────────────────────────────────────────────────────
        $permissions = [
            // Brands
            'brands.view', 'brands.create', 'brands.edit', 'brands.delete', 'brands.sort',
            // Units
            'units.view', 'units.create', 'units.edit', 'units.delete',
            // Categories
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete', 'categories.sort',
            // Attributes
            'attributes.view', 'attributes.create', 'attributes.edit', 'attributes.delete', 'attributes.sort',
            // Products
            'products.view', 'products.create', 'products.edit', 'products.delete',
            // Customers
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            // Coupons
            'coupons.view', 'coupons.create', 'coupons.edit', 'coupons.delete',
            // Promotions
            'promotions.view', 'promotions.create', 'promotions.edit', 'promotions.delete',
            // Fraud Check
            'fraud_check.view', 'fraud_check.manage',
            // Delivery
            'delivery.view', 'delivery.manage',
            // Orders
            'orders.view', 'orders.edit', 'orders.delete',
            // Settings
            'settings.view', 'settings.manage',
            // Reports
            'reports.view',
            // POS
            'pos.view', 'pos.manage',
            // Landing Pages
            'landing_pages.view', 'landing_pages.create', 'landing_pages.edit', 'landing_pages.delete',
            // HRM
            'hrm.dashboard.view',
            'hrm.departments.view', 'hrm.departments.create', 'hrm.departments.edit', 'hrm.departments.delete',
            'hrm.designations.view', 'hrm.designations.create', 'hrm.designations.edit', 'hrm.designations.delete',
            'hrm.employees.view', 'hrm.employees.create', 'hrm.employees.edit', 'hrm.employees.delete',
            'hrm.attendance.view', 'hrm.attendance.manage',
            'hrm.leaves.view', 'hrm.leaves.manage',
            'hrm.payroll.view', 'hrm.payroll.manage',
            // Roles & Permissions
            'roles.view', 'roles.manage',
            // Staff Management
            'staff.view', 'staff.create', 'staff.edit', 'staff.delete',
            
            // SUPER ADMIN / PLATFORM MANAGEMENT
            'admin.dashboard.view',
            'admin.users.view', 'admin.users.manage',
            'admin.plans.view', 'admin.plans.manage',
            'admin.vendors.view', 'admin.vendors.manage',
            'admin.payments.view', 'admin.payments.manage',
            'admin.subscriptions.view', 'admin.subscriptions.manage',
            'admin.transactions.view', 'admin.transactions.manage',
            'admin.settings.view', 'admin.settings.manage',
            'admin.settings.general', 'admin.settings.payments', 'admin.settings.mail',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─── Roles ──────────────────────────────────────────────────────────────
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'admin']);
        $roleVendor     = Role::firstOrCreate(['name' => 'vendor']);
        $roleStaff      = Role::firstOrCreate(['name' => 'staff']);
        $roleCustomer   = Role::firstOrCreate(['name' => 'customer']);

        // Super-admin gets everything
        $roleSuperAdmin->syncPermissions(Permission::all());

        // Vendor gets all vendor-level permissions (no admin.* permissions)
        $vendorPermissions = Permission::where('name', 'not like', 'admin.%')->pluck('name')->toArray();
        $roleVendor->syncPermissions($vendorPermissions);

        // Staff role gets a sensible default subset (can be adjusted per vendor via role management)
        $roleStaff->syncPermissions([
            'brands.view', 'units.view', 'categories.view', 'attributes.view',
            'products.view', 'products.create', 'products.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'coupons.view',
            'orders.view', 'orders.edit',
            'reports.view',
            'pos.view', 'pos.manage',
            'delivery.view',
            'hrm.dashboard.view',
            'hrm.employees.view',
            'hrm.attendance.view',
            'hrm.leaves.view',
        ]);

        // Admin gets a read-heavy subset
        $roleAdmin->syncPermissions([
            'brands.view', 'units.view', 'attributes.view', 'products.view',
            'customers.view', 'orders.view', 'reports.view', 'settings.view',
        ]);

        // ─── Default Packages ────────────────────────────────────────────────────
        // Features are used by CheckPackageFeature middleware to gate controller access
        // Feature names match what's checked in the middleware (case-insensitive partial match)
        $starter = Package::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name'           => 'Starter',
                'description'    => 'Perfect for small shops just getting started. Core e-commerce features included.',
                'price'          => 0,
                'duration'       => 'monthly',
                'product_limit'  => 50,
                'order_limit'    => 200,
                'employee_limit' => 3,
                'pos_access'     => false,
                'hrm_access'     => false,
                'is_active'      => true,
                'features'       => [
                    'Products',
                    'Orders',
                    'Customers',
                    'Basic Reports',
                ],
            ]
        );

        $professional = Package::updateOrCreate(
            ['slug' => 'professional'],
            [
                'name'           => 'Professional',
                'description'    => 'For growing businesses. Includes POS, advanced reports, and unlimited products.',
                'price'          => 1999,
                'duration'       => 'monthly',
                'product_limit'  => -1,
                'order_limit'    => -1,
                'employee_limit' => 10,
                'pos_access'     => true,
                'hrm_access'     => false,
                'is_active'      => true,
                'features'       => [
                    'Products',
                    'Orders',
                    'Customers',
                    'Promotions',
                    'Fraud Check',
                    'Advanced Reports',
                    'POS System',
                    'Delivery',
                ],
            ]
        );

        $enterprise = Package::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name'           => 'Enterprise',
                'description'    => 'Full-featured plan for large operations. Everything included — HRM, POS, and more.',
                'price'          => 4999,
                'duration'       => 'monthly',
                'product_limit'  => -1,
                'order_limit'    => -1,
                'employee_limit' => -1,
                'pos_access'     => true,
                'hrm_access'     => true,
                'is_active'      => true,
                'features'       => [
                    'Products',
                    'Orders',
                    'Customers',
                    'Promotions',
                    'Fraud Check',
                    'Advanced Reports',
                    'POS System',
                    'Delivery',
                    'HRM Suite',
                    'Landing Pages',
                    'Priority Support',
                ],
            ]
        );

        // ─── Super Admin User ───────────────────────────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->syncRoles($roleSuperAdmin);

        // ─── Admin / Manager User ───────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin Manager',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles($roleAdmin);

        // ─── Demo Vendor User (Starter plan) ────────────────────────────────────
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name'     => 'Demo Vendor',
                'password' => Hash::make('password'),
            ]
        );
        $vendor->syncRoles($roleVendor);

        VendorProfile::updateOrCreate(
            ['user_id' => $vendor->id],
            [
                'store_name'           => 'Demo Store',
                'store_slug'           => 'demo-store',
                'phone'                => '+8801700000001',
                'email'                => 'vendor@example.com',
                'address'              => 'Dhaka, Bangladesh',
                'description'          => 'This is the demo vendor store.',
                'package_id'           => $starter->id,
                'package_expiry_date'  => now()->addMonth(),
            ]
        );

        // ─── Demo Vendor 2 (Professional plan) ──────────────────────────────────
        $vendor2 = User::firstOrCreate(
            ['email' => 'vendor2@example.com'],
            [
                'name'     => 'Pro Vendor',
                'password' => Hash::make('password'),
            ]
        );
        $vendor2->syncRoles($roleVendor);

        VendorProfile::updateOrCreate(
            ['user_id' => $vendor2->id],
            [
                'store_name'           => 'Pro Shop',
                'store_slug'           => 'pro-shop',
                'phone'                => '+8801700000002',
                'email'                => 'vendor2@example.com',
                'address'              => 'Chittagong, Bangladesh',
                'description'          => 'A professional tier demo vendor.',
                'package_id'           => $professional->id,
                'package_expiry_date'  => now()->addMonth(),
            ]
        );

        // ─── Demo Vendor 3 (Enterprise plan) ────────────────────────────────────
        $vendor3 = User::firstOrCreate(
            ['email' => 'vendor3@example.com'],
            [
                'name'     => 'Enterprise Vendor',
                'password' => Hash::make('password'),
            ]
        );
        $vendor3->syncRoles($roleVendor);

        VendorProfile::updateOrCreate(
            ['user_id' => $vendor3->id],
            [
                'store_name'           => 'Enterprise Hub',
                'store_slug'           => 'enterprise-hub',
                'phone'                => '+8801700000003',
                'email'                => 'vendor3@example.com',
                'address'              => 'Sylhet, Bangladesh',
                'description'          => 'An enterprise tier demo vendor.',
                'package_id'           => $enterprise->id,
                'package_expiry_date'  => now()->addYear(),
            ]
        );

        // ─── Demo Staff User (belongs to Enterprise Vendor) ─────────────────────
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name'      => 'Demo Staff',
                'password'  => Hash::make('password'),
                'vendor_id' => $vendor3->id,
            ]
        );
        $staffUser->syncRoles($roleStaff);

        $this->command->info('✅ Roles, permissions, packages, and users seeded successfully.');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Package'],
            [
                ['super-admin', 'superadmin@example.com', 'password', '—'],
                ['admin',       'admin@example.com',      'password', '—'],
                ['vendor',      'vendor@example.com',     'password', 'Starter (Free)'],
                ['vendor',      'vendor2@example.com',    'password', 'Professional'],
                ['vendor',      'vendor3@example.com',    'password', 'Enterprise'],
                ['staff',       'staff@example.com',      'password', 'Enterprise (via owner)'],
            ]
        );
    }
}
