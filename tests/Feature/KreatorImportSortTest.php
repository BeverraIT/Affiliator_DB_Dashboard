<?php

use App\Imports\CreatorsImport;
use App\Imports\SendSampleImport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

// 12 Nov 2026: kategori wajib, import massal tanpa duplikat, tanggal input, sort di header tabel Kreator.
// Tabel dashboard lama tidak ada di migrasi, jadi skema minimal dibuat di sqlite memori.

beforeEach(function () {
    Schema::create('users', function ($t) {
        $t->id(); $t->string('name'); $t->string('email')->nullable(); $t->string('role')->nullable();
        $t->string('password')->nullable(); $t->rememberToken(); $t->timestamps();
    });
    Schema::create('creators', function ($t) {
        $t->id(); $t->unsignedBigInteger('user_id')->nullable(); $t->string('username')->unique();
        $t->string('channel'); $t->bigInteger('gmv')->default(0); $t->integer('itemsterjual')->default(0);
        $t->string('genderpengikut'); $t->string('namawa')->nullable(); $t->string('nomerwa')->nullable();
        $t->boolean('top_creator')->default(false); $t->boolean('is_blocked')->default(false); $t->timestamps();
    });
    foreach (['ages', 'categories', 'contentstyles'] as $tabel) {
        Schema::create($tabel, function ($t) { $t->id(); $t->string('name')->nullable(); $t->timestamps(); });
    }
    Schema::create('age_creator', function ($t) { $t->unsignedBigInteger('creator_id'); $t->unsignedBigInteger('age_id'); });
    Schema::create('category_creator', function ($t) { $t->unsignedBigInteger('creator_id'); $t->unsignedBigInteger('category_id'); });
    Schema::create('content_style_creator', function ($t) { $t->unsignedBigInteger('creator_id'); $t->unsignedBigInteger('content_style_id'); });
    Schema::create('stores', function ($t) { $t->id(); $t->string('name'); $t->timestamps(); });
    Schema::create('products', function ($t) {
        $t->id(); $t->string('sku_utama')->nullable(); $t->string('sku')->nullable(); $t->integer('harga')->nullable(); $t->timestamps();
    });
    Schema::create('send_sample', function ($t) {
        $t->id(); $t->date('deal_date')->nullable(); $t->unsignedBigInteger('pic'); $t->unsignedBigInteger('approve')->nullable();
        $t->unsignedBigInteger('store_id'); $t->unsignedBigInteger('creator_id'); $t->integer('perjanjian_livestream')->nullable();
        $t->integer('perjanjian_video')->nullable(); $t->unsignedBigInteger('product_id'); $t->string('seller_product_id')->nullable();
        $t->integer('qty')->default(1); $t->string('id_pesanan')->nullable(); $t->date('tanggal_kirim')->nullable();
        $t->date('estimasi_sampai')->nullable(); $t->date('tanggal_aktual')->nullable(); $t->integer('video_dibuat')->nullable();
        $t->integer('live_dibuat')->nullable(); $t->integer('status_follow_up_konten_mingguan')->default(0);
        $t->integer('status_chat')->default(0); $t->dateTime('tanggal_dichat')->nullable();
    });

    DB::table('users')->insert([['id' => 7, 'name' => 'Erica', 'role' => 'admin']]);
    DB::table('stores')->insert(['id' => 1, 'name' => 'YARRA STORE']);
    DB::table('products')->insert(['id' => 10, 'sku_utama' => 'BM-T01', 'sku' => 'T01-BSDD-1', 'harga' => 66500]);
    DB::table('categories')->insert([['id' => 1, 'name' => 'Aksesoris Fashion'], ['id' => 2, 'name' => 'Sepatu']]);
    DB::table('creators')->insert([
        ['id' => 1, 'user_id' => 7, 'username' => 'sudahada', 'channel' => 'video', 'gmv' => 500, 'itemsterjual' => 5,
         'genderpengikut' => 'seimbang', 'created_at' => '2026-03-01 08:00:00'],
        ['id' => 2, 'user_id' => 7, 'username' => 'zeta', 'channel' => 'video', 'gmv' => 900, 'itemsterjual' => 9,
         'genderpengikut' => 'perempuan', 'created_at' => '2026-05-01 08:00:00'],
        ['id' => 3, 'user_id' => 7, 'username' => 'tanpakategori', 'channel' => 'video', 'gmv' => 100, 'itemsterjual' => 1,
         'genderpengikut' => 'perempuan', 'created_at' => '2026-04-01 08:00:00'],
    ]);
    DB::table('category_creator')->insert([['creator_id' => 1, 'category_id' => 2], ['creator_id' => 2, 'category_id' => 1]]);
});

