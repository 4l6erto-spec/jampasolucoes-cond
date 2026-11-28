<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    protected $fillable = ['unit_id', 'authorized_by', 'name', 'document', 'phone', 'vehicle', 'plate', 'token', 'valid_from', 'valid_until', 'checked_in_at', 'checked_out_at'];
    protected function casts(): array { return ['valid_from' => 'datetime', 'valid_until' => 'datetime', 'checked_in_at' => 'datetime', 'checked_out_at' => 'datetime']; }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function authorizer(): BelongsTo { return $this->belongsTo(User::class, 'authorized_by'); }
}
