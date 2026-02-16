<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket; // <- THIS is required
class checklist extends Model
{
    protected $fillable=[
        "ticket_id",
        "name",
        "completed_at",
    ];

    public $timestamps=false;
    protected $casts=[
        "completed_at"=>"datetime"
    ];

    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }
}
