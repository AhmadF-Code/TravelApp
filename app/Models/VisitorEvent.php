<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorEvent extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'metadata' => 'array'
    ];

    public function visitor()
    {
        return $this->belongsTo(VisitorLog::class, 'visitor_log_id');
    }
}