function erica(): User
{
    return (new User())->forceFill(['id' => 7, 'name' => 'Erica', 'email' => 'e@example.com', 'role' => 'admin']);
}

function berkasCsv(array $baris): string
{
    $jalur = tempnam(sys_get_temp_dir(), 'imp').'.csv';
    $f = fopen($jalur, 'w');
    foreach ($baris as $b) {
        fputcsv($f, $b);
    }
    fclose($f);

    return $jalur;
}

test('form tambah kreator menolak tanpa kategori', function () {
    $data = ['username' => 'barubanget', 'channel' => 'video', 'genderpengikut' => 'perempuan', 'gmv' => 1000, 'itemsterjual' => 10];

    $this->actingAs(erica())->post('/admin/kreator', $data)->assertSessionHasErrors('categories');
    expect(DB::table('creators')->where('username', 'barubanget')->exists())->toBeFalse();

    $this->actingAs(erica())->post('/admin/kreator', $data + ['categories' => [1]])->assertSessionHasNoErrors();
    expect(DB::table('creators')->where('username', 'barubanget')->exists())->toBeTrue();
});

test('import kreator: duplikat dan tanpa kategori dilewati, tanggal_input dipakai', function () {
    $this->actingAs(erica());
    $jalur = berkasCsv([
        ['username', 'channel', 'gmv', 'itemsterjual', 'genderpengikut', 'namawa', 'nomerwa', 'kategori', 'umur', 'konten_style', 'tanggal_input'],
        ['@BaruSatu', 'video', 1000, 10, 'perempuan', '', '', 'Aksesoris Fashion, Sepatu', '18-24', 'Random', '2026-11-11 15:41:00'],
        ['SudahAda', 'video', 1000, 10, 'perempuan', '', '', 'Sepatu', '', '', ''],          // sudah ada di database
        ['barusatu', 'video', 1000, 10, 'perempuan', '', '', 'Sepatu', '', '', ''],          // ganda di file yang sama
        ['kosongkategori', 'video', 1000, 10, 'perempuan', '', '', '', '', '', ''],          // tanpa kategori
        ['barudua', 'video', 2000, 20, 'seimbang', '', '', 'Sepatu', '', '', ''],            // tanpa tanggal -> waktu upload
    ]);

    $import = new CreatorsImport;
    Excel::import($import, $jalur, null, \Maatwebsite\Excel\Excel::CSV);

    expect($import->imported)->toBe(2)
        ->and($import->duplicates)->toBe(2)
        ->and($import->withoutCategory)->toBe(1)
        ->and(DB::table('creators')->count())->toBe(5);

    $baru = DB::table('creators')->where('username', 'barusatu')->first();
    expect($baru->user_id)->toEqual(7)
        ->and(substr($baru->created_at, 0, 16))->toBe('2026-11-11 15:41')
        ->and(DB::table('category_creator')->where('creator_id', $baru->id)->count())->toBe(2);
    expect(substr(DB::table('creators')->where('username', 'barudua')->value('created_at'), 0, 10))->toBe(now()->format('Y-m-d'));
});

