<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchSchedule extends Model
{
    protected $table = 'matches';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'is_hot_match' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'match_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'match_id');
    }
}