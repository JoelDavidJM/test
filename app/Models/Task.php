<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    // protected is used to ensure that $fillable is only accessible within the model class
    // $fillable is an array that specifies the attributes of the columns
    // These are the columns that can be filled in bulk
    protected $fillable=[
        'titulo',
        'descripcion',
        'estado',
        'fecha_vencimiento',
        'user_id'
    ];
    //user_id

    public function user() {
       // $this = The current task
       // belongsTo(User::class) = This task belongs to a user
        return $this->belongsTo(User::class);
    }
}

