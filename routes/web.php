<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\Api\TrackApiController;
use App\Http\Controllers\Admin\ChangelogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rute utama - redirect langsung ke halaman pemilihan tipe pesanan
Route::get('/', function () {
    return redirect()->route('kiosk.order-type');
});

// Rute untuk akses langsung ke localhost:8000
Route::redirect('', '/kiosk/order-type');

// Rute untuk self-ordering kiosk
Route::get('/kiosk/order-type', [KioskController::class, 'orderType'])->name('kiosk.order-type');
Route::post('/kiosk/process-order-type', [KioskController::class, 'processOrderType'])->name('kiosk.process-order-type');
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/cart', [KioskController::class, 'cart'])->name('kiosk.cart');
Route::get('/kiosk/checkout', [KioskController::class, 'checkout'])->name('kiosk.checkout');
Route::post('/kiosk/process-order', [KioskController::class, 'processOrder'])->name('kiosk.process-order');
Route::get('/kiosk/success', [KioskController::class, 'success'])->name('kiosk.success');

// Rute untuk pelacakan pesanan
Route::get('/track/{orderNumber}', [TrackController::class, 'trackOrder'])->name('kiosk.track-order');
Route::get('/track/qrcode/{orderNumber}', [TrackController::class, 'generateTrackingQrCode'])->name('kiosk.tracking-qrcode');

// Membuat route API alternatif untuk akses tracking di luar namespace /api
Route::get('/track-api/{orderNumber}', [TrackApiController::class, 'getOrderStatus'])->name('track.api');

// Rute untuk admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/api/dashboard-data', [AdminController::class, 'getDashboardData'])->name('api.dashboard-data');
    Route::get('/api/activities-data', [AdminController::class, 'getActivitiesData'])->name('api.activities-data');

    // Products
    Route::resource('products', ProductController::class);

    // Product Images
    Route::get('products/images', [ProductImageController::class, 'index'])->name('products.images.index');
    Route::post('products/images', [ProductImageController::class, 'store'])->name('products.images.store');
    Route::delete('products/images/{id}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Orders
    Route::delete('orders/bulk-destroy', [OrderController::class, 'bulkDestroy'])->name('orders.bulk-destroy');
    Route::post('orders/bulk-destroy', [OrderController::class, 'bulkDestroy']);
    Route::get('orders/api/latest', [OrderController::class, 'getLatestOrders'])->name('orders.api.latest');
    Route::get('orders/fix-variants/{id?}', [OrderController::class, 'fixVariants'])->name('orders.fix-variants');
    Route::resource('orders', OrderController::class);

    // Changelog
    Route::get('changelog', [App\Http\Controllers\Admin\ChangelogController::class, 'index'])->name('changelog.index');

    // Site Settings
    Route::get('settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('settings.index');
    Route::get('settings/footer', [App\Http\Controllers\Admin\SiteSettingController::class, 'footer'])->name('settings.footer');
    Route::put('settings/footer', [App\Http\Controllers\Admin\SiteSettingController::class, 'updateFooter'])->name('settings.footer.update');
    Route::get('settings/store', [App\Http\Controllers\Admin\SiteSettingController::class, 'store'])->name('settings.store');
    Route::put('settings/store', [App\Http\Controllers\Admin\SiteSettingController::class, 'updateStore'])->name('settings.store.update');
    Route::get('settings/appearance', [App\Http\Controllers\Admin\SiteSettingController::class, 'appearance'])->name('settings.appearance');
    Route::put('settings/appearance', [App\Http\Controllers\Admin\SiteSettingController::class, 'updateAppearance'])->name('settings.appearance.update');
    Route::get('settings/payment', [App\Http\Controllers\Admin\SiteSettingController::class, 'payment'])->name('settings.payment');
    Route::put('settings/payment', [App\Http\Controllers\Admin\SiteSettingController::class, 'updatePayment'])->name('settings.payment.update');
    Route::get('settings/about', [App\Http\Controllers\Admin\SiteSettingController::class, 'about'])->name('settings.about');
    Route::put('settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'updateSettings'])->name('settings.update');

    // Setup Payment Settings (Temporary route)
    Route::get('settings/payment/setup', [App\Http\Controllers\Admin\SiteSettingController::class, 'setupPaymentSettings'])->name('settings.payment.setup');
});
