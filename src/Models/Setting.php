<?php

namespace Scriptoshi\Settings\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


class Setting extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';



    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'val',
        'group',
        'cast',
    ];


    protected function val(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => match ($attributes['cast']) {
                'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                'array' => json_decode($value, true),
                'datetime' => \Carbon\Carbon::parse($value),
                'int' => (int)$value,
                'float' => (float)$value,
                default => $value, // Return raw value for unsupported cast types.
            },
            set: function ($value) {
                return match ($this->cast) {
                    'boolean'  => $value ? 1 : 0,
                    'array'  => json_encode($value),
                    'datetime' => $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value,
                    default => (string) $value, // Store raw value for unsupported cast types.
                };
            },
        );
    }
}
