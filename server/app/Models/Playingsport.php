<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playingsport extends Model
{
    /** @use HasFactory<\Database\Factories\PlayingsportFactory> */
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'studentId',
        'sportId',
    ];

    protected function casts(): array
    {
        return [
            'studentId' => 'integer',
            'sportId' => 'integer',
        ];
    }
}
