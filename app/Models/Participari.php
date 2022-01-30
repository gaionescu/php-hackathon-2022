<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participari extends Model
{
    use HasFactory;

    protected $fillable=['user_id','programme_id'];

    public function getProgramme(){
        return $this->belongsTo(Programme::class);
    }

    public function getParticipant(){
        return $this->belongsTo(User::class);
    }
}
