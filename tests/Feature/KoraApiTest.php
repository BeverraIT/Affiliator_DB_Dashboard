<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// API KORA (/kora-api/*). Tabel dashboard lama tidak ada di migrasi, jadi skema minimal dibuat di sqlite memori.

beforeEach(function () {
    config(['kora.api_token' => 'token-rahasia', 'kora.user_id' => 99]);

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
        $t->id(); $t->date('deal_date'); $t->unsignedBigInteger('pic'); $t->unsignedBigInteger('approve');
        $t->unsignedBigInteger('store_id'); $t->unsignedBigInteger('creator_id'); $t->integer('perjanjian_livestream')->nullable();
        $t->integer('perjanjian_video')->nullable(); $t->unsignedBigInteger('product_id'); $t->string('seller_product_id')->nullable();
        $t->integer('qty')->default(1); $t->string('id_pesanan')->nullable(); $t->date('tanggal_kirim')->nullable();
        $t->date('estimasi_sampai')->nullable(); $t->date('tanggal_aktual')->nullable(); $t->integer('video_dibuat')->nullable();
        $t->integer('live_dibuat')->nullable(); $t->integer('status_follow_up_konten_mingguan')->default(0);
        $t->integer('status_chat')->default(0); $t->dateTime('tanggal_dichat')->nullable(); $t->timestamps();
    });

    DB::table('users')->insert([
        ['id' => 99, 'name' => 'KORA', 'role' => 'admin'],
        ['id' => 5, 'name' => 'Hima', 'role' => 'admin'],
    ]);
    DB::table('stores')->insert(['id' => 4, 'name' => 'MINZO STORE']);
    DB::table('products')->insert(['id' => 3920, 'sku_utama' => 'BM-T01-BSDD', 'sku' => 'T01-BSDD-1', 'harga' => 66500]);
});

function kreatorKora(array $ubah = []): array
{
    return array_merge(['username' => '@Febiyani31', 'channel' => 'video', 'genderpengikut' => 'perempuan',
                        'gmv' => 34788092, 'itemsterjual' => 2100], $ubah);
}

function sampelKora(int $creatorId, array $ubah = []): array
{
    return array_merge(['creator_id' => $creatorId, 'store_id' => 4, 'product_id' => 3920,
                        'seller_product_id' => '1738281994219455657', 'deal_date' => '2026-11-05', 'tanggal_kirim' => '2026-11-05',
                        'qty' => 1, 'perjanjian_video' => 5], $ubah);
}

