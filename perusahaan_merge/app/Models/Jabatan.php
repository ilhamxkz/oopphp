<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'jabatan',
        'gaji_pokok',
        'tunjangan',
        'created_at',
    ];

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'id_jabatan', 'id_jabatan');
    }
}
