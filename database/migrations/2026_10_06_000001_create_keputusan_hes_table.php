<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keputusan_hes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admin_name');
            $table->string('keputusan', 60);
            $table->text('catatan')->nullable();
            $table->unsignedInteger('total_observasi');
            $table->unsignedInteger('jumlah_rendah');
            $table->unsignedInteger('jumlah_sedang');
            $table->unsignedInteger('jumlah_tinggi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keputusan_hes');
    }
};
