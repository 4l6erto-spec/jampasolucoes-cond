<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = ['unit_id', 'user_id', 'subject', 'description', 'status', 'priority', 'private'];
    protected function casts(): array { return ['private' => 'boolean']; }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function comments(): HasMany { return $this->hasMany(TicketComment::class); }
}
