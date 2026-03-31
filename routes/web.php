<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CreatorController;
use App\Http\Controllers\Admin\KirimSampelController;
use App\Http\Controllers\Admin\FollowUpPengirimanController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\FollowUpKontenController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentStyleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('kirim-sampel/export', [KirimSampelController::class, 'export'])
            ->name('kirim-sampel.export');
        Route::post('/kirim-sampel/import', [KirimSampelController::class, 'import'])
            ->name('kirim-sampel.import');
        Route::delete('/kirim-sampel/bulk-destroy', [KirimSampelController::class, 'bulkDestroy'])
            ->name('kirim-sampel.bulk_destroy');
        Route::get('/kirim-sampel', [KirimSampelController::class, 'index'])
            ->name('kirim-sampel.index');
        Route::get('/kirim-sampel/create', [KirimSampelController::class, 'create'])
            ->name('kirim-sampel.create');
        Route::post('/kirim-sampel', [KirimSampelController::class, 'store'])
            ->name('kirim-sampel.store');
        Route::get('/kirim-sampel/{sendSample}', [KirimSampelController::class, 'show'])
            ->name('kirim-sampel.show');
        Route::get('/kirim-sampel/{sendSample}/edit', [KirimSampelController::class, 'edit'])
            ->name('kirim-sampel.edit');
        Route::put('/kirim-sampel/{sendSample}', [KirimSampelController::class, 'update'])
            ->name('kirim-sampel.update');
        Route::delete('/kirim-sampel/{sendSample}', [KirimSampelController::class, 'destroy'])
            ->name('kirim-sampel.destroy');
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard.index');

            
        Route::get('/follow-up-konten', [FollowUpKontenController::class, 'index'])
            ->name('followup_konten.index');
        Route::post('/follow-up-konten/{sendSample}/video-dibuat', [FollowUpKontenController::class, 'storeVideoDibuat'])
            ->name('followup_konten.video_dibuat');
        Route::post('/followup-konten/{sendSample}/mark-mingguan', [FollowUpKontenController::class, 'markFollowUpMingguan'])
            ->name('followup_konten.mark_mingguan');


        Route::delete('/kreator/bulk-destroy', [CreatorController::class, 'bulkDestroy'])
            ->name('kreator.bulk_destroy');
        Route::post('/kreator/import', [CreatorController::class, 'import'])
            ->name('kreator.import');
        Route::get('kreator/export', [CreatorController::class, 'export'])
            ->name('kreator.export');
        Route::get('/kreator/check-username', [CreatorController::class, 'checkUsername'])
            ->name('kreator.check-username');
        Route::post('/kreator/auto-fill', [CreatorController::class, 'autoFill'])
            ->name('kreator.auto-fill');
        Route::resource('kreator', CreatorController::class)
            ->parameters(['kreator' => 'creator']);
        Route::post('/kreator/{creator}/blokir', [CreatorController::class, 'blokir'])
            ->name('kreator.blokir');


        Route::delete('content-styles/bulk_destroy', [ContentStyleController::class, 'bulkDestroy'])
            ->name('content-styles.bulk_destroy');
        Route::resource('content-styles', ContentStyleController::class);


        Route::delete('categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])
            ->name('category.bulk_destroy');
        Route::resource('category', CategoryController::class);


        Route::get('product/export', [ProductController::class, 'export'])
            ->name('product.export');
        Route::post('/product/import', [ProductController::class, 'import'])
            ->name('product.import');
        Route::delete('/product/bulk-delete', [ProductController::class, 'bulkDestroy'])
            ->name('product.bulk_destroy');
        Route::resource('product', ProductController::class);


        Route::delete('toko/bulk-destroy', [StoreController::class, 'bulkDestroy'])
            ->name('toko.bulk_destroy');
        Route::resource('toko', StoreController::class)
            ->parameters(['toko' => 'store']);


        Route::delete('users/bulk-destroy', [UserController::class, 'bulkDestroy'])
            ->name('users.bulk_destroy');
        Route::resource('users', UserController::class);
    });


    Route::get('/follow-up-pengiriman', [FollowUpPengirimanController::class, 'index'])
        ->name('admin.followup.pengiriman.index');
    Route::put('/follow-up-pengiriman/{sendSample}', [FollowUpPengirimanController::class, 'update'])
        ->name('admin.followup.pengiriman.update');


    Route::get('/followup-chat', [KirimSampelController::class, 'followUpChat'])
        ->name('admin.followup.chat.index');
    Route::post('/followup-chat/{sendSample}/chat-status', [KirimSampelController::class, 'chatStatus'])
        ->name('admin.followup.chat.chatStatus');
});