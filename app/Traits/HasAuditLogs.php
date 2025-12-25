<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasAuditLogs
{
    public static function bootHasAuditLogs()
    {
        static::created(function ($model) {
            $model->logAudit('created');
        });

        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });
    }

    public function logAudit(string $action)
    {
        $companyId = $this->company_id ?? (method_exists($this, 'company') ? $this->company?->id : null);
        
        // If still null, try to get from session
        if (!$companyId && session('company_id')) {
            $companyId = session('company_id');
        }

        if (!$companyId && Auth::check()) {
            $companyId = Auth::user()->companies()->first()?->id;
        }

        if (!$companyId) {
            return;
        }

        $metadata = [];
        if ($action === 'updated') {
            $changes = $this->getChanges();
            if (empty($changes)) {
                return;
            }
            $metadata = [
                'old' => array_intersect_key($this->getOriginal(), $changes),
                'new' => $changes,
            ];
        }

        AuditLog::create([
            'company_id' => $companyId,
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
