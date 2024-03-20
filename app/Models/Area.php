<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'area_master';

    protected $fillable = [
        'id_plano',
        'nome_area',
        'ativo'
    ];

    public $timestamps = false;
}
