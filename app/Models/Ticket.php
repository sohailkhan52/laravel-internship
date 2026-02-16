<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Checklist; // <- THIS is required
use App\Models\Board; // <- THIS is required
class Ticket extends Model
{
    protected $fillable = [
        "title",
        "description",
        "attachment",
        "created_by",
        "deadline",
        "priority_id",
        "assigned_to",
        "status",
        "started_at",
        "completed_at",
        "deadline_notified",
        "points",
        "board_id",
    ];

     protected $casts = [
      'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deadline' => 'datetime', // add this if you have a deadline column
        'started_at' => 'datetime', // if exists
        'completed_at' => 'datetime', // if exi
    ];

    public function checklist(){
        return $this->hasMany(Checklist::class);
    }
    public function board(){
        return $this->belongsTo(Board::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
