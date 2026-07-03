<?php

use App\Http\Controllers\CompanyRegistrationRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::get('/register-company', [CompanyRegistrationRequestController::class, 'create'])->name('company-registration.create');
Route::post('/register-company', [CompanyRegistrationRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('company-registration.store');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/plans', [PageController::class, 'plans'])->name('plans');
Route::view('/companies', 'companies.index')->name('companies.index');
Route::view('/companies/sample', 'companies.show')->name('companies.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
