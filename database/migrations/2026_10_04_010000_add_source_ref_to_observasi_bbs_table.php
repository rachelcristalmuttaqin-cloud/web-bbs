<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->string('source_ref')->nullable()->unique()->after('observasi_lainnya');
        });
    }

    public function down(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->dropUnique(['source_ref']);
            $table->dropColumn('source_ref');
        });
    }
};
