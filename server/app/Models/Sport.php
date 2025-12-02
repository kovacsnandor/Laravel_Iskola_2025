<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    /** @use HasFactory<\Database\Factories\SportFactory> */
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'sportNev',
    ];

     protected $hidden =[
        'created_at',
        'updated_at',
    ];
}
