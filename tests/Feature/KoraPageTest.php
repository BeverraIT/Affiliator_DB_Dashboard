<?php

use App\Models\User;
use App\Services\KoraStats;

// Koneksi DB KORA tidak ada di lingkungan test: data diganti stub, dan satu tes memastikan
// halaman tetap terbuka saat DB KORA tidak bisa dibaca.

function koraUser(): User
{
    return (new User())->forceFill(['id' => 1, 'name' => 'Tester', 'email' => 't@example.com', 'role' => 'admin']);
}

function stubKoraStats(): void
{
    app()->bind(KoraStats::class, fn () => new class extends KoraStats {
        public function ringkasan($dari, $sampai): array
        {
            return ['dicek' => 2978, 'kreator' => 2831, 'ditolak' => 2256, 'lolos' => 699, 'ditawari' => 369, 'deal' => 184,
                    'approve' => 178, 'chat' => 849, 'kartu' => 351, 'kartu_putus' => 281, 'median_putus' => 30,
                    'jalur' => ['kora' => 47, 'approve_manual_wa' => 70]];
        }

        public function kinerja(): array
        {
            $kosong = ['matang' => 0, 'selesai' => null, 'lengkap' => null, 'laku' => null, 'views' => 0, 'gmv' => 0.0,
                       'per_sampel' => null, 'rasio' => null, 'baru' => 0];
            $hasil = [];
            foreach (self::KELOMPOK as $k => $label) {
                $hasil[$k] = ['label' => $label] + $kosong;
            }
            $hasil['nego_kora'] = ['label' => self::KELOMPOK['nego_kora'], 'matang' => 27, 'selesai' => 85, 'lengkap' => null,
                                   'laku' => 41, 'views' => 16487, 'gmv' => 6131702.0, 'per_sampel' => 227100.0, 'rasio' => 3.75, 'baru' => 33];
            return $hasil;
        }

        public function antrian(?string $state, int $page, int $perPage = 10): array
        {
            $baris = (object) ['id' => 2469, 'store_slug' => 'keren', 'username' => 'nasrullohagm', 'state' => 'nego_menunggu_kreator',
                               'screening_result' => 'lolos', 'produk' => ['Topi Keren Baseball'], 'request_deadline' => now()->addHours(30),
                               'created_at' => now()->subDay()];
            return ['jumlah' => ['nego_menunggu_kreator' => 1], 'total' => 1, 'baris' => [$baris]];
        }

        public function penjualan(): array
        {
            return [
                'mingguan' => [(object) ['minggu_awal' => '2026-10-19', 'gmv' => 683296473, 'orders' => 20307]],
                'kreator'  => [(object) ['username' => 'missyka14', 'store_slug' => 'yarra', 'gmv' => 86594985, 'orders' => 2059, 'video' => 9, 'sampel' => 1]],
                'produk'   => [(object) ['product_id' => '1', 'store_slug' => 'yarra', 'gmv' => 207024327, 'orders' => 3122, 'kreator' => 100, 'judul' => 'Yarra Topi Pria']],
            ];
        }
    });
}

test('tamu diarahkan ke login', function () {
    $this->get(route('admin.kora.index'))->assertRedirect(route('login'));
});

test('ringkasan menampilkan funnel KORA', function () {
    stubKoraStats();
    $this->actingAs(koraUser())->get(route('admin.kora.index'))
        ->assertOk()
        ->assertSee('Dari pengajuan sampai sampel')
        ->assertSee('2.978')
        ->assertSee('median 30 menit')
        ->assertSee('Di-approve KORA (setelah DEAL tim)');
});

test('tab kinerja, antrian, dan penjualan tampil', function () {
    stubKoraStats();
    $user = koraUser();
    $this->actingAs($user)->get(route('admin.kora.index', ['tab' => 'kinerja']))
        ->assertOk()->assertSee('Nego KORA, approve tim')->assertSee('3,8×', false)
        ->assertSee('perbandingan dengan tim belum adil');
    $this->actingAs($user)->get(route('admin.kora.index', ['tab' => 'antrian']))
        ->assertOk()->assertSee('@nasrullohagm')->assertSee('https://www.tiktok.com/@nasrullohagm', false);
    $this->actingAs($user)->get(route('admin.kora.index', ['tab' => 'penjualan', 'toko' => 'yarra']))
        ->assertOk()->assertSee('missyka14')->assertSee('koraGmvChart', false);
});

test('DB KORA tidak terbaca: halaman tetap terbuka dengan pesan', function () {
    config(['database.connections.kora.host' => '127.0.0.1', 'database.connections.kora.port' => '1']);
    $this->actingAs(koraUser())->get(route('admin.kora.index'))
        ->assertOk()
        ->assertSee('Data KORA belum bisa dibaca');
});
