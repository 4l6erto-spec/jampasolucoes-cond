<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['user_id', 'title', 'body', 'priority', 'published_at', 'expires_at'];
    protected function casts(): array { return ['published_at' => 'datetime', 'expires_at' => 'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
