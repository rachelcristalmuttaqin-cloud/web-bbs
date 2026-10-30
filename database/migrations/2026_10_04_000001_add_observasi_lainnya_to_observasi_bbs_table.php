<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->text('observasi_lainnya')->nullable()->after('item_states');
        });
    }

    public function down(): void
    {
        Schema::table('observasi_bbs', function (Blueprint $table) {
            $table->dropColumn('observasi_lainnya');
        });
    }
};
