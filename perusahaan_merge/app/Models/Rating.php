<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'rating';
    protected $primaryKey = 'id_rating';
    public $timestamps = false;

    protected $fillable = [
        'rating',
        'persentase_bonus',
    ];

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'id_rating', 'id_rating');
    }
}
