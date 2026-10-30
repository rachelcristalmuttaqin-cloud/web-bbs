<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeputusanHes extends Model
{
    protected $table = 'keputusan_hes';

    protected $fillable = [
        'user_id',
        'admin_name',
        'keputusan',
        'rekomendasi_sistem',
        'mengikuti_rekomendasi',
        'keputusan_custom',
        'catatan',
        'total_observasi',
        'jumlah_rendah',
        'jumlah_sedang',
        'jumlah_tinggi',
    ];

    protected $casts = [
        'mengikuti_rekomendasi' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
