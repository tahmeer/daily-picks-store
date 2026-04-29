<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
Route::get('/items/{item}/restock', [ItemController::class, 'restockForm'])->name('items.restock.form');
Route::post('/items/{item}/restock', [ItemController::class, 'restock'])->name('items.restock');

Route::get('/restock-logs', [ItemController::class, 'restockLogs'])->name('restock-logs.index');

Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

Route::get('/api/items/search', [ItemController::class, 'search'])->name('items.search');
