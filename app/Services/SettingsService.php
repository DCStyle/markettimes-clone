<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache duration in seconds (30 minutes)
     */
    protected int $cacheDuration = 1800;

    /**
     * Cache key prefix
     */
    protected string $cachePrefix = 'setting.';

    /**
     * Get a setting value by key
     */
    public function get(string $key, $default = null)
    {
        return Cache::remember($this->cachePrefix . $key, $this->cacheDuration, function () use ($key, $default) {
            $setting = Setting::byKey($key)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->decoded_value;
        });
    }

    /**
     * Set a setting value
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general'): Setting
    {
        // Encode value based on type
        $encodedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $encodedValue,
                'type' => $type,
                'group' => $group,
            ]
        );

        // Clear cache for this setting
        $this->forget($key);

        return $setting;
    }

    /**
     * Delete a setting from cache
     */
    public function forget(string $key): bool
    {
        return Cache::forget($this->cachePrefix . $key);
    }

    /**
     * Get all settings, optionally filtered by group
     */
    public function all(?string $group = null): array
    {
        $cacheKey = $this->cachePrefix . 'all' . ($group ? ".{$group}" : '');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($group) {
            $query = Setting::query();

            if ($group) {
                $query->group($group);
            }

            return $query->get()->pluck('decoded_value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Check if a setting exists
     */
    public function has(string $key): bool
    {
        return Setting::byKey($key)->exists();
    }

    /**
     * Delete a setting
     */
    public function delete(string $key): bool
    {
        $this->forget($key);
        return Setting::byKey($key)->delete();
    }
}
