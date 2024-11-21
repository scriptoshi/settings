<?php

namespace Scriptoshi\Settings;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

use Scriptoshi\Settings\Models\Setting;

class Settings
{
    /**
     * Cache key.
     *
     * @var string
     */
    protected static $cacheKey = 'betriver-settings-cache';
    protected static $groupKey = 'betriver-settings-cache';

    /**
     * Get all settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function all(bool $fresh = false): Collection
    {
        if ($fresh) {
            $dot = Setting::all()->flatMap(fn(Setting $s) => ["{$s->group}.{$s->name}" => $s->val]);
            return Setting::query()->pluck('val', 'name')->merge($dot);
        }

        return Cache::rememberForever(static::$groupKey, function () {
            $dot = Setting::all()->flatMap(fn(Setting $s) => ["{$s->group}.{$s->name}" => $s->val]);
            return  Setting::query()->pluck('val', 'name')->merge($dot);
        });
    }

    /**
     * Get all settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function group(bool $fresh = false): Collection
    {
        if ($fresh) Cache::forget(static::$groupKey);
        return Cache::rememberForever(static::$cacheKey, function () {
            $dot = Setting::all()->groupBy('group')->flatMap(function (Setting $s) {
                return ["{$s->group}.{$s->name}" => $s->val];
            });
            return  Setting::query()->pluck('val', 'name')->merge($dot);
        });
    }

    /**
     * Get all group settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function for(string|array $group): Collection
    {
        if (is_array($group)) {
            $settings = Setting::query()->whereIn('group', $group)->get();
            return $settings->groupBy('group')->mapWithKeys(function (Collection $list, $grp) {
                return [$grp => $list->flatMap(function (Setting $s) {
                    return [$s->name => $s->val];
                })];
            });
        }
        return Setting::query()
            ->where('group', $group)
            ->get()
            ->flatMap(function (Setting $s) {
                return [$s->name => $s->val];
            });
    }

    /**
     * Get all group settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function json($group)
    {
        return Setting::query()->where('group', $group)->pluck('val', 'name');
    }

    /**
     * Get a setting from storage by key.
     *
     * @param  string  $key
     * @param  null  $default
     * @param  bool  $fresh
     */
    public static function get(string $key, $default = null, bool $fresh = false): mixed
    {
        $setting = static::all($fresh)->get($key, $default);
        return $setting;
    }

    /**
     * Save a setting in storage.
     *
     * @param string|array $key
     * @param mixed $val
     * @param string|null $cast
     * @return mixed
     */
    public static function set(string|array $key, mixed $val = null, ?string $cast = null): mixed
    {
        // Handle batch updates with an array
        if (is_array($key)) {
            Setting::query()
                ->upsert(
                    collect($key)->map(function ($value, $name) use ($cast) {
                        [$group, $name] = str($name)->contains('.') ? explode('.', $name, 2) : [null, $name];
                        $setting = Setting::query()
                            ->where('name', $name)
                            ->where('group', $group)
                            ->first();
                        $useCast = $setting?->cast ?? $cast;
                        return [
                            'name' => trim($name),
                            'group' => $group ? trim($group) : null,
                            'val' => $value,
                            ...($useCast ? ['cast' => $useCast] : [])
                        ];
                    })->all(),
                    uniqueBy: ['name', 'group'], // Ensure unique key constraint is met
                    update: ['val', 'cast'] // Fields to update
                );
            Cache::forget(static::$cacheKey);
            return true;
        }

        // Handle single key updates
        $group = null;
        if (str($key)->contains('.')) {
            [$group, $key] = explode('.', $key, 2);
        }
        $setting = Setting::query()
            ->where('name', $key)
            ->where(
                'group',
                $group
            )
            ->first();
        $useCast = $setting?->cast ?? $cast;
        Setting::query()
            ->updateOrCreate(
                ['name' => trim($key), 'group' => $group ? trim($group) : null],
                [
                    'val' => $val,
                    ...($useCast ? ['cast' => $useCast] : [])
                ]
            );

        Cache::forget(static::$cacheKey);
        return $val;
    }


    /**
     * Save a setting in storage.
     *
     * @param $key string|array
     * @param $val string|mixed
     */
    public static function setCast($key, $cast = null): void
    {
        Setting::query()->where('name', $key)->update(['cast' => $cast]);
    }

    /**
     * Check if setting with key exists.
     *
     * @param $key
     */
    public static function has($key): bool
    {
        return static::all()->has($key);
    }

    /**
     * Remove a setting from storage.
     *
     * @param $key
     */
    public static function remove($key): mixed
    {
        $deleted = Setting::query()
            ->where('name', $key)
            ->delete();
        Cache::forget(static::$cacheKey);
        return $deleted;
    }

    public static function refresh()
    {
        Cache::forget(static::$cacheKey);
        Cache::forget(static::$groupKey);
        Cache::flush(); //
    }
}
