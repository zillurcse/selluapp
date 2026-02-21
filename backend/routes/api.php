<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/users', [App\Http\Controllers\AdminUserController::class, 'index']);
        Route::get('/users/{user}', [App\Http\Controllers\AdminUserController::class, 'show']);
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
    });


    // Vendor Routes
    Route::prefix('vendor')->group(function () {
        Route::apiResource('attributes/categories', App\Http\Controllers\VendorAttributeController::class);
        Route::apiResource('attributes/products', App\Http\Controllers\VendorAttributeController::class); // This might need to be specific product controller later
        Route::apiResource('attributes/brands', App\Http\Controllers\VendorBrandController::class);
        Route::apiResource('attributes/units', App\Http\Controllers\VendorUnitController::class);
        Route::post('attributes/categories/sort', [App\Http\Controllers\VendorAttributeController::class, 'updateOrder']);
        Route::post('attributes/brands/sort', [App\Http\Controllers\VendorBrandController::class, 'updateOrder']);
        Route::get('profile', [App\Http\Controllers\VendorProfileController::class, 'index']);
        Route::post('profile', [App\Http\Controllers\VendorProfileController::class, 'update']);
        //products routes
        Route::apiResource('products', App\Http\Controllers\ProductController::class);
        Route::get('products/{product}/variants', [App\Http\Controllers\ProductController::class, 'variants']);
        Route::get('products/{slug}', [App\Http\Controllers\ProductController::class, 'slugByStatusProducts']);

        // Customers Routes
        Route::apiResource('customers', App\Http\Controllers\CustomerController::class);

        // Coupons Routes
        Route::apiResource('coupons', App\Http\Controllers\Vendor\CouponController::class);

        // Promotions Routes
        Route::apiResource('promotions', App\Http\Controllers\Vendor\PromotionController::class);

        // Fraud Check Routes
        Route::get('fraud-checks', [App\Http\Controllers\Vendor\FraudCheckController::class, 'index']);
        Route::get('fraud-checks/stats', [App\Http\Controllers\Vendor\FraudCheckController::class, 'stats']);
        Route::post('fraud-checks/settings', [App\Http\Controllers\Vendor\FraudCheckController::class, 'updateSetting']);
        Route::post('fraud-checks/{id}/action', [App\Http\Controllers\Vendor\FraudCheckController::class, 'action']);

        // Shop Delivery
        Route::get('delivery', [App\Http\Controllers\VendorDeliveryController::class, 'index']);
        Route::post('delivery', [App\Http\Controllers\VendorDeliveryController::class, 'store']);

        // Orders
        Route::apiResource('orders', App\Http\Controllers\API\Vendor\OrderController::class);

        // Shop Settings
        Route::get('settings', [App\Http\Controllers\ShopSettingController::class, 'index']);
        Route::post('settings', [App\Http\Controllers\ShopSettingController::class, 'update']);

        // Reports Routes
        Route::prefix('reports')->group(function () {
            Route::get('overview', [\App\Http\Controllers\API\Vendor\ReportController::class, 'overview']);
            Route::get('sales', [\App\Http\Controllers\API\Vendor\ReportController::class, 'sales']);
            Route::get('returns', [\App\Http\Controllers\API\Vendor\ReportController::class, 'returns']);
            Route::get('cancels', [\App\Http\Controllers\API\Vendor\ReportController::class, 'cancels']);
            Route::get('expenses', [\App\Http\Controllers\API\Vendor\ReportController::class, 'expenses']);
            Route::get('coupons', [\App\Http\Controllers\API\Vendor\ReportController::class, 'coupons']);
            Route::get('product-performance', [\App\Http\Controllers\API\Vendor\ReportController::class, 'productPerformance']);
            Route::get('stock', [\App\Http\Controllers\API\Vendor\ReportController::class, 'stock']);
            Route::post('stock/adjust', [\App\Http\Controllers\API\Vendor\ReportController::class, 'adjustStock']);
            Route::get('customers', [\App\Http\Controllers\API\Vendor\ReportController::class, 'customers']);
            Route::get('earnings', [\App\Http\Controllers\API\Vendor\ReportController::class, 'earnings']);
            Route::get('tax', [\App\Http\Controllers\API\Vendor\ReportController::class, 'tax']);
            Route::get('sales-analytics', [\App\Http\Controllers\API\Vendor\ReportController::class, 'salesAnalytics']);
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

        // Landing Pages
        Route::apiResource('landing-pages', App\Http\Controllers\Vendor\LandingPageController::class);

        // Staff Management
        Route::apiResource('staff', App\Http\Controllers\Vendor\VendorStaffController::class);
    });
});
