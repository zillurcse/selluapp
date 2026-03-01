<?php

use App\Http\Controllers\API\StorefrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/webhooks/steadfast', [App\Http\Controllers\Vendor\CourierController::class, 'steadfastWebhook']);



// Public Storefront Routes
Route::get('/storefront', [StorefrontController::class, 'index']);
Route::get('/storefront/products', [StorefrontController::class, 'products']);
Route::get('/storefront/products/{product}', [StorefrontController::class, 'show']);
Route::get('/storefront/landing-page/{slug}', [StorefrontController::class, 'landingPage']);
Route::get('/storefront/vendors/{slug}', [StorefrontController::class, 'vendor']);
Route::get('/storefront/categories', function () {
    return \App\Models\Category::where('is_active', true)->whereNull('parent_id')->get();
});
Route::get('/storefront/states', [StorefrontController::class, 'states']);
Route::get('/storefront/cities', [StorefrontController::class, 'cities']);

Route::post('/checkout/estimate-shipping', [StorefrontController::class, 'estimateShipping']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Cart Routes
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart', [\App\Http\Controllers\CartController::class, 'store']);
    Route::put('/cart/{product}', [\App\Http\Controllers\CartController::class, 'update']);
    Route::delete('/cart/{product}', [\App\Http\Controllers\CartController::class, 'destroy']);

    // Checkout Routes
    Route::post('/storefront/checkout', [\App\Http\Controllers\API\CheckoutController::class, 'placeOrder']);
    Route::post('/storefront/checkout/payment-methods', [\App\Http\Controllers\API\CheckoutController::class, 'getPaymentMethods']);

    // Customer Panel Routes
    Route::prefix('customer')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\API\CustomerPanelController::class, 'dashboard']);
        Route::get('profile', [\App\Http\Controllers\API\CustomerPanelController::class, 'profile']);
        Route::put('profile', [\App\Http\Controllers\API\CustomerPanelController::class, 'updateProfile']);
        Route::put('password', [\App\Http\Controllers\API\CustomerPanelController::class, 'updatePassword']);
        Route::get('orders', [\App\Http\Controllers\API\CustomerPanelController::class, 'orders']);

        Route::get('addresses', [\App\Http\Controllers\API\CustomerPanelController::class, 'addresses']);
        Route::post('addresses', [\App\Http\Controllers\API\CustomerPanelController::class, 'storeAddress']);
        Route::put('addresses/{address}', [\App\Http\Controllers\API\CustomerPanelController::class, 'updateAddress']);
        Route::delete('addresses/{address}', [\App\Http\Controllers\API\CustomerPanelController::class, 'destroyAddress']);
    });

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/users', [App\Http\Controllers\AdminUserController::class, 'index']);
        Route::post('/users', [App\Http\Controllers\AdminUserController::class, 'store']);
        Route::get('/users/{user}', [App\Http\Controllers\AdminUserController::class, 'show']);
        Route::put('/users/{user}', [App\Http\Controllers\AdminUserController::class, 'update']);
        Route::post('/users/{user}/role', [App\Http\Controllers\AdminUserController::class, 'updateRole']);
        Route::delete('/users/{user}', [App\Http\Controllers\AdminUserController::class, 'destroy']);

        // Ecommerce Routes
        Route::apiResource('categories', App\Http\Controllers\CategoryController::class);

        // Role & Permission Routes
        Route::apiResource('roles', App\Http\Controllers\RoleController::class);
        Route::get('permissions', [App\Http\Controllers\PermissionController::class, 'index']);
    });

    // Super Admin Only Routes
    Route::prefix('admin')->group(function () {
        // Dashboard Stats
        Route::get('dashboard', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'dashboardStats']);

        // Package Management
        Route::apiResource('packages', App\Http\Controllers\SuperAdmin\PackageController::class);

        // Vendor Management
        Route::get('vendors', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'index']);
        Route::post('vendors', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'store']);
        Route::get('vendors/{user}', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'show']);
        Route::put('vendors/{user}', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'update']);
        Route::delete('vendors/{user}', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'destroy']);
        Route::post('vendors/{user}/assign-package', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'assignPackage']);
        Route::post('vendors/{user}/login-as', [App\Http\Controllers\SuperAdmin\VendorManagementController::class, 'loginAsVendor']);

        // Finance — Payments
        Route::get('finance/payments/stats', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'paymentsStats']);
        Route::get('finance/payments', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'paymentsIndex']);
        Route::post('finance/payments', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'paymentsStore']);
        Route::put('finance/payments/{payment}', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'paymentsUpdate']);
        Route::delete('finance/payments/{payment}', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'paymentsDestroy']);

        // Finance — Subscriptions
        Route::get('finance/subscriptions/stats', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'subscriptionsStats']);
        Route::get('finance/subscriptions', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'subscriptionsIndex']);
        Route::post('finance/subscriptions', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'subscriptionsStore']);
        Route::put('finance/subscriptions/{subscription}', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'subscriptionsUpdate']);
        Route::delete('finance/subscriptions/{subscription}', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'subscriptionsDestroy']);

        // Finance — Transactions
        Route::get('finance/transactions/stats', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'transactionsStats']);
        Route::get('finance/transactions', [App\Http\Controllers\SuperAdmin\FinanceController::class, 'transactionsIndex']);

        // Global Settings
        Route::get('settings', [App\Http\Controllers\SuperAdmin\GlobalSettingController::class, 'index']);
        Route::post('settings', [App\Http\Controllers\SuperAdmin\GlobalSettingController::class, 'update']);
        Route::post('settings/upload', [App\Http\Controllers\SuperAdmin\GlobalSettingController::class, 'uploadFile']);

        // Dynamic Payment Methods
        Route::apiResource('payment-methods', App\Http\Controllers\API\PaymentMethodController::class);
    });


    // Vendor Routes
    Route::prefix('vendor')->group(function () {
        Route::get('payment-methods', [App\Http\Controllers\API\PaymentMethodController::class, 'index']);
        Route::apiResource('attributes/categories', App\Http\Controllers\Vendor\CategoryController::class);
        Route::apiResource('attributes/products', App\Http\Controllers\Vendor\CategoryController::class); 
        Route::apiResource('attributes/brands', App\Http\Controllers\Vendor\BrandController::class);
        Route::apiResource('attributes/units', App\Http\Controllers\Vendor\UnitController::class);
        Route::post('attributes/categories/sort', [App\Http\Controllers\Vendor\CategoryController::class, 'updateOrder']);
        Route::post('attributes/brands/sort', [App\Http\Controllers\Vendor\BrandController::class, 'updateOrder']);
        Route::get('profile', [App\Http\Controllers\Vendor\ProfileController::class, 'index']);
        Route::post('profile', [App\Http\Controllers\Vendor\ProfileController::class, 'update']);
        //products routes
        Route::apiResource('products', App\Http\Controllers\Vendor\ProductController::class);
        Route::apiResource('product-attributes', \App\Http\Controllers\AttributeController::class);
        Route::get('products/{product}/variants', [App\Http\Controllers\Vendor\ProductController::class, 'variants']);
        Route::get('products/{slug}', [App\Http\Controllers\Vendor\ProductController::class, 'slugByStatusProducts']);

        // Customers Routes
        Route::apiResource('customers', App\Http\Controllers\Vendor\CustomerController::class);

        // Coupons Routes
        Route::apiResource('coupons', App\Http\Controllers\Vendor\CouponController::class);

        // Promotions Routes
        Route::apiResource('promotions', App\Http\Controllers\Vendor\PromotionController::class);

        // Fraud Check Routes
        Route::get('fraud-checks', [App\Http\Controllers\Vendor\FraudCheckController::class, 'index']);
        Route::get('fraud-checks/stats', [App\Http\Controllers\Vendor\FraudCheckController::class, 'stats']);
        Route::post('fraud-checks/settings', [App\Http\Controllers\Vendor\FraudCheckController::class, 'updateSetting']);
        Route::post('fraud-checks/{id}/action', [App\Http\Controllers\Vendor\FraudCheckController::class, 'action']);

        // Shipping Areas
        Route::apiResource('countries', App\Http\Controllers\Vendor\CountryController::class);
        Route::apiResource('states', App\Http\Controllers\Vendor\StateController::class);
        Route::get('all-countries', [App\Http\Controllers\Vendor\StateController::class, 'get_countries']);
        Route::apiResource('cities', App\Http\Controllers\Vendor\CityController::class);
        Route::get('all-states', [App\Http\Controllers\Vendor\CityController::class, 'get_states']);

        Route::get('/pathao/bulk-sync', [App\Http\Controllers\Vendor\CityController::class, 'syncPathaoLocations']);

        // Shop Delivery
        Route::get('delivery', [App\Http\Controllers\Vendor\DeliveryController::class, 'index']);
        Route::post('delivery', [App\Http\Controllers\Vendor\DeliveryController::class, 'store']);

        // Courier Routes
        Route::controller(App\Http\Controllers\Vendor\CourierController::class)->prefix('couriers')->group(function () {
            Route::get('/', 'index');
            Route::post('/update', 'update');
            Route::post('/send-to-pathao', 'sendToPathao');
            Route::post('/send-to-steadfast', 'sendToSteadfast');
            Route::get('/update-status/{id}', 'updateStatus');
            Route::get('/pathao/cities', 'getPathaoCities');
            Route::get('/pathao/zones/{city_id}', 'getPathaoZones');
            Route::get('/pathao/areas/{zone_id}', 'getPathaoAreas');
            Route::get('/pathao/bulk-sync', 'syncPathaoLocations');
        });


        // Orders
        Route::apiResource('orders', App\Http\Controllers\Vendor\OrderController::class);
        Route::post('orders/{order}/sync', [App\Http\Controllers\Vendor\OrderController::class, 'syncCourier']);

        // Shop Settings
        Route::get('settings', [App\Http\Controllers\Vendor\SettingController::class, 'index']);
        Route::post('settings', [App\Http\Controllers\Vendor\SettingController::class, 'update']);

        // Reports Routes
        Route::prefix('reports')->group(function () {
            Route::get('overview', [\App\Http\Controllers\Vendor\ReportController::class, 'overview']);
            Route::get('sales', [\App\Http\Controllers\Vendor\ReportController::class, 'sales']);
            Route::get('returns', [\App\Http\Controllers\Vendor\ReportController::class, 'returns']);
            Route::get('cancels', [\App\Http\Controllers\Vendor\ReportController::class, 'cancels']);
            Route::get('expenses', [\App\Http\Controllers\Vendor\ReportController::class, 'expenses']);
            Route::get('coupons', [\App\Http\Controllers\Vendor\ReportController::class, 'coupons']);
            Route::get('product-performance', [\App\Http\Controllers\Vendor\ReportController::class, 'productPerformance']);
            Route::get('stock', [\App\Http\Controllers\Vendor\ReportController::class, 'stock']);
            Route::post('stock/adjust', [\App\Http\Controllers\Vendor\ReportController::class, 'adjustStock']);
            Route::get('customers', [\App\Http\Controllers\Vendor\ReportController::class, 'customers']);
            Route::get('earnings', [\App\Http\Controllers\Vendor\ReportController::class, 'earnings']);
            Route::get('tax', [\App\Http\Controllers\Vendor\ReportController::class, 'tax']);
            Route::get('sales-analytics', [\App\Http\Controllers\Vendor\ReportController::class, 'salesAnalytics']);
        });

        // Role & Permission Routes
        Route::apiResource('roles', App\Http\Controllers\RoleController::class);
        Route::get('permissions', [App\Http\Controllers\PermissionController::class, 'index']);
        // HRM Routes
        Route::prefix('hrm')->group(function () {
            // Dashboard
            Route::get('stats', [App\Http\Controllers\Vendor\Hrm\HrmDashboardController::class, 'stats']);
            Route::get('recent-activities', [App\Http\Controllers\Vendor\Hrm\HrmDashboardController::class, 'recentActivities']);

            // Departments
            Route::apiResource('departments', App\Http\Controllers\Vendor\Hrm\DepartmentController::class);

            // Designations
            Route::apiResource('designations', App\Http\Controllers\Vendor\Hrm\DesignationController::class);

            // Employees
            Route::apiResource('employees', App\Http\Controllers\Vendor\Hrm\EmployeeController::class);

            // Attendance
            Route::get('attendance', [App\Http\Controllers\Vendor\Hrm\AttendanceController::class, 'index']);
            Route::post('attendance', [App\Http\Controllers\Vendor\Hrm\AttendanceController::class, 'store']);
            Route::post('attendance/bulk', [App\Http\Controllers\Vendor\Hrm\AttendanceController::class, 'bulkStore']);
            Route::get('attendance/summary', [App\Http\Controllers\Vendor\Hrm\AttendanceController::class, 'summary']);

            // Leaves
            Route::get('leaves', [App\Http\Controllers\Vendor\Hrm\LeaveController::class, 'index']);
            Route::post('leaves', [App\Http\Controllers\Vendor\Hrm\LeaveController::class, 'store']);
            Route::post('leaves/{leave}/action', [App\Http\Controllers\Vendor\Hrm\LeaveController::class, 'action']);
            Route::delete('leaves/{leave}', [App\Http\Controllers\Vendor\Hrm\LeaveController::class, 'destroy']);

            // Payroll
            Route::get('payroll', [App\Http\Controllers\Vendor\Hrm\PayrollController::class, 'index']);
            Route::post('payroll/generate', [App\Http\Controllers\Vendor\Hrm\PayrollController::class, 'generate']);
            Route::put('payroll/{payroll}', [App\Http\Controllers\Vendor\Hrm\PayrollController::class, 'update']);
            Route::post('payroll/{payroll}/mark-paid', [App\Http\Controllers\Vendor\Hrm\PayrollController::class, 'markPaid']);
            Route::get('payroll/summary', [App\Http\Controllers\Vendor\Hrm\PayrollController::class, 'summary']);
        });

        // POS Routes
        Route::prefix('pos')->group(function () {
            Route::get('products', [App\Http\Controllers\Vendor\PosController::class, 'products']);
            Route::get('customers', [App\Http\Controllers\Vendor\PosController::class, 'customers']);
            Route::get('categories', [App\Http\Controllers\Vendor\PosController::class, 'categories']);
            Route::get('brands', [App\Http\Controllers\Vendor\PosController::class, 'brands']);
            Route::post('order', [App\Http\Controllers\Vendor\PosController::class, 'placeOrder']);
            Route::get('sales', [App\Http\Controllers\Vendor\PosController::class, 'sales']);
            Route::get('stats', [App\Http\Controllers\Vendor\PosController::class, 'stats']);
        });

        // Barcodes
        Route::prefix('barcodes')->group(function () {
            Route::get('scan', [App\Http\Controllers\Vendor\BarcodeController::class, 'scan']);
            Route::post('generate', [App\Http\Controllers\Vendor\BarcodeController::class, 'generate']);
            Route::post('print', [App\Http\Controllers\Vendor\BarcodeController::class, 'printLabels']);
            Route::post('audit', [App\Http\Controllers\Vendor\BarcodeController::class, 'audit']);
            Route::post('mark-as-printed', [App\Http\Controllers\Vendor\BarcodeController::class, 'markAsPrinted']);
        });

        // Stock Management
        Route::prefix('stock')->group(function () {
            Route::get('logs', [App\Http\Controllers\Vendor\StockController::class, 'logs']);
            Route::post('restock', [App\Http\Controllers\Vendor\StockController::class, 'restock']);
        });

        // Suppliers CRUD
        Route::apiResource('suppliers', App\Http\Controllers\Vendor\SupplierController::class);

        // Landing Pages
        Route::apiResource('landing-pages', App\Http\Controllers\Vendor\LandingPageController::class);

        // Staff Management
        Route::apiResource('staff', App\Http\Controllers\Vendor\VendorStaffController::class);

        // Media Library
        Route::apiResource('media', App\Http\Controllers\Vendor\UploadController::class);

        // Packages
        Route::get('packages', [App\Http\Controllers\Vendor\PackageController::class, 'index']);
        Route::post('packages/{package}/purchase', [App\Http\Controllers\Vendor\PackageController::class, 'purchase']);
    });
});
