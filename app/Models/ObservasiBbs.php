<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservasiBbs extends Model
{
    use HasFactory;

    protected $table = 'observasi_bbs';

    protected $fillable = [
        'tanggal',
        'user_id',
        'lokasi_id',
        'unit_departemen',
        'jenis_perilaku',
        'rekomendasi_perilaku',
        'kategori_id',
        'item_states',
        'observasi_lainnya',
        'source_ref',
        'ketika',
        'ditemukan',
        'sebab',
        'saran',
        'tingkat_risiko',
        'rekomendasi_risiko',
        'risiko_hes',
        'risiko_hes_oleh',
        'risiko_hes_pada',
        'keputusan_hes_id',
        'status_tindak_lanjut',
        'catatan_tindak_lanjut',
    ];

    protected $casts = [
        'item_states' => 'array',
        'risiko_hes_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPerilaku::class, 'kategori_id');
    }

    public function validatorRisikoHes()
    {
        return $this->belongsTo(User::class, 'risiko_hes_oleh');
    }

    public function keputusanHes()
    {
        return $this->belongsTo(KeputusanHes::class, 'keputusan_hes_id');
    }
}
