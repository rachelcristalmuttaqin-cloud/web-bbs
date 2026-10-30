<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->enum('rekomendasi_perilaku', ['safe', 'at_risk'])->nullable()->after('jenis_perilaku');
            $table->enum('rekomendasi_risiko', ['rendah', 'sedang', 'tinggi'])->nullable()->after('tingkat_risiko');
        });
    }

    public function down(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->dropColumn(['rekomendasi_perilaku', 'rekomendasi_risiko']);
        });
    }
};
