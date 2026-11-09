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
use App\Http\Controllers\Admin\KoraController;
use App\Http\Controllers\Api\KoraApiController;

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Root: redirect to /dashboard
// Pintu masuk KORA (creator agent): dijaga token bearer, bukan sesi login.
Route::prefix('kora-api')->middleware('kora.token')->name('kora_api.')->group(function () {
    Route::post('/kreator', [KoraApiController::class, 'kreator'])->name('kreator');
    Route::post('/kirim-sampel', [KoraApiController::class, 'kirimSampel'])->name('kirim_sampel');
    Route::patch('/kirim-sampel/{sendSample}', [KoraApiController::class, 'perbaruiKirimSampel'])->name('kirim_sampel.perbarui');
});

Route::redirect('/', '/dashboard');
Route::redirect('/dashboard', '/admin/dashboard');


Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/kirim-sampel/update-pic-massal', [KirimSampelController::class, 'updatePicMassal'])
            ->name('kirim-sampel.update-pic-massal');
        Route::get('kirim-sampel/export', [KirimSampelController::class, 'export'])
            ->name('kirim-sampel.export');
        Route::get('/kirim-sampel/template', [KirimSampelController::class, 'downloadTemplate'])
            ->name('kirim-sampel.template');
        Route::get('/kirim-sampel/search/creators', [KirimSampelController::class, 'searchCreators'])
            ->name('kirim-sampel.search_creators');
        Route::get('/kirim-sampel/search/products', [KirimSampelController::class, 'searchProducts'])
            ->name('kirim-sampel.search_products');
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
        Route::get('/kora', [KoraController::class, 'index'])
            ->name('kora.index');


        Route::get('/follow-up-konten', [FollowUpKontenController::class, 'index'])
            ->name('followup_konten.index');
        Route::prefix('/follow-up-konten')->name('followup_konten.')->controller(FollowUpKontenController::class)->group(function () {
            Route::post('/{sendSample}/video-dibuat', 'storeVideoDibuat')->name('video_dibuat');
            Route::post('/{sendSample}/mark-mingguan', 'markFollowUpMingguan')->name('mark_mingguan');
            Route::put('/{sendSample}/update-product-id', 'updateProductId')->name('update_product_id');
        });


        Route::delete('/kreator/bulk-destroy', [CreatorController::class, 'bulkDestroy'])
            ->name('kreator.bulk_destroy');
        Route::post('/kreator/import', [CreatorController::class, 'import'])
            ->name('kreator.import');
        Route::get('kreator/export', [CreatorController::class, 'export'])
            ->name('kreator.export');
        Route::get('/kreator/template', [CreatorController::class, 'downloadTemplate'])
            ->name('kreator.template');
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


        Route::prefix('product')->name('product.')->controller(ProductController::class)->group(function () {
            Route::get('/export', 'export')->name('export');
            Route::get('/template', 'downloadTemplate')->name('template');
            Route::post('/import', 'import')->name('import');
            Route::delete('/bbulk-delete', 'bulkDestroy')->name('bulk_destroy');
        });
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