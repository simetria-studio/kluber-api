<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPressProblema extends Model
{
    use HasFactory;

    protected $table = 'my_press_problemas';

    protected $fillable = [
        'produto_redutor_principal',
        'problema_redutor_principal',
        'comentario_redutor_principal',
        'produto_temperatura',
        'problema_temperatura',
        'comentario_temperatura',
        'produto_tambor_principa',
        'problema_tambor_principal',
        'comentario_tambor_principal',
        'mypress_visita_id'
    ];

    protected $casts = [
        'problema_redutor_principal' => 'integer',
        'problema_temperatura' => 'integer',
        'problema_tambor_principal' => 'integer',
    ];

    public function visita()
    {
        return $this->belongsTo(MyPressVisita::class, 'mypress_visita_id', 'id');
    }
}
