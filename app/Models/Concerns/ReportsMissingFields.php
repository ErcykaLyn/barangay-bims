<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Tinutukoy kung kulang ang birthday / sex / civil status ng isang tao,
 * para maipakita ang "Needs action" at mai-filter.
 *
 * Resident  -> 'gender' column (default)
 * FamilyMember -> 'sex' column (i-override ang sexColumnName() doon)
 */
trait ReportsMissingFields
{
    protected function sexColumnName(): string
    {
        return 'gender';
    }

    public function getMissingFieldsAttribute(): array
    {
        $missing = [];

        if (empty($this->date_of_birth)) {
            $missing[] = 'Birthday';
        }
        if (empty($this->{$this->sexColumnName()})) {
            $missing[] = 'Sex';
        }
        if (empty($this->civil_status)) {
            $missing[] = 'Civil status';
        }

        return $missing;
    }

    public function getNeedsActionAttribute(): bool
    {
        return !empty($this->missing_fields);
    }

    public function scopeNeedsAction(Builder $query): Builder
    {
        $sex = $this->sexColumnName();

        return $query->where(fn ($q) => $q->whereNull('date_of_birth')
            ->orWhereNull($sex)
            ->orWhereNull('civil_status'));
    }
}