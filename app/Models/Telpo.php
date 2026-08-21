<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Telpo extends Model
{
    use HasFactory;

    protected $table = 'telpos';

    protected $fillable = [
        'tecnologia_id',
        'imei_antes',
        'v_apk',
        'telpo',
        'imei_telpo',
        'telefono',
        'plan',
        'costo_plan',
    ];

    protected $casts = [
        'costo_plan' => 'decimal:2',
    ];

    public function tecnologia(): BelongsTo
    {
        return $this->belongsTo(Tecnologia::class);
    }
}