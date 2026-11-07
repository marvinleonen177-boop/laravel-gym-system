<?php

namespace App\Models;

use Database\Factories\MembershipPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'duration_days', 'price', 'is_active'])]
class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
