<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellingController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\StoreProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DeleteProductController;
use App\Http\Controllers\ViewStoreController;
use App\Http\Controllers\EmailServiceController;
use App\Http\Controllers\UpdatePriceController;
use App\Http\Controllers\SaveBillController;
use App\Http\Controllers\BillsController;

//Route::METHOD('URL', [CONTROLLER, 'FUNCTION'])->name('NAME')

Route::get('/', SellingController::class)->name('selling');
Route::post('/sell', SaleController::class)->name('sell.process');

Route::get('/manage', ManageController::class)->name('manage');
Route::post('/manage', StoreProductController::class)->name('products.store');
Route::put('/manage/update-stock', StockController::class)->name('products.updateStock');
Route::put('/manage/update-price', UpdatePriceController::class)->name('products.updatePrice');
Route::delete('/manage/{product}', DeleteProductController::class)->name('products.destroy');

Route::get('/store', ViewStoreController::class)->name('store');

Route::post('/send-bill-email', [EmailServiceController::class, 'emailService'])->name('bill.email');
Route::post('/save-bill', SaveBillController::class)->name('bill.save');
Route::get('/bills/{billId}', BillsController::class)->name('bills.show');

