<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $table = 'pokemons';

    protected $fillable = [
        'number',
        'name',
        'type1',
        'type2',
        'height',
        'weight',
        'description',
        'image_url'
    ];
}