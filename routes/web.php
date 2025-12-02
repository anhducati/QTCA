<?php

use Illuminate\Support\Facades\Route;

// ====== AUTH CONTROLLERS ======
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;

// ====== SYSTEM CONTROLLERS ======
use App\Http\Controllers\LanguageController;

// ====== BACKEND CONTROLLERS ======
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\MajorController;

// ====== XE - KHO - NHẬP XUẤT ======
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\VehicleModelController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\VehicleController;
use App\Http\Controllers\Backend\ImportReceiptController;
use App\Http\Controllers\Backend\ExportReceiptController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\StockTakeController;
use App\Http\Controllers\Backend\InventoryAdjustmentController;
use App\Http\Controllers\Backend\InventoryLogController;



/*
|--------------------------------------------------------------------------
| CLIENT LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/', [ClientAuthController::class, 'login'])
    ->name('client.login');

Route::post('/', [ClientAuthController::class, 'handle_login'])
    ->name('client.login.post');

Route::get('/dang-nhap', [ClientAuthController::class, 'login'])
    ->name('client.login');

Route::post('/dang-nhap', [ClientAuthController::class, 'handle_login']);

Route::get('/quen-mat-khau', [ClientAuthController::class, 'forgot'])
    ->name('client.forgot');

Route::post('/quen-mat-khau', [ClientAuthController::class, 'handle_forgot']);

Route::get('/reset/{token}', [ClientAuthController::class, 'reset_password'])
    ->name('client.reset');

Route::post('/reset/{token}', [ClientAuthController::class, 'handle_reset_password']);

Route::post('/change-language', [LanguageController::class, 'changeLanguage'])
    ->name('change.language');



/*
|--------------------------------------------------------------------------
| ADMIN LOGIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    Route::get('/login', [AuthController::class, 'login'])
        ->name('admin.login');

    Route::post('/login', [AuthController::class, 'handleLogin']);

    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('admin.logout');



    /*
    |--------------------------------------------------------------------------
    | ADMIN MAIN (REQUIRE LOGIN)
    |--------------------------------------------------------------------------
    */
    Route::middleware('check.login')->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        // Đổi mật khẩu
        Route::post('/changePassword', [UserController::class, 'changePassword'])
            ->name('admin.user.changepassword');


        /*
        |--------------------------------------------------------------------------
        | CHỈ ADMIN ĐƯỢC QUẢN LÝ USER
        |--------------------------------------------------------------------------
        */
        Route::middleware('admin')->group(function () {

            Route::get('/user', [UserController::class, 'index'])->name('admin.user.index');
            Route::get('/user/create', [UserController::class, 'create_user'])->name('admin.user.create');
            Route::post('/user/create', [UserController::class, 'handle_create_user']);

            Route::get('/user/update/{id}', [UserController::class, 'update_user'])->name('admin.user.update');
            Route::put('/user/update/{id}', [UserController::class, 'handle_update_user'])->name('admin.user.update');

            Route::get('/user/delete/{id}', [UserController::class, 'delete_user'])->name('admin.user.delete');
        });



        /*
        |--------------------------------------------------------------------------
        | BLOG - CATEGORY - MAJORS (DỰ ÁN CŨ)
        |--------------------------------------------------------------------------
        */

        // CATEGORY
        Route::get('/category', [CategoryController::class, 'index'])->name('admin.category.index');
        Route::get('/category/create', [CategoryController::class, 'create_category'])->name('admin.category.create');
        Route::post('/category/create', [CategoryController::class, 'handle_create_category']);

        Route::get('/category/update/{id}', [CategoryController::class, 'update_category'])->name('admin.category.update');
        Route::put('/category/update/{id}', [CategoryController::class, 'handle_update_category'])->name('admin.category.update');

        Route::get('/category/delete/{id}', [CategoryController::class, 'delete_category'])->name('admin.category.delete');


        // BLOG
        Route::get('/blog', [BlogController::class, 'blog'])->name('admin.blog');
        Route::get('/blog/recycle', [BlogController::class, 'recycle'])->name('admin.blog.recycle');
        Route::get('/blog/create', [BlogController::class, 'create_blog'])->name('admin.blog.create');
        Route::post('/blog/create', [BlogController::class, 'handle_create_blog']);

        Route::get('/blog/update/{id}', [BlogController::class, 'update_blog'])->name('admin.blog.update');
        Route::put('/blog/update/{id}', [BlogController::class, 'handle_update_blog']);

        Route::get('/blog/delete/{id}', [BlogController::class, 'delete_blog'])->name('admin.blog.delete');
        Route::get('/blog/restore/{id}', [BlogController::class, 'restore'])->name('admin.blog.restore');

        Route::get('/blog/force-delete/{id}', [BlogController::class, 'force_delete'])->name('admin.blog.force-delete');


        // MAJORS
        Route::get('/majors', [MajorController::class, 'major'])->name('admin.major');
        Route::get('/majors/create', [MajorController::class, 'create_major'])->name('admin.major.create');
        Route::post('/majors/create', [MajorController::class, 'handle_create_major']);

        Route::get('/majors/{id}/update', [MajorController::class, 'update_major'])->name('admin.major.update');
        Route::put('/majors/{id}/update', [MajorController::class, 'handle_update_major']);

        Route::get('/majors/{id}/delete', [MajorController::class, 'delete_major'])->name('admin.major.delete');



        /*
        |--------------------------------------------------------------------------
        | HỆ THỐNG QUẢN LÝ NHẬP - XUẤT - XE (FULL ROUTES TIẾNG VIỆT)
        |--------------------------------------------------------------------------
        */


        // ===== Hãng xe =====
        Route::get('/hang-xe', [BrandController::class, 'index'])
            ->name('admin.brands.index')
            ->middleware('module_permission:brands,read');

        Route::get('/hang-xe/tao-moi', [BrandController::class, 'create'])
            ->name('admin.brands.create')
            ->middleware('module_permission:brands,create');

        Route::post('/hang-xe', [BrandController::class, 'store'])
            ->name('admin.brands.store')
            ->middleware('module_permission:brands,create');

        Route::get('/hang-xe/{brand}/sua', [BrandController::class, 'edit'])
            ->name('admin.brands.edit')
            ->middleware('module_permission:brands,update');

        Route::put('/hang-xe/{brand}', [BrandController::class, 'update'])
            ->name('admin.brands.update')
            ->middleware('module_permission:brands,update');

        Route::delete('/hang-xe/{brand}', [BrandController::class, 'destroy'])
            ->name('admin.brands.destroy')
            ->middleware('module_permission:brands,delete');


        // ===== Dòng xe =====
        Route::get('/dong-xe', [VehicleModelController::class, 'index'])
            ->name('admin.models.index')
            ->middleware('module_permission:models,read');

        Route::get('/dong-xe/tao-moi', [VehicleModelController::class, 'create'])
            ->name('admin.models.create')
            ->middleware('module_permission:models,create');

        Route::post('/dong-xe', [VehicleModelController::class, 'store'])
            ->name('admin.models.store')
            ->middleware('module_permission:models,create');

        Route::get('/dong-xe/{model}/sua', [VehicleModelController::class, 'edit'])
            ->name('admin.models.edit')
            ->middleware('module_permission:models,update');

        Route::put('/dong-xe/{model}', [VehicleModelController::class, 'update'])
            ->name('admin.models.update')
            ->middleware('module_permission:models,update');

        Route::delete('/dong-xe/{model}', [VehicleModelController::class, 'destroy'])
            ->name('admin.models.destroy')
            ->middleware('module_permission:models,delete');


        // ===== Màu xe =====
        Route::get('/mau-xe', [ColorController::class, 'index'])
            ->name('admin.colors.index')
            ->middleware('module_permission:colors,read');

        Route::get('/mau-xe/tao-moi', [ColorController::class, 'create'])
            ->name('admin.colors.create')
            ->middleware('module_permission:colors,create');

        Route::post('/mau-xe', [ColorController::class, 'store'])
            ->name('admin.colors.store')
            ->middleware('module_permission:colors,create');

        Route::get('/mau-xe/{color}/sua', [ColorController::class, 'edit'])
            ->name('admin.colors.edit')
            ->middleware('module_permission:colors,update');

        Route::put('/mau-xe/{color}', [ColorController::class, 'update'])
            ->name('admin.colors.update')
            ->middleware('module_permission:colors,update');

        Route::delete('/mau-xe/{color}', [ColorController::class, 'destroy'])
            ->name('admin.colors.destroy')
            ->middleware('module_permission:colors,delete');


        // ===== Kho =====
        Route::get('/kho', [WarehouseController::class, 'index'])
            ->name('admin.warehouses.index')
            ->middleware('module_permission:warehouses,read');

        Route::get('/kho/tao-moi', [WarehouseController::class, 'create'])
            ->name('admin.warehouses.create')
            ->middleware('module_permission:warehouses,create');

        Route::post('/kho', [WarehouseController::class, 'store'])
            ->name('admin.warehouses.store')
            ->middleware('module_permission:warehouses,create');

        Route::get('/kho/{warehouse}/sua', [WarehouseController::class, 'edit'])
            ->name('admin.warehouses.edit')
            ->middleware('module_permission:warehouses,update');

        Route::put('/kho/{warehouse}', [WarehouseController::class, 'update'])
            ->name('admin.warehouses.update')
            ->middleware('module_permission:warehouses,update');

        Route::delete('/kho/{warehouse}', [WarehouseController::class, 'destroy'])
            ->name('admin.warehouses.destroy')
            ->middleware('module_permission:warehouses,delete');


        // ===== Nhà cung cấp =====
        Route::get('/nha-cung-cap', [SupplierController::class, 'index'])
            ->name('admin.suppliers.index')
            ->middleware('module_permission:suppliers,read');

        Route::get('/nha-cung-cap/tao-moi', [SupplierController::class, 'create'])
            ->name('admin.suppliers.create')
            ->middleware('module_permission:suppliers,create');

        Route::post('/nha-cung-cap', [SupplierController::class, 'store'])
            ->name('admin.suppliers.store')
            ->middleware('module_permission:suppliers,create');

        Route::get('/nha-cung-cap/{supplier}/sua', [SupplierController::class, 'edit'])
            ->name('admin.suppliers.edit')
            ->middleware('module_permission:suppliers,update');

        Route::put('/nha-cung-cap/{supplier}', [SupplierController::class, 'update'])
            ->name('admin.suppliers.update')
            ->middleware('module_permission:suppliers,update');

        Route::delete('/nha-cung-cap/{supplier}', [SupplierController::class, 'destroy'])
            ->name('admin.suppliers.destroy')
            ->middleware('module_permission:suppliers,delete');


        // ===== Khách hàng =====
        Route::get('/khach-hang', [CustomerController::class, 'index'])
            ->name('admin.customers.index')
            ->middleware('module_permission:customers,read');

        Route::get('/khach-hang/tao-moi', [CustomerController::class, 'create'])
            ->name('admin.customers.create')
            ->middleware('module_permission:customers,create');

        Route::post('/khach-hang', [CustomerController::class, 'store'])
            ->name('admin.customers.store')
            ->middleware('module_permission:customers,create');

        Route::get('/khach-hang/{customer}/sua', [CustomerController::class, 'edit'])
            ->name('admin.customers.edit')
            ->middleware('module_permission:customers,update');

        Route::put('/khach-hang/{customer}', [CustomerController::class, 'update'])
            ->name('admin.customers.update')
            ->middleware('module_permission:customers,update');

        Route::delete('/khach-hang/{customer}', [CustomerController::class, 'destroy'])
            ->name('admin.customers.destroy')
            ->middleware('module_permission:customers,delete');

        Route::get('/khach-hang/{customer}', [CustomerController::class, 'show'])
            ->name('admin.customers.show')
            ->middleware('module_permission:customers,read');


        // ===== Xe =====
        // Route::get('/xe', [VehicleController::class, 'index'])
        //     ->name('admin.vehicles.index')
        //     ->middleware('module_permission:vehicles,read');

        // Route::get('/xe/tao-moi', [VehicleController::class, 'create'])
        //     ->name('admin.vehicles.create')
        //     ->middleware('module_permission:vehicles,create');

        // Route::post('/xe', [VehicleController::class, 'store'])
        //     ->name('admin.vehicles.store')
        //     ->middleware('module_permission:vehicles,create');

        // Route::get('/xe/{vehicle}/sua', [VehicleController::class, 'edit'])
        //     ->name('admin.vehicles.edit')
        //     ->middleware('module_permission:vehicles,update');

        // Route::put('/xe/{vehicle}', [VehicleController::class, 'update'])
        //     ->name('admin.vehicles.update')
        //     ->middleware('module_permission:vehicles,update');

        // Route::delete('/xe/{vehicle}', [VehicleController::class, 'destroy'])
        //     ->name('admin.vehicles.destroy')
        //     ->middleware('module_permission:vehicles,delete');


                // Xe
        Route::get('/xe', [VehicleController::class, 'index'])
            ->name('admin.vehicles.index')
            ->middleware('module_permission:vehicles,read');

        Route::get('/xe/tao-moi', [VehicleController::class, 'create'])
            ->name('admin.vehicles.create')
            ->middleware('module_permission:vehicles,create');

        Route::post('/xe', [VehicleController::class, 'store'])
            ->name('admin.vehicles.store')
            ->middleware('module_permission:vehicles,create');

        Route::get('/xe/{vehicle}', [VehicleController::class, 'show'])
            ->name('admin.vehicles.show')
            ->middleware('module_permission:vehicles,read');

        Route::get('/xe/{vehicle}/sua', [VehicleController::class, 'edit'])
            ->name('admin.vehicles.edit')
            ->middleware('module_permission:vehicles,update');

        Route::put('/xe/{vehicle}', [VehicleController::class, 'update'])
            ->name('admin.vehicles.update')
            ->middleware('module_permission:vehicles,update');

        Route::delete('/xe/{vehicle}', [VehicleController::class, 'destroy'])
            ->name('admin.vehicles.destroy')
            ->middleware('module_permission:vehicles,delete');

        // Kết thúc demo
        Route::post('/xe/{vehicle}/ket-thuc-demo', [VehicleController::class, 'endDemo'])
            ->name('admin.vehicles.end_demo')
            ->middleware('module_permission:vehicles,update');


        // ===== Phiếu nhập =====
        Route::get('/phieu-nhap', [ImportReceiptController::class, 'index'])
            ->name('admin.import_receipts.index')
            ->middleware('module_permission:import_receipts,read');

        Route::get('/phieu-nhap/tao-moi', [ImportReceiptController::class, 'create'])
            ->name('admin.import_receipts.create')
            ->middleware('module_permission:import_receipts,create');

        Route::post('/phieu-nhap', [ImportReceiptController::class, 'store'])
            ->name('admin.import_receipts.store')
            ->middleware('module_permission:import_receipts,create');

        Route::get('/phieu-nhap/{importReceipt}', [ImportReceiptController::class, 'show'])
            ->name('admin.import_receipts.show')
            ->middleware('module_permission:import_receipts,read');

        Route::get('/phieu-nhap/{importReceipt}/sua', [ImportReceiptController::class, 'edit'])
            ->name('admin.import_receipts.edit')
            ->middleware('module_permission:import_receipts,update');

        Route::put('/phieu-nhap/{importReceipt}', [ImportReceiptController::class, 'update'])
            ->name('admin.import_receipts.update')
            ->middleware('module_permission:import_receipts,update');

        Route::delete('/phieu-nhap/{importReceipt}', [ImportReceiptController::class, 'destroy'])
            ->name('admin.import_receipts.destroy')
            ->middleware('module_permission:import_receipts,delete');

                    // *** Thêm xe trong phiếu nhập (chỉ ai có quyền tạo xe mới được dùng) ***
        Route::get('/phieu-nhap/{importReceipt}/them-xe', [VehicleController::class, 'createForImport'])
            ->name('admin.import_receipts.vehicles.create')
            ->middleware('module_permission:vehicles,create');

        Route::post('/phieu-nhap/{importReceipt}/them-xe', [VehicleController::class, 'storeForImport'])
            ->name('admin.import_receipts.vehicles.store')
            ->middleware('module_permission:vehicles,create');    

        // ĐÁNH DẤU ĐÃ THANH TOÁN
            //
            Route::post('/phieu-nhap/{importReceipt}/da-thanh-toan', 
                [ImportReceiptController::class, 'markPaid'])
                ->name('admin.import_receipts.mark_paid')
                ->middleware('module_permission:import_receipts,update');

            //
            // ĐÁNH DẤU ĐÃ NHẬN GIẤY TỜ
            //
            Route::post('/phieu-nhap/{importReceipt}/da-nhan-giay-to', 
                [ImportReceiptController::class, 'markDocsReceived'])
                ->name('admin.import_receipts.mark_docs_received')
                ->middleware('module_permission:import_receipts,update');


        // ===== Phiếu xuất =====
        Route::get('/phieu-xuat', [ExportReceiptController::class, 'index'])
            ->name('admin.export_receipts.index')
            ->middleware('module_permission:export_receipts,read');

        Route::get('/phieu-xuat/tao-moi', [ExportReceiptController::class, 'create'])
            ->name('admin.export_receipts.create')
            ->middleware('module_permission:export_receipts,create');

        Route::post('/phieu-xuat', [ExportReceiptController::class, 'store'])
            ->name('admin.export_receipts.store')
            ->middleware('module_permission:export_receipts,create');

        Route::get('/phieu-xuat/{exportReceipt}', [ExportReceiptController::class, 'show'])
            ->name('admin.export_receipts.show')
            ->middleware('module_permission:export_receipts,read');

        Route::get('/phieu-xuat/{exportReceipt}/sua', [ExportReceiptController::class, 'edit'])
            ->name('admin.export_receipts.edit')
            ->middleware('module_permission:export_receipts,update');

        Route::put('/phieu-xuat/{exportReceipt}', [ExportReceiptController::class, 'update'])
            ->name('admin.export_receipts.update')
            ->middleware('module_permission:export_receipts,update');

        Route::delete('/phieu-xuat/{exportReceipt}', [ExportReceiptController::class, 'destroy'])
            ->name('admin.export_receipts.destroy')
            ->middleware('module_permission:export_receipts,delete');

        Route::post('/phieu-xuat/{exportReceipt}/nhan-tien', 
            [ExportReceiptController::class, 'markPaid'])
            ->name('admin.export_receipts.mark_paid')
            ->middleware('module_permission:export_receipts,update');

        Route::post('/phieu-xuat/{exportReceipt}/giao-giay-to', 
            [ExportReceiptController::class, 'markDocsDelivered'])
            ->name('admin.export_receipts.mark_docs_delivered')
            ->middleware('module_permission:export_receipts,update');


        // ===== Phiếu thu (Payment) =====
        Route::get('/phieu-thu', [PaymentController::class, 'index'])
            ->name('admin.payments.index')
            ->middleware('module_permission:payments,read');

        Route::get('/phieu-xuat/{exportReceipt}/phieu-thu/tao-moi', [PaymentController::class, 'create'])
            ->name('admin.payments.create')
            ->middleware('module_permission:payments,create');

        Route::post('/phieu-xuat/{exportReceipt}/phieu-thu', [PaymentController::class, 'store'])
            ->name('admin.payments.store')
            ->middleware('module_permission:payments,create');

        Route::delete('/phieu-thu/{payment}', [PaymentController::class, 'destroy'])
            ->name('admin.payments.destroy')
            ->middleware('module_permission:payments,delete');


        // ===== Kiểm kê =====
        Route::get('/kiem-ke', [StockTakeController::class, 'index'])
            ->name('admin.stock_takes.index')
            ->middleware('module_permission:stock_takes,read');

        Route::get('/kiem-ke/tao-moi', [StockTakeController::class, 'create'])
            ->name('admin.stock_takes.create')
            ->middleware('module_permission:stock_takes,create');

        Route::post('/kiem-ke', [StockTakeController::class, 'store'])
            ->name('admin.stock_takes.store')
            ->middleware('module_permission:stock_takes,create');

        Route::get('/kiem-ke/{stockTake}', [StockTakeController::class, 'show'])
            ->name('admin.stock_takes.show')
            ->middleware('module_permission:stock_takes,read');

        Route::get('/kiem-ke/{stockTake}/sua', [StockTakeController::class, 'edit'])
            ->name('admin.stock_takes.edit')
            ->middleware('module_permission:stock_takes,update');

        Route::put('/kiem-ke/{stockTake}', [StockTakeController::class, 'update'])
            ->name('admin.stock_takes.update')
            ->middleware('module_permission:stock_takes,update');

        Route::delete('/kiem-ke/{stockTake}', [StockTakeController::class, 'destroy'])
            ->name('admin.stock_takes.destroy')
            ->middleware('module_permission:stock_takes,delete');


        // ➕ CẬP NHẬT DANH SÁCH DÒNG KIỂM KÊ (nếu anh dùng nút Lưu danh sách)
        Route::post('/kiem-ke/{stockTake}/cap-nhat-dong', [StockTakeController::class, 'updateItems'])
            ->name('admin.stock_takes.update_items')
            ->middleware('module_permission:stock_takes,update');

        // ✅ XÁC NHẬN PHIẾU KIỂM KÊ (đổi status từ draft -> confirmed)
        Route::post('/kiem-ke/{stockTake}/xac-nhan', [StockTakeController::class, 'confirm'])
            ->name('admin.stock_takes.confirm')
            ->middleware('module_permission:stock_takes,update');


        // ===== Điều chỉnh tồn kho =====
        Route::get('/dieu-chinh', [InventoryAdjustmentController::class, 'index'])
            ->name('admin.inventory_adjustments.index')
            ->middleware('module_permission:inventory_adjustments,read');

        Route::get('/dieu-chinh/{adjustment}', [InventoryAdjustmentController::class, 'show'])
            ->name('admin.inventory_adjustments.show')
            ->middleware('module_permission:inventory_adjustments,read');

        Route::get('/dieu-chinh/tao-moi/{stockTake?}', [InventoryAdjustmentController::class, 'create'])
            ->name('admin.inventory_adjustments.create')
            ->middleware('module_permission:inventory_adjustments,create');

        Route::post('/dieu-chinh', [InventoryAdjustmentController::class, 'store'])
            ->name('admin.inventory_adjustments.store')
            ->middleware('module_permission:inventory_adjustments,create');



        // ===== Nhật ký tồn kho =====
        Route::get('/nhat-ky-kho', [InventoryLogController::class, 'index'])
            ->name('admin.inventory_logs.index')
            ->middleware('module_permission:inventory_logs,read');

        Route::get('/nhat-ky-kho/{inventoryLog}', [InventoryLogController::class, 'show'])
            ->name('admin.inventory_logs.show')
            ->middleware('module_permission:inventory_logs,read');

    
   

    // ================== BÁN LẺ XE ==================

    // Danh sách HĐ bán lẻ
    Route::get('/ban-le', [ExportReceiptController::class, 'indexRetail'])
        ->name('admin.vehicle_sales.index')
        ->middleware('module_permission:vehicle_sales,read');

    // Form tạo HĐ bán lẻ
    Route::get('/ban-le/tao-moi', [ExportReceiptController::class, 'createRetail'])
        ->name('admin.vehicle_sales.create')
        ->middleware('module_permission:vehicle_sales,create');

    // Lưu HĐ bán lẻ
    Route::post('/ban-le', [ExportReceiptController::class, 'storeRetail'])
        ->name('admin.vehicle_sales.store')
        ->middleware('module_permission:vehicle_sales,create');

    // Xem chi tiết HĐ bán lẻ
    Route::get('/ban-le/{vehicleSale}', [ExportReceiptController::class, 'showRetail'])
        ->name('admin.vehicle_sales.show')
        ->middleware('module_permission:vehicle_sales,read');

    // Form thu nợ / trả góp
    Route::get('/ban-le/{vehicleSale}/chi-tiet', [ExportReceiptController::class, 'createRetailPayment'])
        ->name('admin.vehicle_sales.payments.create')
        ->middleware('module_permission:vehicle_sales,update');

    // Lưu phiếu thu nợ / trả góp
    Route::post('/ban-le/{vehicleSale}/chi-tiet', [ExportReceiptController::class, 'storeRetailPayment'])
        ->name('admin.vehicle_sales.payments.store')
        ->middleware('module_permission:vehicle_sales,update');

    // API bán lẻ
    Route::get('/ban-le/api/find-vehicle', [ExportReceiptController::class, 'findRetailVehicle'])
        ->name('admin.vehicle_sales.find_vehicle')
        ->middleware('module_permission:vehicle_sales,read');

    Route::get('/ban-le/api/find-customer', [ExportReceiptController::class, 'findRetailCustomer'])
        ->name('admin.vehicle_sales.find_customer')
        ->middleware('module_permission:vehicle_sales,read');


    Route::post('/ban-le/update-plate', [ExportReceiptController::class, 'updateRetailPlate'])
    ->name('admin.vehicle_sales.update_plate')
    ->middleware('module_permission:vehicles,update');
    // In hợp đồng bán lẻ

     Route::get('/ban-le/{sale}/print', [ExportReceiptController::class, 'printRetail'])
            ->name('admin.vehicle_sales.print')
            ->middleware('module_permission:vehicle_sales,read');


    }); // END check.login group

}); // END admin prefix group
