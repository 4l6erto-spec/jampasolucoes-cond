<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = ['unit_id', 'user_id', 'area', 'date', 'start_time', 'end_time', 'status', 'notes'];
    protected function casts(): array { return ['date' => 'date']; }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
