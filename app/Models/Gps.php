<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gps extends Model
{
    use HasFactory;

    protected $table = 'gps';

    protected $fillable = [
        'tecnologia_id',
        'imei_gps',
        'telefono',
        'plan',
    ];

    public function tecnologia(): BelongsTo
    {
        return $this->belongsTo(Tecnologia::class);
    }
}