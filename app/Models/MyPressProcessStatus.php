<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyPressProcessStatus extends Model
{
    protected $table = 'my_press_process_status';

    protected $fillable = [
        'job_id',
        'visita_id',
        'status',
        'error_message'
    ];

    public function visita()
    {
        return $this->belongsTo(MyPressVisita::class, 'visita_id');
    }
} 