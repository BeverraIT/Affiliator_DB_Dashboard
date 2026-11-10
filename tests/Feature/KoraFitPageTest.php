<?php

use App\Models\User;
use App\Services\KoraFit;

// Koneksi DB KORA tidak ada di lingkungan test: data diganti stub, dan satu tes memastikan
// halaman tetap terbuka saat DB KORA tidak bisa dibaca.

function koraFitUser(): User
{
    return (new User())->forceFill(['id' => 1, 'name' => 'Tester', 'email' => 't@example.com', 'role' => 'admin']);
}

function stubKoraFit(): void
{
    app()->bind(KoraFit::class, fn () => new class extends KoraFit {
        public function ujiMundur(): array
        {
            $b = fn ($n, $laku, $gmv, $nilai) => ['n' => $n, 'laku' => $laku, 'gmv' => $gmv, 'nilai' => $nilai,
                'pct_laku' => round($laku * 100 / $n), 'per_sampel' => $gmv / $n, 'kali' => round($gmv / $nilai, 1)];
            return ['fit' => $b(118, 52, 58190853, 6068380), 'tidak' => $b(486, 173, 58006689, 13913388),
                    'semua' => $b(604, 225, 116197542, 19981768),
                    'silang' => ['0:1' => $b(13, 7, 19351130, 214000), '1:0' => $b(121, 39, 7519046, 2568114)]];
        }

        public function terbukti(int $page, int $perPage = 20): array
        {
            return ['total' => 1, 'baris' => [(object) ['store_slug' => 'yarra', 'creator_oec_id' => '1', 'username' => 'dwinur808',
                'gmv' => 46536293, 'orders' => 720, 'video' => 12, 'produk' => 2, 'terakhir' => '2026-11-01']]];
        }

        public function kartuFit(int $batas = 30): array
        {
            return [(object) ['id' => 501, 'decision' => 'pending', 'created_at' => now(), 'store_slug' => 'yarra',
                              'username' => 'abc', 'state' => 'menunggu_tim']];
        }
    });
}

test('tamu diarahkan ke login', function () {
    $this->get(route('admin.kora.fit'))->assertRedirect(route('login'));
});

test('halaman kreator fit menampilkan uji mundur, kartu, dan kreator terbukti', function () {
    stubKoraFit();
    $this->actingAs(koraFitUser())->get(route('admin.kora.fit'))
        ->assertOk()
        ->assertSee('Kreator Fit')
        ->assertSee('44%')
        ->assertSee('9,6x')
        ->assertSee('@dwinur808')
        ->assertSee('https://www.tiktok.com/@dwinur808', false)
        ->assertSee('Menunggu tim');
});

test('sub menu KORA tampil di sidebar', function () {
    stubKoraFit();
    $this->actingAs(koraFitUser())->get(route('admin.kora.fit'))
        ->assertSee(route('admin.kora.fit'), false)
        ->assertSee('Ringkasan');
});

test('DB KORA tidak terbaca: halaman tetap terbuka dengan pesan', function () {
    app()->bind(KoraFit::class, fn () => new class extends KoraFit {
        public function ujiMundur(): array
        {
            throw new RuntimeException('koneksi kora tidak ada');
        }
    });
    $this->actingAs(koraFitUser())->get(route('admin.kora.fit'))
        ->assertOk()
        ->assertSee('Data KORA belum bisa dibaca');
});
