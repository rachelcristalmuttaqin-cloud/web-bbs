<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->foreignId('keputusan_hes_id')->nullable()->after('risiko_hes_pada')
                ->constrained('keputusan_hes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('keputusan_hes_id');
        });
    }
};
