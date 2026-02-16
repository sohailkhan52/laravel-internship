<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    protected $fillable=[
        "user_id",
        "ticket_id",
        "start_time",
        "end_time",
        "work_detail",
    ];
     protected $casts = [
      'started_time' => 'datetime',
      'end_time' => 'datetime',
      ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }
}
