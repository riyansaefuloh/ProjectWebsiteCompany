<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\InquiryExportController;
use App\Http\Controllers\Public\DownloadController;
use App\Livewire\Public\InquiryForm;
use App\Livewire\Admin\ProductIndex;
use App\Livewire\Admin\CertificationIndex;
use App\Livewire\Admin\CategoryIndex;
use App\Livewire\Admin\NewsIndex;
use App\Livewire\Admin\InquiryIndex;
use App\Livewire\Admin\DownloadIndex;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Admin\ExportMarketIndex;
use App\Livewire\Admin\SettingIndex;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// ==========================================
// 1. RUTE PUBLIK MULTIBAHASA (ID & EN)
// ==========================================
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    
    Route::get('/', function () {
        return "Halaman Beranda Publik (" . app()->getLocale() . ")";
    })->name('home');

    Route::get('/products', function () {
        return "Halaman Katalog Produk (" . app()->getLocale() . ")";
    })->name('products.index');

    Route::get('/products/{slug}', function ($slug) {
        return "Detail Produk Slug: {$slug} (" . app()->getLocale() . ")";
    })->name('products.show');

    Route::get('/about', function () {
        return "Halaman About Us (" . app()->getLocale() . ")";
    })->name('about');

    Route::get('/inquiry', InquiryForm::class)->name('inquiry.index');
    Route::get('/download/catalog-pdf', [DownloadController::class, 'downloadCatalog'])->name('download.catalog');
    Route::get('/download/file/{download}', [DownloadController::class, 'downloadFile'])->name('download.file');
});

// ==========================================
// 2. RUTE AUTHENTICATION (LOGIN & LOGOUT)
// ==========================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==========================================
// 3. RUTE ADMIN (CMS LIVEWIRE) TERPROTEKSI AUTH & PERMISSION
// ==========================================
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Admin Dashboard Main Menu (Menggunakan View Blade & Directive @can Spatie)
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Livewire Admin Products (Super Admin & Admin CMS)
    Route::get('/products', ProductIndex::class)
        ->middleware('permission:manage products')
        ->name('admin.products.index');

    // Livewire Admin Categories (Super Admin & Admin CMS)
    Route::get('/categories', CategoryIndex::class)
        ->middleware('permission:manage products')
        ->name('admin.categories.index');

    // Livewire Admin Certification (Super Admin & Admin CMS)
    Route::get('/certifications', CertificationIndex::class)
        ->middleware('permission:manage certifications')
        ->name('admin.certifications.index');

    // Livewire Admin Export Markets (Super Admin & Admin CMS)
    Route::get('/export-markets', ExportMarketIndex::class)
        ->middleware('permission:manage export markets')
        ->name('admin.export-markets.index');

    // Livewire Admin News (Super Admin & Admin CMS)
    Route::get('/news', NewsIndex::class)
        ->middleware('permission:manage news')
        ->name('admin.news.index');

    // Livewire Admin Inquiries (Super Admin & Sales)
    Route::get('/inquiries', InquiryIndex::class)
        ->middleware('permission:view inquiries')
        ->name('admin.inquiries.index');

    // Export Inquiries to CSV (Super Admin & Sales)
    Route::get('/inquiries/export', [InquiryExportController::class, 'export'])
        ->middleware('permission:export inquiries')
        ->name('admin.inquiries.export');

    // Livewire Admin Downloads (Super Admin & Admin CMS)
    Route::get('/downloads', DownloadIndex::class)
        ->middleware('permission:manage downloads')
        ->name('admin.downloads.index');

    // Livewire Admin Settings (Super Admin)
    Route::get('/settings', SettingIndex::class)
        ->middleware('permission:manage global settings')
        ->name('admin.settings.index');

    // Livewire Admin Users (Super Admin)
    Route::get('/users', UserIndex::class)
        ->middleware('permission:manage users')
        ->name('admin.users.index');
});
