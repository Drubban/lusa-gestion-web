<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mdvr extends Model
{
    use HasFactory;

    protected $table = 'mdvrs';

    protected $fillable = [
        'tecnologia_id',
        'dvr',
        'modelo',
        'camaras',
        'memoria',
    ];

    public function tecnologia(): BelongsTo
    {
        return $this->belongsTo(Tecnologia::class);
    }
}