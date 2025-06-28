<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ConversionValueController;
use App\Http\Controllers\Admin\CoinRewardConversionController;

Route::middleware(['auth:admin', 'web', 'prevent-back-history'])->group(function(){
    Route::get('conversion-values', [ConversionValueController::class, 'index'])->name('admin.conversion-values.index');
    Route::get('conversion-values/create', [ConversionValueController::class, 'create'])->name('admin.conversion-values.create');
    Route::post('conversion-values', [ConversionValueController::class, 'store'])->name('admin.conversion-values.store');
    Route::get('conversion-values/{id}/edit', [ConversionValueController::class, 'edit'])->name('admin.conversion-values.edit');
    Route::put('conversion-values/{id}', [ConversionValueController::class, 'update'])->name('admin.conversion-values.update');
    Route::post('conversion-values/{id}/activate', [ConversionValueController::class, 'activate'])->name('admin.conversion-values.activate');

    Route::get('coin-reward-conversions', [CoinRewardConversionController::class, 'index'])->name('admin.coin-reward-conversions.index');
    Route::get('coin-reward-conversions/create', [CoinRewardConversionController::class, 'create'])->name('admin.coin-reward-conversions.create');
    Route::post('coin-reward-conversions', [CoinRewardConversionController::class, 'store'])->name('admin.coin-reward-conversions.store');
    Route::get('coin-reward-conversions/{id}/edit', [CoinRewardConversionController::class, 'edit'])->name('admin.coin-reward-conversions.edit');
    Route::put('coin-reward-conversions/{id}', [CoinRewardConversionController::class, 'update'])->name('admin.coin-reward-conversions.update');
    Route::post('coin-reward-conversions/{id}/activate', [CoinRewardConversionController::class, 'activate'])->name('admin.coin-reward-conversions.activate');
});
