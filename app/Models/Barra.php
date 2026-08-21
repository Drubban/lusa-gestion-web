<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barra extends Model
{
    use HasFactory;

    protected $table = 'barras';

    protected $fillable = [
        'tecnologia_id',
        'id_barra',
        'barras',
        'telefono',
        'plan',
    ];

    public function tecnologia(): BelongsTo
    {
        return $this->belongsTo(Tecnologia::class);
    }
}