<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/about', 'pages.about')->name('about');
Route::view('/plans', 'pages.plans')->name('plans');
Route::view('/companies', 'companies.index')->name('companies.index');
Route::view('/companies/sample', 'companies.show')->name('companies.show');
Route::view('/news', 'news.index')->name('news.index');
Route::view('/news/sample', 'news.show')->name('news.show');
