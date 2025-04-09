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
        'mypress_prensa_id'
    ];

    public function prensa()
    {
        return $this->belongsTo(MyPressPrensa::class);
    }
}
