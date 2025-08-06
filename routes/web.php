<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/pricing', [FrontController::class, 'pricing'])->name('front.pricing');

// untuk midtrans payment notification
Route::match(
    ['get', 'post'],
    '/booking/payment/midtrans/notification',
    [FrontController::class, 'paymentMidtransNotification']
)
    ->name('front.payment_midtrans.notification');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:student')->group(function () {
        Route::get('/dashboard/subscriptions/', [DashboardController::class, 'subscriptions'])->name('dashboard.subscriptions');

        Route::get('/dashboard/subscriptions/{transaction}', [DashboardController::class, 'subscription_details'])->name('dashboard.subscription.details');

        Route::get('/dashboard/courses/', [CourseController::class, 'index'])->name('dashboard');

        Route::get('/dashboard/course/{course:slug}', [CourseController::class, 'details'])->name('dashboard.course.details');

        Route::get('/dashboard/search/courses', [CourseController::class, 'search_course'])->name('dashboard.search.courses');

        Route::middleware(['check.subscription'])->group(function () {
            Route::get('/dashboard/join/{course:slug}', [CourseController::class, 'join'])->name('dashboard.course.join');

            Route::get('/dashboard/learning/{course:slug}/{courseSection}/{sectionContent}', [CourseController::class, 'learning'])->name('dashboard.course.learning');

            Route::get('/dashboard/learning/{course:slug}/finished', [CourseController::class, 'learning_finished'])->name('dashboard.course.learning.finished');
        });

        // diluar middleware check.subscription tapi didalam role student
        // walaupun harus mempunyai role stdent tapi tetep dimasukkan ke FrontController karena ini memang kan cuman buat checkout dan pembayaran
        Route::get('/checkout/success', [FrontController::class, 'checkout_success'])->name('front.checkout.success');
        Route::get('/checkout/{pricing}', [FrontController::class, 'checkout'])->name('front.checkout');
        Route::post('/booking/payment/midtrans', [FrontController::class, 'paymentStoreMidtrans'])->name('front.payment_store_midtrans');
    });
});

require __DIR__ . '/auth.php';
