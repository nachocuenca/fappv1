<?php
namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;

trait HasTenant
{
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->check() && empty($model->usuario_id)) {
                $model->usuario_id = auth()->id();
            }
        });
    }
}