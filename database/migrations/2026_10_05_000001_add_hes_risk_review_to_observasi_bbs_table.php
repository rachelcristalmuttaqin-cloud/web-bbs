<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->enum('risiko_hes', ['rendah', 'sedang', 'tinggi'])->nullable()->after('tingkat_risiko');
            $table->foreignId('risiko_hes_oleh')->nullable()->after('risiko_hes')->constrained('users')->nullOnDelete();
            $table->timestamp('risiko_hes_pada')->nullable()->after('risiko_hes_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('risiko_hes_oleh');
            $table->dropColumn(['risiko_hes', 'risiko_hes_pada']);
        });
    }
};
