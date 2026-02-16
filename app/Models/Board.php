<?php

namespace App\Models;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable=[
        'name',
        'description',
        'created_by',
    ];




    public function ticket(){
        return $this->hasMany(Ticket::class);
    }

}
