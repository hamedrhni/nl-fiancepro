<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Log;

trait InteractsWithAuditTrail
{
    public static function bootInteractsWithAuditTrail()
    {
        static::created(function ($model) {
            static::logAuditActivity('created', $model);
        });

        static::updated(function ($model) {
            static::logAuditActivity('updated', $model);
        });

        static::deleted(function ($model) {
            static::logAuditActivity('deleted', $model);
        });
    }

    protected static function logAuditActivity(string $action, $model)
    {
        Log::channel('audit')->info("Model [" . get_class($model) . "] {$action} by User ID [" . (auth()->id() ?? 'system') . "]", [
            'id' => $model->getKey(),
            'changes' => $model->getChanges(),
        ]);
    }
}
