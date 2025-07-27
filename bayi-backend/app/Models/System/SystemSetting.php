<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'is_json',
        'json_value',
    ];

    protected $casts = [
        'is_json' => 'boolean',
        'json_value' => 'array',
    ];

    public static function get(string $key, $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            return $setting->is_json ? $setting->json_value : $setting->value;
        }
        return $default;
    }

    public static function set(string $key, $value, bool $isJson = false): void
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update([
                'value' => $isJson ? null : $value,
                'json_value' => $isJson ? $value : null,
                'is_json' => $isJson,
            ]);
        } else {
            self::create([
                'key' => $key,
                'value' => $isJson ? null : $value,
                'json_value' => $isJson ? $value : null,
                'is_json' => $isJson,
            ]);
        }
    }

    public static function remove(string $key): void
    {
        self::where('key', $key)->delete();
    }

    //group only
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)->get()->toArray();
    }
}
