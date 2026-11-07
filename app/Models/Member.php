<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['full_name', 'email', 'phone', 'gender', 'age', 'address', 'membership_plan_id', 'started_at', 'expires_at'])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'started_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function membershipPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getStatusAttribute(): string
    {
        return $this->expires_at->isToday() || $this->expires_at->isFuture() ? 'active' : 'expired';
    }
}
