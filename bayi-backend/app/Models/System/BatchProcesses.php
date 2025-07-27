<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchProcesses extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'customer_id',
        'payload',
        'errors',
        'type',
        'is_completed',
        'is_failed',
        'is_system',
    ];
}
