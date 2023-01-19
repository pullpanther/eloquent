<?php

namespace Kekalainen\EloquentConcurrency;

use Illuminate\Database\QueryException;

/**
 * Implements workarounds for race conditions.
 *
 * @link https://github.com/laravel/framework/issues/19372 Relevant issue.
 */
trait CanRunOrCreateConcurrently
{
    public static function firstOrCreate(array $attributes = [], array $values = [])
    {
        try {
            if (($instance = static::where($attributes)->first()) === null)
                return static::create(array_merge($attributes, $values));

            return $instance;
        } catch (QueryException $exception) {
            if (static::isDuplicateEntryException($exception))
                return static::where($attributes)->first();

            throw $exception;
        }
    }

    public static function updateOrCreate(array $attributes, array $values = [])
    {
        $updateOrCreate = static fn () => \tap(
            static::firstOrNew($attributes),
            static fn ($instance) => $instance->fill($values)->save()
        );

        try {
            return $updateOrCreate();
        } catch (QueryException $exception) {
            if (static::isDuplicateEntryException($exception))
                return $updateOrCreate();

            throw $exception;
        }
    }

    protected static function isDuplicateEntryException(QueryException $exception): bool
    {
        return (int) $exception->getCode() === 23000 &&
            $exception->errorInfo[1] === 1062;
    }
}
