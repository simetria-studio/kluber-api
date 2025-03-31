<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressTemperatura extends Model
{
    use HasFactory;

    protected $table = 'my_press_temperaturas';

    protected $fillable = [
        'codigo_empresa',
        'data_registro',
        'zona1',
        'zona2',
        'zona3',
        'zona4',
        'zona5',
        'mypress_elemento_id'
    ];

    public function elemento()
    {
        return $this->belongsTo(MyPressElemento::class);
    }
}
