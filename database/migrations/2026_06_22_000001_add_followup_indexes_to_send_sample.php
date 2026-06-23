<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index untuk mempercepat halaman follow-up.
     *
     * - idx_chat_followup: query Follow-up Chat memfilter status_chat = 0 lalu
     *   mengurutkan tanggal_aktual. Index gabungan (status_chat, tanggal_aktual)
     *   menghilangkan full scan + filesort.
     * - idx_pengiriman_followup: query Follow-up Pengiriman memfilter
     *   tanggal_aktual IS NULL (sangat selektif) lalu range/sort estimasi_sampai.
     *   Kolom selektif diletakkan pertama agar index efektif.
     */
    public function up(): void
    {
        Schema::table('send_sample', function (Blueprint $table) {
            $table->index(['status_chat', 'tanggal_aktual'], 'idx_chat_followup');
            $table->index(['tanggal_aktual', 'estimasi_sampai'], 'idx_pengiriman_followup');
        });
    }

    public function down(): void
    {
        Schema::table('send_sample', function (Blueprint $table) {
            $table->dropIndex('idx_chat_followup');
            $table->dropIndex('idx_pengiriman_followup');
        });
    }
};
