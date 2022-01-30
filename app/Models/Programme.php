<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;

    protected $fillable=[
      'locuri',
      'sala',
      'from',
      'to',
      'class',
    ];

    public function sala(){
        return $this->belongsTo(Sala::class);
    }

    public function participari(){
        return $this->hasMany(Participari::class);
    }

}
