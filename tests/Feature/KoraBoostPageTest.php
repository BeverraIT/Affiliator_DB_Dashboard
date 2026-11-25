<?php

use App\Models\User;
use App\Services\KoraBoost;

// Koneksi DB KORA tidak ada di lingkungan test: data diganti stub, dan satu tes memastikan
// halaman tetap terbuka saat DB KORA tidak bisa dibaca.

function koraBoostUser(): User
{
    return (new User())->forceFill(['id' => 1, 'name' => 'Tester', 'email' => 't@example.com', 'role' => 'admin']);
}

function stubKoraBoost(): void
{
    app()->bind(KoraBoost::class, fn () => new class extends KoraBoost {
        public function kampanye(): array
        {
            return [(object) ['kode' => 'produk_fokus_20261124', 'nama' => 'Produk Fokus', 'mulai' => '2026-11-24',
                              'sampai' => '2026-12-31', 'target_video' => 100, 'komisi' => '10%', 'updated_at' => now()],
                    (object) ['kode' => 'kreator_terbukti_20261110', 'nama' => 'Kreator Terbukti (pilot Yarra)',
                              'mulai' => '2026-11-10', 'sampai' => null, 'target_video' => null, 'komisi' => '10%', 'updated_at' => now()]];
        }

        public function ringkasan(object $k): array
        {
            return ['status' => ['diundang' => 193, 'mengajukan' => 10, 'deal' => 3, 'belum_dikirim' => 266],
                    'corong' => [['Diundang', 206], ['Mengajukan sampel', 13], ['Sampel di-ACC', 0], ['Membuat video', 0], ['Videonya laku', 0]],
                    'total' => (object) ['kreator' => 476, 'sampel' => 0, 'video' => 0, 'gmv' => 0, 'orders' => 0, 'diperbarui' => now()],
                    'target' => (object) ['sampel' => 40, 'video' => 37, 'laku' => 5, 'gmv' => 1000000]];
        }

        public function perProduk(object $k): array
        {
            return [(object) ['sku' => 'TRC', 'judul' => 'Yarra Topi Baseball', 'antre' => 3, 'diundang' => 74, 'mengajukan' => 5,
                              'acc' => 0, 'video' => 0, 'gmv' => 0, 'semua_sampel' => 12, 'semua_video' => 9]];
        }

        public function belumTerpetakan(object $k): array
        {
            return ['T01-PTAA-27'];
        }

        public function daftar(object $k, array $f, int $page, int $perPage = 25): array
        {
            $GLOBALS['boostFilter'] = $f + ['kampanye' => $k->kode];
            return ['total' => 1, 'baris' => [(object) ['username' => 'mnasrulloh7', 'store_slug' => 'yarra', 'sku' => 'T01-BSCR',
                'product_title' => 'Topi Bucket', 'status' => 'deal', 'request_state' => 'deal_tunggu_approve',
                'diundang_at' => '2026-11-24 14:25:00', 'mengajukan_at' => '2026-11-25 08:06:41', 'acc_at' => null,
                'video' => 0, 'gmv' => 0, 'orders' => 0, 'gmv_sebelum' => 2390000,
                'ket' => 'Nego: menunggu kreator', 'produk_lain' => 'Yarra Brush Make Up (BRS1-2 BROWN)']]];
        }
    });
}

test('tamu diarahkan ke login', function () {
    $this->get(route('admin.kora.boost'))->assertRedirect(route('login'));
});

test('halaman boost menampilkan target video, corong, produk, dan kreator', function () {
    stubKoraBoost();
    $this->actingAs(koraBoostUser())->get(route('admin.kora.boost'))
        ->assertOk()
        ->assertSee('Boost')
        ->assertSee('/ 100', false)
        ->assertSee('Mengajukan sampel')
        ->assertSee('TRC')
        ->assertSee('belum terhubung ke produk TikTok')
        ->assertSee('@mnasrulloh7')
        ->assertSee('https://www.tiktok.com/@mnasrulloh7', false)
        ->assertSee('DEAL, tunggu approve')
        ->assertSee('Nego: menunggu kreator')
        ->assertSee('Mengajukan produk lain: Yarra Brush Make Up (BRS1-2 BROWN)')
        ->assertSee(route('admin.kora.boost'), false);
});

test('filter kampanye dan status diteruskan', function () {
    stubKoraBoost();
    $this->actingAs(koraBoostUser())
        ->get(route('admin.kora.boost', ['kampanye' => 'kreator_terbukti_20261110', 'status' => 'deal', 'toko' => 'xx']))
        ->assertOk();
    $f = $GLOBALS['boostFilter'];
    expect($f['kampanye'])->toBe('kreator_terbukti_20261110')
        ->and($f['status'])->toBe('deal')
        ->and($f['toko'])->toBeNull();
});

test('DB KORA tidak terbaca: halaman tetap terbuka dengan pesan', function () {
    app()->bind(KoraBoost::class, fn () => new class extends KoraBoost {
        public function kampanye(): array
        {
            throw new RuntimeException('koneksi kora tidak ada');
        }
    });
    $this->actingAs(koraBoostUser())->get(route('admin.kora.boost'))
        ->assertOk()
        ->assertSee('Data Boost belum bisa dibaca');
});
