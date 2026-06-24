<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// ─── Public Landing ────────────────────────────────────────────────────────────
Route::view('/', 'welcome');

// ─── Authenticated Routes ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Volt::route('dashboard', 'dashboard')->name('dashboard');

    // Profile
    Route::view('profile', 'profile')->name('profile');

    // ── Master Data: accessible by admin AND petugas ───────────────────────────
    Route::middleware('role:admin,petugas')->group(function () {
        Volt::route('categories', 'pages.categories.index')->name('categories.index');
        Volt::route('categories/create', 'pages.categories.create')->name('categories.create');
        Volt::route('categories/{id}/edit', 'pages.categories.edit')->name('categories.edit');

        Volt::route('brands', 'pages.brands.index')->name('brands.index');
        Volt::route('brands/create', 'pages.brands.create')->name('brands.create');
        Volt::route('brands/{id}/edit', 'pages.brands.edit')->name('brands.edit');

        Volt::route('products', 'pages.products.index')->name('products.index');
        Volt::route('products/create', 'pages.products.create')->name('products.create');
        Volt::route('products/{id}/edit', 'pages.products.edit')->name('products.edit');

        Volt::route('suppliers', 'pages.suppliers.index')->name('suppliers.index');
        Volt::route('suppliers/create', 'pages.suppliers.create')->name('suppliers.create');
        Volt::route('suppliers/{id}/edit', 'pages.suppliers.edit')->name('suppliers.edit');
    });

    // ── Admin-only routes ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Volt::route('users', 'pages.users.index')->name('users.index');
        Volt::route('users/create', 'pages.users.create')->name('users.create');
        Volt::route('users/{id}/edit', 'pages.users.edit')->name('users.edit');
        Volt::route('purchases', 'pages.purchases.index')->name('purchases.index');
        Volt::route('purchases/create', 'pages.purchases.create')->name('purchases.create');
        Volt::route('purchases/{purchase}', 'pages.purchases.show')->name('purchases.show');
        Volt::route('reports/sales', 'pages.reports.sales')->name('reports.sales');
    });

    // ── POS / Kasir: petugas AND admin ────────────────────────────────────────
    Route::middleware('role:admin,petugas')->group(function () {
        Volt::route('pos', 'pages.pos.index')->name('pos.index');
    });
});

require __DIR__ . '/auth.php';
