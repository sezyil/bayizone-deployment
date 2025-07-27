<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'guard_name',
        'team_id'
    ];

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
