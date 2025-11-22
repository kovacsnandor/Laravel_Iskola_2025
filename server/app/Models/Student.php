<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'diakNev',
        'schoolclassId',
        'neme',
        'iranyitoszam',
        'lakHelyseg',
        'lakCim',
        'szulHelyseg',
        'szulDatum',
        'igazolvanyszam',
        'atlag',
        'osztondij',
    ];

    protected function casts(): array
    {
        return [
            'szulDatum' => 'date',
            'neme' => 'boolean',
            'iranyitoszam' => 'string',
            'atlag' => 'decimal:1',
            'osztondij' => 'decimal:0',
        ];
    }
}
