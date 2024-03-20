<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubArea extends Model
{
    use HasFactory;

    protected $table = 'subarea';

    protected $fillable = [
        'id',
        'id_area',
        'nome_subarea',
        'ativo'
    ];

    public $timestamps = false;
}
