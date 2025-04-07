<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);


// Public Routes
Route::get('/services', [ApiController::class, 'getServices']);
Route::get('/sub-services/{service_name}', [ApiController::class, 'getSubServices']);
Route::get('/providers/{sub_service_name}', [ApiController::class, 'getProviders']);

Route::get('/products', [ApiController::class, 'indexProduct']);


Route::middleware('auth:sanctum')->group(function () {
    // service-request
    Route::get('/service-requests', [ApiController::class, 'getServiceRequests']);
    Route::post('/service-requests/accept', [ApiController::class, 'acceptServiceRequest']);
    Route::post('/service-requests/delete', [ApiController::class, 'deleteServiceRequest']);

    // accept/reject service providers
    Route::post('/service-providers/update-status', [ApiController::class, 'updateRequestStatus']);

    // order list
    Route::get('/bookings', [ApiController::class, 'getBookingsData']);

    // order updates
    Route::post('/bookings/update-arrival', [ApiController::class, 'updateArrivalDate']);
    Route::post('/bookings/update-tracking', [ApiController::class, 'updateTrackingStatus']);
    Route::delete('/bookings/delete', [ApiController::class, 'deleteBooking']);

    // Accessories
    Route::get('/accessories/{id}/edit', [ApiController::class, 'editAccessory']);
    Route::post('/accessories/{id}/update', [ApiController::class, 'updateAccessory']);
    Route::delete('/accessories/{id}/delete', [ApiController::class, 'destroyAccessory']);

    // Fire-Extinguisher
    Route::get('/fire-extinguishers/{id}', [ApiController::class, 'editFireExtinguisher']);
    Route::put('/fire-extinguishers/{id}', [ApiController::class, 'updateFireExtinguisher']);
    Route::delete('/fire-extinguishers/{id}', [ApiController::class, 'destroyFireExtinguisher']);

    // Fire-suppression
    Route::get('/fire-suppression/{id}', [ApiController::class, 'editFireSuppression']);
    Route::post('/fire-suppression/update/{id}', [ApiController::class, 'updateFireSuppression']);
    Route::delete('/fire-suppression/delete/{id}', [ApiController::class, 'destroyFireSuppression']);

    // fire-watermist
    Route::get('/fire-watermist/{id}', [ApiController::class, 'editFireWatermist']);
    Route::put('/fire-watermist/{id}', [ApiController::class, 'updateFireWatermist']);
    Route::delete('/fire-watermist/{id}', [ApiController::class, 'destroyFireWatermist']);


    // USER API ROUTES
    // request for service  
    Route::post('/service-request', [ApiController::class, 'storeServiceRequest']);
    //  order 
    Route::get('/order-history', [ApiController::class, 'orderHistory']);
    Route::post('/cancel-order', [ApiController::class, 'cancelOrder']);
});
