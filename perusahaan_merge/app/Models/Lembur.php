<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';
    protected $primaryKey = 'id_lembur';
    public $timestamps = false;

    protected $fillable = [
        'tarif',
    ];

    public function gajis(): HasMany
    {
        return $this->hasMany(Gaji::class, 'id_lembur', 'id_lembur');
    }
}
