<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keputusan_hes', function (Blueprint $table) {
            $table->string('rekomendasi_sistem', 60)->nullable()->after('keputusan');
            $table->boolean('mengikuti_rekomendasi')->default(false)->after('rekomendasi_sistem');
            $table->string('keputusan_custom', 255)->nullable()->after('mengikuti_rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('keputusan_hes', function (Blueprint $table) {
            $table->dropColumn(['rekomendasi_sistem', 'mengikuti_rekomendasi', 'keputusan_custom']);
        });
    }
};
