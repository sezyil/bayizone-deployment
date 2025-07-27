<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
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

    public function findId($id)
    {
        return $this->where('id', $id)->first();
    }

    public function findName($name)
    {
        return $this->where('name', $name)->first();
    }

    public function nameToView()
    {
        $viewLabel = '';
        switch ($this->name) {
            case 'administrator':
                $viewLabel = 'Yönetici';
                break;
            case 'dealer':
                $viewLabel = 'Bayi';
                break;
            case 'user':
                $viewLabel = 'Kullanıcı';
                break;
            default:
                $viewLabel = 'Tanımsız';
                break;
        }
        return $viewLabel;
    }
}
