<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\InquiryExportController;
use App\Http\Controllers\Admin\DashboardController;
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
use App\Livewire\Public\Home;
use App\Livewire\Public\ProductIndex as PublicProductIndex;
use App\Livewire\Public\ProductShow;
use App\Livewire\Public\NewsIndex as PublicNewsIndex;
use App\Livewire\Public\NewsShow;
use App\Livewire\Public\ExportMarketIndex as PublicExportMarketIndex;
use App\Livewire\Public\GalleryIndex as PublicGalleryIndex;
use App\Livewire\Public\CertificationIndex as PublicCertificationIndex;
use App\Livewire\Public\DownloadIndex as PublicDownloadIndex;
use App\Livewire\Public\About;
use App\Livewire\Public\PageShow;
use App\Livewire\Admin\PageIndex;
use App\Livewire\Admin\GalleryIndex;
use App\Livewire\Admin\NewsTaxonomyIndex;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// ==========================================
// 1. RUTE PUBLIK MULTIBAHASA (ID & EN)
// ==========================================
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    
    Route::get('/', Home::class)->name('home');
    Route::get('/products', PublicProductIndex::class)->name('products.index');
    Route::get('/products/{slug}', ProductShow::class)->name('products.show');
    Route::get('/news', PublicNewsIndex::class)->name('news.index');
    Route::get('/news/{slug}', NewsShow::class)->name('news.show');
    Route::get('/export-markets', PublicExportMarketIndex::class)->name('export-markets.index');
    Route::get('/certifications', PublicCertificationIndex::class)->name('certifications.index');
    Route::get('/gallery', PublicGalleryIndex::class)->name('gallery.index');
    Route::get('/downloads', PublicDownloadIndex::class)->name('downloads.index');
    Route::get('/about', About::class)->name('about');
    Route::get('/page/{slug}', PageShow::class)->name('page.show');

    Route::get('/inquiry', InquiryForm::class)->name('inquiry.index');

    // ── Katalog PDF dinamis ──────────────────────────────────────────────
    Route::get('/download/catalog-pdf', [DownloadController::class, 'showCatalogForm'])->name('download.catalog.form');
    Route::post('/download/catalog-pdf', [DownloadController::class, 'downloadCatalog'])->name('download.catalog');
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
// SetPanelLocale WAJIB ikut: tanpa itu panel berjalan dalam APP_LOCALE ('en')
// dan seluruh pesan validasinya muncul berbahasa Inggris di layar Indonesia.
Route::middleware(['auth', \App\Http\Middleware\SetPanelLocale::class])->prefix('admin')->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

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

    // Kategori & tag berita. Izinnya sama dengan Berita: yang boleh menulis
    // artikel adalah yang boleh mengatur penggolongannya.
    Route::get('/news-taxonomy', NewsTaxonomyIndex::class)
        ->middleware('permission:manage news')
        ->name('admin.news-taxonomy.index');

    // Livewire Admin Pages (Super Admin & Admin CMS)
    Route::get('/pages', PageIndex::class)
        ->middleware('permission:manage pages')
        ->name('admin.pages.index');

    // Livewire Admin Galleries (Super Admin & Admin CMS)
    Route::get('/galleries', GalleryIndex::class)
        ->middleware('permission:manage galleries')
        ->name('admin.galleries.index');

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
