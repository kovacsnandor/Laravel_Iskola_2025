<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schoolclass extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolclassFactory> */
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'osztalyNev',
    ];

     protected $hidden =[
        'created_at',
        'updated_at',
    ];
}
