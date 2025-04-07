<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provider\AuthController as ProviderAuthcontroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PayPalController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

Route::get('/', [ProfileController::class, 'index'])->name('dashboard.index');


// Authentication routes
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('authregister', [AuthController::class, 'register'])->name('authregister');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/authenticate', [AuthController::class, 'login'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/provider/verify/{id}', [AuthController::class, 'verifyProvider'])->name('provider.verify');
Route::post('/check-email', [AuthController::class, 'checkEmailExists'])->name('checkEmailExists');



// Admin Dashboard
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/dashboard/refill-requests', [AdminDashboardController::class, 'getMonthlyRefillRequests']);
Route::get('/admin/dashboard/filter', [AdminDashboardController::class, 'filterDashboard'])->name('admin.dashboard.filter');


// provider dashboard
Route::get('provider/dashboard', [AdminDashboardController::class, 'index'])->name('provider.dashboard');


//profile
Route::get('/admin/profile', [AdminDashboardController::class, 'editProfile'])->name('admin.profile.edit');
Route::put('/admin/profile/update', [AdminDashboardController::class, 'updateProfile'])->name('admin.profile.update');


// PASSWORD
// Show Forgot Password Form
Route::get('/admin/forgot-password', [AdminDashboardController::class, 'showForgotPasswordForm'])->name('admin.forgot.password');
Route::post('/admin/forgot-password', [AdminDashboardController::class, 'sendResetLink']);
Route::get('/admin/reset-password/{id}', [AdminDashboardController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/admin/reset-password', [AdminDashboardController::class, 'resetPassword'])->name('password.update');

// Password Reset Routes
Route::get('change-password', [AdminDashboardController::class, 'showChangePasswordForm'])->name('change.password.form');
Route::post('change-password', [AdminDashboardController::class, 'changePassword'])->name('change.password');

// Forgot Password Route (Shows form)
Route::get('forgot-password', [AdminDashboardController::class, 'showForgotPasswordForm'])->name('forgot.password.form');
Route::post('forgot-password', [AdminDashboardController::class, 'sendResetLink'])->name('forgot.password');
Route::get('reset-password/{token}', [AdminDashboardController::class, 'showResetPasswordForm'])->name('reset.password.form');
Route::post('reset-password', [AdminDashboardController::class, 'resetPassword'])->name('reset.password');



//SERVICES

Route::get('/services', [AdminDashboardController::class, 'services'])->name('services')->middleware('permission:services');
Route::get('/services/data', [AdminDashboardController::class, 'getData'])->name('services.data');
// Route::get('/services/update', [AdminDashboardController::class, 'getData'])->name('services.update')->middleware('permission:edit_services');
Route::get('/services/{id}/edit', [AdminDashboardController::class, 'editServices'])->name('services.edit')->middleware('permission:edit services');

Route::post('/services/update-status', [AdminDashboardController::class, 'updateStatus'])->name('services.updateStatus');
Route::post('/services/update', [AdminDashboardController::class, 'updateservice'])->name('services.update');


Route::get('services/create', [AdminDashboardController::class, 'create'])->name('services.create')->middleware('permission:create services');
Route::post('services', [AdminDashboardController::class, 'store'])->name('services.store');
// Route::get('services/{service}', [AdminDashboardController::class, 'show'])->name('services.show')->middleware('permission:view_services');
Route::delete('/services/delete/{id}', [AdminDashboardController::class, 'destroy'])->name('services.destroy')->middleware('permission:delete services');

// Service- request

Route::get('/admin/service-requests', [AdminDashboardController::class, 'serviceAdmin'])->name('admin.service-requests')->middleware('permission:show service resquest');
Route::get('/admin/service-requests/data', [AdminDashboardController::class, 'getServiceData'])->name('admin.service-requests.data');
Route::post('/admin/service-requests/accept', [AdminDashboardController::class, 'acceptServiceRequest'])->name('admin.service-requests.accept')->middleware('permission:accept service request');
Route::get('/admin/service-requests/accepted', [AdminDashboardController::class, 'getAcceptedServiceData'])->name('admin.service-requests.accepted')->middleware('permission:accept service request list');
Route::post('/admin/service-requests/delete-accepted', [AdminDashboardController::class, 'deleteAcceptedServiceRequest'])->name('admin.service-requests.delete-accepted')->middleware('permission:delete service request');
Route::post('/admin/service-requests/delete', [AdminDashboardController::class, 'deleteServiceRequest'])->name('admin.service-requests.delete')->middleware('permission:delete accepted service');;


Route::post('/admin/service-requests/accept/email', [AdminDashboardController::class, 'sendAcceptEmail'])->name('admin.service-requests.accept.email');

//user-list
Route::get('/users', [AdminDashboardController::class, 'getUsers'])->name('admin.users.index')->middleware('permission:user list');
Route::get('/users/{id}/edit', [AdminDashboardController::class, 'editUser'])->name('admin.users.edit')->middleware('permission:edit user');
Route::put('/users/{id}/update', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
Route::delete('/users/{id}/delete', [AdminDashboardController::class, 'destroyUser'])->name('admin.users.destroy')->middleware('permission:delete user');
Route::post('/users/{id}/restore', [AdminDashboardController::class, 'restoreUser'])->name('users.restore')->middleware('permission:restore user');
Route::post('/admin/users/{id}/update-email-verification', [AdminDashboardController::class, 'updateEmailVerification']);






// PRODUCTS: ACCESSORIES with Permissions
Route::get('accessories', [AdminDashboardController::class, 'accessory'])
    ->name('accessories.index')
    ->middleware('permission:view accessories');


Route::get('accessories/data', [AdminDashboardController::class, 'getAccessoriesData'])
    ->name('accessories.data');


Route::get('/accessories/create', [AdminDashboardController::class, 'createAccessory'])
    ->name('accessories.create')
    ->middleware('permission:create accessories');

Route::post('/accessories/store', [AdminDashboardController::class, 'storeAccessory'])
    ->name('accessories.store');

Route::get('/accessories/{id}/edit', [AdminDashboardController::class, 'editAccessory'])
    ->name('accessories.edit')
    ->middleware('permission:edit accessories');

Route::put('/accessories/{id}', [AdminDashboardController::class, 'updateAccessory'])
    ->name('accessories.update');

Route::delete('/accessories/{id}', [AdminDashboardController::class, 'destroyAccessory'])
    ->name('accessories.destroy')
    ->middleware('permission:delete accessories');


// PRODUCT: FIRE_EXTINGUISHER
Route::get('/fire-extinguishers', [AdminDashboardController::class, 'fireExtinguisher'])->name('fire_extinguishers.index')->middleware('permission:view fire extinguisher');
Route::get('/fire-extinguishers/data', [AdminDashboardController::class, 'getFireExtinguisherData'])->name('fire_extinguishers.data');
Route::get('/fire-extinguishers/create', [AdminDashboardController::class, 'createFireExtinguisher'])->name('fire_extinguishers.create')->middleware('permission:create extinguisher');
Route::post('/fire-extinguishers/store', [AdminDashboardController::class, 'storeFireExtinguisher'])->name('fire_extinguishers.store');
Route::get('/fire-extinguishers/{id}/edit', [AdminDashboardController::class, 'editFireExtinguisher'])->name('fire_extinguishers.edit')->middleware('permission:edit extinguisher');
Route::put('/fire-extinguishers/{id}', [AdminDashboardController::class, 'updateFireExtinguisher'])->name('fire_extinguishers.update');
Route::delete('/fire-extinguishers/{id}', [AdminDashboardController::class, 'destroyFireExtinguisher'])->name('fire_extinguishers.destroy')->middleware('permission:delete extinguisher');

// PRODUCT: Fire_SUPRRESION_SYSTEM
Route::get('/fire-suppression', [AdminDashboardController::class, 'fireSuppression'])->name('fire_suppression.index')->middleware('permission:view suppression');
Route::get('/fire-suppression/data', [AdminDashboardController::class, 'getfireSuppressionData'])->name('fire_suppression.data');
Route::get('/fire-suppression/create', [AdminDashboardController::class, 'createfireSuppression'])->name('fire_suppression.create')->middleware('permission:create suppression');
Route::post('/fire-suppression/store', [AdminDashboardController::class, 'storefireSuppression'])->name('fire_suppression.store');
Route::get('/fire-suppression/{id}/edit', [AdminDashboardController::class, 'editfireSuppression'])->name('fire_suppression.edit')->middleware('permission:edit suppression');
Route::put('/fire-suppression/{id}', [AdminDashboardController::class, 'updatefireSuppression'])->name('fire_suppression.update');
Route::delete('/fire-suppression/{id}', [AdminDashboardController::class, 'destroyfireSuppression'])->name('fire_suppression.destroy')->middleware('permission:delete suppression');

// PRODUCT: Fire_WATERMIST_SYSTEM
Route::get('/fire-watermist', [AdminDashboardController::class, 'fireWatermist'])->name('fire_watermist.index')->middleware('permission:view watermist');
Route::get('/fire-watermist/data', [AdminDashboardController::class, 'getfireWatermistData'])->name('fire_watermist.data');
Route::get('/fire-watermist/create', [AdminDashboardController::class, 'createfireWatermist'])->name('fire_watermist.create')->middleware('permission:create watermist');
Route::post('/fire-watermist/store', [AdminDashboardController::class, 'storefireWatermist'])->name('fire_watermist.store');
Route::get('/fire-watermist/{id}/edit', [AdminDashboardController::class, 'editfireWatermist'])->name('fire_watermist.edit')->middleware('permission:edit watermist');
Route::put('/fire-watermist/{id}', [AdminDashboardController::class, 'updatefireWatermist'])->name('fire_watermist.update');
Route::delete('/fire-watermist/{id}', [AdminDashboardController::class, 'destroyfireWatermist'])->name('fire_watermist.destroy')->middleware('permission:delete watermist');


//Booking
Route::get('/admin/bookings', [AdminDashboardController::class, 'indexBooking'])->name('admin.bookingShow')->middleware('permission:view order');
Route::get('/admin/bookings/data', [AdminDashboardController::class, 'getBookingsData'])->name('admin.getBookingsData');
Route::get('/admin/get-order-status/{id}', [AdminDashboardController::class, 'getOrderStatus']);
Route::post('/admin/update-tracking-status', [AdminDashboardController::class, 'updateTrackingStatus']);
Route::post('/admin/update-arrival-date', [AdminDashboardController::class, 'updateArrivalDate'])->name('admin.updateArrivalDate');
Route::post('/admin/delete-booking', [AdminDashboardController::class, 'deleteBooking'])->name('admin.deleteBooking');


Route::get('/admin/order', [AdminDashboardController::class, 'deliveredData'])->name('admin.OrderShow')->middleware('permission:view delivered order');;
Route::get('/admin/delivered/order', [AdminDashboardController::class, 'deliveredOrder'])->name('admin.deliveredOrder');



// COUPONS
Route::get('/admin/coupons', [AdminDashboardController::class, 'indexCoupon'])->name('admin.coupons.index')->middleware('permission:view coupon');
// Route::get('coupons', [AdminDashboardController::class, 'indexCoupo'])->name('coupons.index');
Route::get('coupons/{id}/edit', [AdminDashboardController::class, 'editCoupon'])->name('coupons.edit');
Route::post('coupons/update', [AdminDashboardController::class, 'updateCoupon'])->name('coupons.update');
Route::delete('coupons/{id}', [AdminDashboardController::class, 'deleteCoupon'])->name('coupons.delete');


Route::get('/coupons/create', [AdminDashboardController::class, 'createCoupon'])->name('admin.coupons.create')->middleware('permission:create coupon');
Route::post('/coupons/store', [AdminDashboardController::class, 'storeCoupon'])->name('admin.coupons.store');
Route::get('/admin/get-products', [AdminDashboardController::class, 'getProductsCoupon'])->name('admin.getProducts');
Route::post('/coupons/delete-expired', [AdminDashboardController::class, 'deleteExpired'])->name('admin.coupons.deleteExpired');


// AJAX Routes for Dynamic Dropdowns
Route::get('/get-categories/{provider_id}', [AdminDashboardController::class, 'getCategories'])->name('admin.get.categories');

// Provider Routes
Route::get('provider/register', [ProviderAuthController::class, 'showRegisterForm'])->name('provider.register');
Route::post('providerauthregister', [ProviderAuthController::class, 'register'])->name('provider.authregister');
Route::get('provider/login', [ProviderAuthController::class, 'showLogin'])->name('provider.login');
Route::post('provider/authenticate', [ProviderAuthController::class, 'login'])->name('provider.authenticate');


//service-provider request-list
Route::get('/service-providers', [AdminDashboardController::class, 'showRequestList'])->name('admin.providers')->middleware('permission:provider request');
Route::post('service-providers/update-email-verified', [AdminDashboardController::class, 'updateEmailVerified'])->name('admin.providers.updateEmailVerified');
Route::post('/service-providers/update-status', [AdminDashboardController::class, 'updateRequestStatus'])->name('admin.providers.updateStatus');

//all service provider list 
Route::get('/providers', [AdminDashboardController::class, 'getProviders'])->name('admin.providers.index')->middleware('permission:view providers');
Route::get('/providers/{id}/edit', [AdminDashboardController::class, 'editProvider'])->name('admin.providers.edit')->middleware('permission:edit service provider');
Route::put('/providers/{id}/update', [AdminDashboardController::class, 'updateProvider'])->name('admin.providers.update');
Route::delete('/providers/{id}/delete', [AdminDashboardController::class, 'destroyProvider'])->name('admin.providers.destroy');
Route::post('/providers/{id}/restore', [AdminDashboardController::class, 'restore'])->name('providers.restore');
Route::get('/admin/providers/rejected', [AdminDashboardController::class, 'rejectedProviders'])->name('admin.providers.rejected')->middleware('permission:rejected provider');


//pdf download

Route::get('/providers/download-pdf', [AdminDashboardController::class, 'downloadPDF'])->name('providers.export-pdf');
Route::get('/users/download-pdf', [AdminDashboardController::class, 'userDownloadPDF'])->name('users.export-pdf');

// Role & Permission
Route::get('/admin/roles-permissions', [AdminDashboardController::class, 'indexPermission'])->name('roles.permissions');
Route::post('/admin/roles-permissions/update', [AdminDashboardController::class, 'updatePermission'])->name('roles.permissions.update');







//USER Interface

//service page

Route::get('/user-services', [ProfileController::class, 'indexService'])->name('user.services');
Route::get('/get-services', [ProfileController::class, 'getServices'])->name('get.services');
Route::get('/get-sub-services/{service_name}', [ProfileController::class, 'getSubServices'])->name('subservices.get');
Route::get('/get-providers/{sub_service_name}', [ProfileController::class, 'getProviders'])->name('get.providers');
Route::post('/service-requests', [ProfileController::class, 'storeServiceRequest'])->name('service-requests.store');



//about page
Route::get('/about', [ProfileController::class, 'indexAbout'])->name('about');

//profile page
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('user.profile.update');

//notification
Route::get('/notifications/clear', function () {
    Auth::user()->notifications()->delete();
    return back();
})->name('notifications.clear');

Route::post('/notifications/mark-as-read/{id}', function ($id) {
    $notification = Auth::user()->unreadNotifications->find($id);
    if ($notification) {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }
    return response()->json(['status' => 'error'], 404);
})->name('notifications.mark-as-read');



// contact
Route::get('/contact', [ProfileController::class, 'indexContact'])->name('contact');
Route::post('/messages/store', [ProfileController::class, 'storeMessage'])->name('messages.store');
Route::post('/notifications/mark-as-read/{id}', [ProfileController::class, 'markAsRead']);


// PRODUCT
Route::get('/product', [ProfileController::class, 'indexProduct'])->name('product');
Route::get('/filter-products', [ProfileController::class, 'filterProducts'])->name('filter.products');
Route::get('/fetch-product-details', [ProfileController::class, 'fetchProductDetails']);




// CART
Route::get('/cart', [ProfileController::class, 'viewCart'])->name('cart.view');
Route::post('/cart/add', [ProfileController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [ProfileController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update', [ProfileController::class, 'updateCart'])->name('cart.update');
Route::post('/remove-coupon', [ProfileController::class, 'removeCoupon'])->name('remove.coupon');
Route::post('/apply-coupon', [ProfileController::class, 'applyCoupon'])->name('apply.coupon');
Route::get('/cart/get-total', [ProfileController::class, 'getTotal'])->name('cart.get-total');
Route::get('/product/stock/check', [ProfileController::class, 'checkStock'])->name('product.stock.check');
Route::get('/cart/check-stock', [ProfileController::class, 'checkCartStock'])->name('cart.checkStock');

// checkout
Route::post('/checkout/store', [ProfileController::class, 'storeCheckout'])->name('checkout.store');
Route::get('/checkout', [ProfileController::class, 'showCheckout'])->name('checkout');
Route::get('/order-history', [ProfileController::class, 'orderHistory'])->name('order.history');
Route::post('/checkout', [ProfileController::class, 'store'])->name('checkout.order.store');
Route::get('/checkout/track/{orderId}', [ProfileController::class, 'trackOrder'])->name('checkout.track');


Route::get('/dashboard/paypal/{checkout}', [ProfileController::class, 'paypalView'])->name('dashboard.paypal.pay');
Route::get('/paypal/payment/{checkout_id}', [PayPalController::class, 'payment'])->name('dashboard.paypal');
Route::get('/paypal/success/{checkout_id}', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel/{checkout_id}', [PayPalController::class, 'cancel'])->name('paypal.cancel');

// cancellation order : cod

Route::post('/order/cancel', [ProfileController::class, 'cancelOrder'])->name('order.cancel');
Route::get('/invoice/download/{id}', function ($id) {
    $checkout = DB::table('checkout')->where('id', $id)->first();
    $items = DB::table('checkout_items')->where('checkout_id', $id)->get();

    $grandTotal = 0;

    foreach ($items as $item) {
        $table = '';
        if ($item->category_id == 1) $table = 'accessories';
        elseif ($item->category_id == 2) $table = 'fire_extinguishers';
        elseif ($item->category_id == 3) $table = 'fire_suppression_systems';
        elseif ($item->category_id == 4) $table = 'watermist_systems';

        $product = DB::table($table)->where('id', $item->product_id)->first();
        $item->product_name = $product->name ?? 'N/A';

        // Safe calculations
        $item->unit_price = $item->quantity > 0
            ? ($item->final_price ?? ($item->price * $item->quantity)) / $item->quantity
            : $item->price;

        $item->total = $item->final_price ?? ($item->price * $item->quantity);
        $grandTotal += $item->total;
    }

    $pdf = Pdf::loadView('invoice.pdf', compact('checkout', 'items', 'grandTotal'));
    return $pdf->download('invoice_' . $checkout->id . '.pdf');
})->name('invoice.download');
