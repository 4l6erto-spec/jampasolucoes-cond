<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    protected $fillable = ['unit_id', 'user_id', 'gate', 'action', 'actor_name', 'ip_address', 'notes'];
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