test('import kirim sampel: baris yang sudah ada atau ganda di file dilewati', function () {
    DB::table('send_sample')->insert(['deal_date' => '2026-11-10', 'pic' => 7, 'approve' => 7, 'store_id' => 1, 'creator_id' => 1,
                                      'product_id' => 10, 'qty' => 1, 'id_pesanan' => '5811', 'tanggal_kirim' => '2026-11-10']);
    $kepala = ['deal_date', 'pic', 'approve', 'toko', 'kreator', 'perjanjian_livestream', 'perjanjian_video', 'sku', 'qty',
               'id_pesanan', 'tanggal_kirim', 'tanggal_aktual', 'video_dibuat', 'live_dibuat'];
    $jalur = berkasCsv([
        $kepala,
        ['2026-11-10', 'Erica', 'Erica', 'Yarra Store', 'sudahada', 0, 3, 'T01-BSDD-1', 1, '', '2026-11-10', '', '', ''],     // sudah ada
        ['2026-11-11', 'Erica', 'Erica', 'Yarra Store', 'zeta', 0, 3, 'T01-BSDD-1', 1, '5811', '2026-11-11', '', '', ''],     // ID pesanan sama
        ['2026-11-11', 'Erica', 'Erica', 'Yarra Store', 'zeta', 0, 3, 'T01-BSDD-1', 1, '5900', '2026-11-11', '', '', ''],     // baru
        ['2026-11-11', 'Erica', 'Erica', 'Yarra Store', 'zeta', 0, 3, 'T01-BSDD-1', 1, '', '2026-11-11', '', '', ''],         // ganda di file
        ['2026-11-11', 'Erica', 'Erica', 'Yarra Store', 'tidakdikenal', 0, 3, 'T01-BSDD-1', 1, '', '2026-11-11', '', '', ''], // kreator tak ada
    ]);

    $import = new SendSampleImport;
    Excel::import($import, $jalur, null, \Maatwebsite\Excel\Excel::CSV);

    expect($import->duplicates)->toBe(3)
        ->and($import->skipped)->toBe(1)
        ->and(DB::table('send_sample')->count())->toBe(2);
    $baru = DB::table('send_sample')->where('id_pesanan', '5900')->first();
    expect($baru->creator_id)->toEqual(2)
        ->and(substr($baru->deal_date, 0, 10))->toBe('2026-11-11')
        ->and(substr($baru->estimasi_sampai, 0, 10))->toBe('2026-11-18');
});

test('tabel kreator bisa diurutkan dari header, termasuk kategori dan tanggal input', function () {
    $urutan = function (string $sort, string $dir) {
        $isi = $this->actingAs(erica())->get("/admin/kreator?sort={$sort}&dir={$dir}")->assertOk()->getContent();
        $posisi = [];
        foreach (['sudahada', 'zeta', 'tanpakategori'] as $u) {
            $posisi[$u] = strpos($isi, 'data-copy="'.$u.'"');
        }
        asort($posisi);

        return array_keys($posisi);
    };

    expect($urutan('username', 'asc'))->toBe(['sudahada', 'tanpakategori', 'zeta'])
        ->and($urutan('gmv', 'desc'))->toBe(['zeta', 'sudahada', 'tanpakategori'])
        ->and($urutan('created_at', 'asc'))->toBe(['sudahada', 'tanpakategori', 'zeta'])
        // kategori: Aksesoris Fashion (zeta) < Sepatu (sudahada); tanpa kategori selalu di bawah
        ->and($urutan('kategori', 'asc'))->toBe(['zeta', 'sudahada', 'tanpakategori'])
        ->and($urutan('kategori', 'desc'))->toBe(['sudahada', 'zeta', 'tanpakategori']);

    $this->actingAs(erica())->get('/admin/kreator?sort=kategori&dir=asc')
        ->assertSee('Tgl Input')->assertSee('01/03/2026');
});
