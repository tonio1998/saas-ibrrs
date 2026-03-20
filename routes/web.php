<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BusinessInfoController;
use App\Http\Controllers\CertificateRequestController;
use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\CertificateTypesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PuroksController;
use App\Http\Controllers\ResidentInfoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\ResidentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended('/dashboard');
    }

    return view('auth.login');
});

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register']);

Route::post('/logout',[AuthController::class,'logout'])->name('logout');
Route::middleware('auth')->group(function(){

    Route::prefix('dashboard')->name('dashboard.')->group(function() {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/data', [DashboardController::class, 'data'])->name('data');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('data', [UserController::class, 'users_data'])->name('data');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/generate-password/{type}/{typeId}/{userId}',[UserController::class,'generatePassword'])->name('password');
        Route::get('/{user}/roles', [UserController::class,'editRoles'])->name('roles');
        Route::get('/{user}/permissions', [UserController::class,'editPermissions'])->name('permissions');
        Route::put('/{user}/roles',[UserController::class,'updateRoles'])->name('roles.update');
        Route::put('/{user}/permissions',[UserController::class,'updatePermissions'])->name('permissions.update');
    });

    Route::prefix('households')->name('households.')->group(function(){
        Route::get('/index', [HouseholdsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [HouseholdsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [HouseholdsController::class, 'update'])->name('update');
        Route::get('/create', [HouseholdsController::class, 'create'])->name('create');
        Route::post('/create', [HouseholdsController::class, 'store'])->name('store');
        Route::get('/data', [HouseholdsController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('puroks')->name('puroks.')->group(function(){
        Route::get('/index', [PuroksController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [PuroksController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PuroksController::class, 'update'])->name('update');
        Route::get('/create', [PuroksController::class, 'create'])->name('create');
        Route::post('/create', [PuroksController::class, 'store'])->name('store');
        Route::get('/data', [PuroksController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('residents')->name('residents.')->group(function(){
        Route::get('/index', [ResidentsController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [ResidentsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ResidentsController::class, 'update'])->name('update');
        Route::get('/create', [ResidentsController::class, 'create'])->name('create');
        Route::post('/create', [ResidentsController::class, 'store'])->name('store');
        Route::get('/data', [ResidentsController::class, 'ajaxData'])->name('data');
        Route::get('show/{id}', [ResidentsController::class, 'show'])->name('show');
    });

    Route::prefix('resident-info')->name('resident-info.')->group(function () {
        Route::post('/store', [ResidentInfoController::class, 'store'])->name('store');
        Route::put('/update/{id}', [ResidentInfoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ResidentInfoController::class, 'destroy'])->name('delete');
    });

    Route::prefix('business')->group(function () {
        Route::post('/store', [BusinessInfoController::class, 'store'])->name('business.store');
        Route::put('/update/{id}', [BusinessInfoController::class, 'update'])->name('business.update');
        Route::delete('/delete/{id}', [BusinessInfoController::class, 'destroy'])->name('business.delete');
    });

    Route::prefix('permissions')->name('permissions.')->group(function(){
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/create', [PermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        Route::get('/data', [PermissionController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('roles')->name('roles.')->group(function(){
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/create', [RoleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('update');
        Route::get('/data', [RoleController::class, 'ajaxData'])->name('data');
    });

    Route::prefix('certificates_request')->name('certificates_request.')->group(function(){
        Route::get('/index', [CertificateRequestController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [CertificateRequestController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [CertificateRequestController::class, 'show'])->name('show');
        Route::put('/update/{id}', [CertificateRequestController::class, 'update'])->name('update');
        Route::get('/create', [CertificateRequestController::class, 'create'])->name('create');
        Route::post('/create', [CertificateRequestController::class, 'store'])->name('store');
        Route::get('/data', [CertificateRequestController::class, 'ajaxData'])->name('data');
        Route::post('/approve', [CertificateRequestController::class, 'approve'])->name('approve');
        Route::post('/reject', [CertificateRequestController::class, 'reject'])->name('reject');
        Route::post('/issue', [CertificateRequestController::class, 'issue'])->name('issue');
    });

    Route::prefix('certificate-types')->name('certificate-types.')->group(function(){
        Route::get('/index', [CertificateTypesController::class, 'index'])->name('index');
        Route::get('/create', [CertificateTypesController::class, 'create'])->name('create');
        Route::post('/create', [CertificateTypesController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CertificateTypesController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CertificateTypesController::class, 'update'])->name('update');
        Route::get('/data', [CertificateTypesController::class, 'ajaxData'])->name('data');
        Route::delete('/destroy/{id}', [CertificateTypesController::class, 'destroy'])->name('destroy');
        Route::get('/print/{control_no}', [CertificateRequestController::class,'print'])->name('print');
    });

    Route::get('/reports', fn() => view('pages.reports.index'));
    Route::get('/settings', fn() => view('pages.settings.index'));

    Route::prefix('select2')->name('select2.')->group(function(){
        Route::get('roles/search',[RoleController::class,'search'])->name('roles');
        Route::get('users/search',[UserController::class,'users_search'])->name('users');
        Route::get('households/search',[HouseholdsController::class,'households_search'])->name('households');
        Route::get('puroks/search',[PuroksController::class,'puroks_search'])->name('puroks');
        Route::get('residents/search',[ResidentsController::class,'residents_search'])->name('residents');
        Route::get('certificate-requests/search',[CertificateRequestController::class,'certificate_requests_search'])->name('certificate-requests');
        Route::get('certificate-types/search',[CertificateTypesController::class,'certificate_types_search'])->name('certificate-types');
        Route::get('businesses/search',[BusinessInfoController::class,'businesses_search'])->name('businesses');

        Route::prefix('address')->group(function () {
            Route::get('/full', [AddressController::class, 'full'])->name('full');
            Route::get('/regions', [AddressController::class, 'regions'])->name('x_selectRegions');
            Route::get('/provinces', [AddressController::class, 'provinces'])->name('x_selectRegionProvinces');
            Route::get('/cities', [AddressController::class, 'cities'])->name('x_selectProvinceCityMuns');
            Route::get('/barangays', [AddressController::class, 'barangays'])->name('x_selectCityMunBarangays');
        });
    });


});

Route::get('/verify/{control_no}', [CertificateRequestController::class,'verify'])->name('cert.verify');
