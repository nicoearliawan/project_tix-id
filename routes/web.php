<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;



Route::middleware('isGuest')->group(function(){
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/signup', function () {
        return view('auth.signup');
    })->name('signup');
    Route::post('/signup', [UserController::class,'register'])->name('signup.send_data');
});

Route::get('/', function () {
    return view('home');
})->name('home');


Route::middleware('isUser')->group(function() {
    Route::get('/schedules/{scheduleId}/hours/{hourId}', [ScheduleController::class, 'showSeats'])->name('schedules.show_seats');

    Route::prefix('/tickets')->name('tickets.')->group(function() {
        Route::get('/', [TicketController::class, 'index'])->name('index');

        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticketId}/order', [TicketController::class, 'orderPage'])->name('order');
        Route::post('/qrcode', [TicketController::class, 'createQrcode'])->name('qrcode');
        Route::get('/{ticket_Id}/payment', [TicketController::class, 'paymentPage'])->name('payment');
        Route::patch('/{ticketId}/payment/status' , [TicketController::class, 'updateStatusPayment'])->name('payment.status');
        Route::get('/{ticketId}/payment/proof', [TicketController::class, 'proofPayment'])->name('payment.proof');
        Route::get(('/{ticketId}/pdf'), [TicketController::class, 'exportPdf'])->name('export_pdf');

    });
});

Route::get('/schedules/detail/{movie_id}', [MovieController::class, 'movieSchedule'])->name('schedules.detail');
// menu bioskop pada navbar user
Route::get('/cinemas/list', [CinemaController::class, 'cinemaList'])->name('cinemas.list');
Route::get('cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');



// Mengapa UserController? karna akan mengisi tabel user
Route::post('/auth', [UserController::class,'authentication'])->name('auth');
Route::get('/logout', [UserController::class,'logout'])->name('logout');
// method route di laravel
// get = menampilkan halaman
// post = menambahkan data baru
// patch/put = mengubah
// delete = menghapus


// untuk halaman admin
// membuat group route dengan middleware is Admin, route2 yang hanya diakses oleh admin
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function(){

    Route::get('/tickets/chart', [TicketController::class, 'chart'])->name('tickets.chart');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // bioskop
    Route::prefix('/cinemas')->controller(CinemaController::class)->name('cinemas.')->group(function(){
        Route::get('/index', 'index')->name('index');
        // admin.cinemas.index
        // prefix() : membuat path awalan, /admin ditulis 1x bisa dipake berkali kali
        // name() : membuat nama route awalan, admin. ditulis 1x bisa
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        // parameter placeholder -> {id} : mencari data spesifik
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'destroy')->name('delete');
        Route::get('/export', 'export')->name('export');
        Route::get('trash', 'trash')->name('trash');
        Route::patch('/restore/{id}', 'restore')->name('restore');
        Route::delete('/delete-permanent/{id}', 'deletePermanent')->name('delete_permanent');
        Route::get('/datatables', 'datatables')->name('datatables');
    });

    // user
    Route::prefix('/users')->name('users.')->group(function(){
        Route::get('/index', [UserController::class, 'index'])->name('index');
        // admin.users.index
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [UserController::class, 'datatables'])->name('datatables');
    });

    // movie
    Route::prefix('/movies')->name('movies.')->group(function(){
        Route::get('/chart', [MovieController::class, 'chart'])->name('chart');

        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::patch('/patch/{id}', [MovieController::class, 'patch'])->name('patch');
        Route::get('/export', [MovieController::class, 'export'])->name('export');
        Route::get('trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [MovieController::class, 'datatables'])->name('datatables');
    });
});

// Beranda
Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.all');


// Staff
Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function() {

    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    // Promo
    Route::prefix('/promos')->name('promos.')->group(function() {
        Route::get('/index', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        Route::get('/export', [PromoController::class, 'export'])->name('export');
        Route::get('trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [PromoController::class, 'datatables'])->name('datatables');
    });

    // Jadwal Tayang
    Route::prefix('/schedules')->name('schedules.')->group(function() {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        Route::get('/export', [ScheduleController::class, 'export'])->name('export');
        Route::get('trash', [ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [ScheduleController::class, 'datatables'])->name('datatables');
    });
});


