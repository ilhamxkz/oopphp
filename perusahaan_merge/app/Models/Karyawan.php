<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $timestamps = false; // tables seem to only use created_at

    protected $fillable = [
        'id_jabatan',
        'id_rating',
        'nama',
        'divisi',
        'alamat',
        'umur',
        'jenis_kelamin',
        'status',
        'created_at',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class, 'id_rating', 'id_rating');
    }

    public function gajis(): HasMany
    {
        return $this->hasMany(Gaji::class, 'id_karyawan', 'id_karyawan');
    }
}
