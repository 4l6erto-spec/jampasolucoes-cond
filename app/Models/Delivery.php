<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = ['unit_id', 'recipient', 'courier', 'tracking_code', 'received_at', 'collected_at', 'received_by', 'collected_by'];
    protected function casts(): array { return ['received_at' => 'datetime', 'collected_at' => 'datetime']; }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}
