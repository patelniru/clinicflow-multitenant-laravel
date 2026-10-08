<?php
namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder): void {
            $user = auth()->user();
            if ($user && !$user->isSuperAdmin()) {
                $builder->where($builder->getModel()->getTable().'.company_id', $user->company_id);
            }
        });
        static::creating(function ($model): void {
            $user = auth()->user();
            if (!$model->company_id && $user && !$user->isSuperAdmin()) $model->company_id = $user->company_id;
        });
    }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