function koraApi(string $metode, string $url, array $data = [], string $token = 'token-rahasia')
{
    return test()->withHeaders(['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])->json($metode, $url, $data);
}

test('tanpa token atau token salah ditolak', function () {
    test()->withHeaders(['Accept' => 'application/json'])->postJson('/kora-api/kreator', kreatorKora())->assertStatus(401);
    koraApi('POST', '/kora-api/kreator', kreatorKora(), 'salah')->assertStatus(401);
});

test('API mati (404) kalau token atau akun KORA belum diisi', function () {
    config(['kora.api_token' => null]);
    koraApi('POST', '/kora-api/kreator', kreatorKora())->assertNotFound();
});

test('kreator baru dibuat atas nama KORA dengan username dinormalkan', function () {
    $res = koraApi('POST', '/kora-api/kreator', kreatorKora());

    $res->assertStatus(201)->assertJson(['created' => true]);
    $this->assertDatabaseHas('creators', ['username' => 'febiyani31', 'user_id' => 99, 'top_creator' => 0]);
});

test('kreator yang sudah ada tidak diubah dan tidak digandakan', function () {
    DB::table('creators')->insert(['id' => 7, 'user_id' => 5, 'username' => 'febiyani31', 'channel' => 'live',
                                   'genderpengikut' => 'laki-laki', 'gmv' => 1, 'itemsterjual' => 1]);

    $res = koraApi('POST', '/kora-api/kreator', kreatorKora());

    $res->assertOk()->assertJson(['id' => 7, 'created' => false]);
    expect(DB::table('creators')->count())->toBe(1);
    $this->assertDatabaseHas('creators', ['id' => 7, 'user_id' => 5, 'channel' => 'live']);
});

test('data kreator tidak valid ditolak 422', function () {
    koraApi('POST', '/kora-api/kreator', kreatorKora(['channel' => 'podcast']))->assertStatus(422);
});

test('kirim sampel dibuat dengan PIC dan approve = KORA, estimasi kirim + 7 hari', function () {
    $res = koraApi('POST', '/kora-api/kirim-sampel', sampelKora(1) + ['status_chat' => 1, 'tanggal_dichat' => '2026-11-05 10:00:00']);
    // creator_id 1 belum ada -> tolak
    $res->assertStatus(422);

    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');
    $res = koraApi('POST', '/kora-api/kirim-sampel', sampelKora($kid) + ['status_chat' => 1, 'tanggal_dichat' => '2026-11-05 10:00:00']);

    $res->assertStatus(201)->assertJson(['created' => true]);
    $this->assertDatabaseHas('send_sample', ['creator_id' => $kid, 'pic' => 99, 'approve' => 99,
                                             'perjanjian_livestream' => 0, 'status_chat' => 1]);
    // sqlite menyimpan tanggal dengan jam; MySQL memotongnya, jadi bandingkan bagian tanggalnya saja.
    expect(substr((string) DB::table('send_sample')->value('estimasi_sampai'), 0, 10))->toBe('2026-11-12');
});

test('kirim sampel yang sama dua kali tidak membuat baris ganda', function () {
    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');
    $pertama = koraApi('POST', '/kora-api/kirim-sampel', sampelKora($kid))->json('id');
    $kedua = koraApi('POST', '/kora-api/kirim-sampel', sampelKora($kid));

    $kedua->assertOk()->assertJson(['id' => $pertama, 'created' => false]);
    expect(DB::table('send_sample')->count())->toBe(1);
});

test('tanggal kirim sebelum tanggal deal ditolak', function () {
    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');
    koraApi('POST', '/kora-api/kirim-sampel', sampelKora($kid, ['tanggal_kirim' => '2026-11-01']))->assertStatus(422);
});

test('KORA hanya boleh melengkapi baris buatannya sendiri', function () {
    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');
    $milikKora = koraApi('POST', '/kora-api/kirim-sampel', sampelKora($kid))->json('id');
    $milikTim = DB::table('send_sample')->insertGetId(sampelKora($kid, ['deal_date' => '2026-10-01', 'tanggal_kirim' => '2026-10-01'])
        + ['pic' => 5, 'approve' => 5, 'estimasi_sampai' => '2026-10-08']);

    koraApi('PATCH', "/kora-api/kirim-sampel/{$milikKora}", ['id_pesanan' => '587008287385224994', 'video_dibuat' => 2])->assertOk();
    $this->assertDatabaseHas('send_sample', ['id' => $milikKora, 'id_pesanan' => '587008287385224994', 'video_dibuat' => 2]);

    koraApi('PATCH', "/kora-api/kirim-sampel/{$milikTim}", ['id_pesanan' => '1'])->assertStatus(403);
    $this->assertDatabaseMissing('send_sample', ['id' => $milikTim, 'id_pesanan' => '1']);
});

test('user biasa melihat baris KORA di daftar kirim sampel, tapi tidak baris PIC lain', function () {
    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');
    $koraRow = koraApi('POST', '/kora-api/kirim-sampel', sampelKora($kid))->json('id');
    $lain = DB::table('send_sample')->insertGetId(sampelKora($kid, ['deal_date' => '2026-10-01', 'tanggal_kirim' => '2026-10-01'])
        + ['pic' => 8, 'approve' => 8, 'estimasi_sampai' => '2026-10-08']);
    $hima = (new App\Models\User())->forceFill(['id' => 5, 'role' => 'admin']);

    $id = App\Models\SendSample::query()->visibleTo($hima)->pluck('id')->all();

    expect($id)->toContain($koraRow)->not->toContain($lain);
    expect(App\Models\SendSample::query()->visibleTo((new App\Models\User())->forceFill(['id' => 1, 'role' => 'super admin']))->count())->toBe(2);
});

test('KORA mengisi konten style kreator buatannya yang masih kosong, tanpa menimpa isian tim', function () {
    DB::table('contentstyles')->insert([['id' => 3, 'name' => 'Goyang Produk + VO'], ['id' => 5, 'name' => 'Review Produk + VO']]);
    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');

    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => [5]])->assertOk()->assertJson(['updated' => true]);
    $this->assertDatabaseHas('content_style_creator', ['creator_id' => $kid, 'content_style_id' => 5]);

    // sudah terisi -> tidak ditimpa
    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => [3]])->assertOk()->assertJson(['updated' => false]);
    $this->assertDatabaseMissing('content_style_creator', ['creator_id' => $kid, 'content_style_id' => 3]);

    // id tidak dikenal / kosong ditolak
    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => [999]])->assertStatus(422);
    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => []])->assertStatus(422);

    // kreator tim tidak boleh disentuh
    $tim = DB::table('creators')->insertGetId(['user_id' => 5, 'username' => 'kreatortim', 'channel' => 'video', 'genderpengikut' => 'perempuan']);
    koraApi('PATCH', "/kora-api/kreator/{$tim}/konten-style", ['contentstyles' => [5]])->assertStatus(403);
    $this->assertDatabaseMissing('content_style_creator', ['creator_id' => $tim]);
});

test('KORA boleh mengganti tebakannya sendiri selama belum diedit tim', function () {
    DB::table('contentstyles')->insert([['id' => 3, 'name' => 'Goyang Produk + VO'], ['id' => 5, 'name' => 'Review Produk + VO'],
                                        ['id' => 7, 'name' => 'Menampilkan Wajah + Text']]);
    $kid = koraApi('POST', '/kora-api/kreator', kreatorKora())->json('id');
    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => [5]])->assertJson(['updated' => true]);

    // isi lama masih sama dengan kiriman KORA -> diganti jadi 2 style
    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => [5, 7], 'sebelumnya' => [5]])
        ->assertOk()->assertJson(['updated' => true]);
    expect(DB::table('content_style_creator')->where('creator_id', $kid)->pluck('content_style_id')->sort()->values()->all())->toBe([5, 7]);

    // tim mengedit (hapus 7, tambah 3) -> KORA tidak boleh menimpa
    DB::table('content_style_creator')->where('creator_id', $kid)->where('content_style_id', 7)->delete();
    DB::table('content_style_creator')->insert(['creator_id' => $kid, 'content_style_id' => 3]);
    koraApi('PATCH', "/kora-api/kreator/{$kid}/konten-style", ['contentstyles' => [7], 'sebelumnya' => [5, 7]])
        ->assertOk()->assertJson(['updated' => false]);
    expect(DB::table('content_style_creator')->where('creator_id', $kid)->pluck('content_style_id')->sort()->values()->all())->toBe([3, 5]);
});
