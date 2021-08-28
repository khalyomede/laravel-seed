<?php

namespace Khalyomede\LaravelSeed;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property int $id
 * @property string $seeder
 * @property int $batch
 */
class Seeder extends Model
{
    public function scopeMatchingSeederFileName(Builder $query, string $fileName): Builder
    {
        return $query->where("seeder", $fileName);
    }

    public function scopeMatchingBatchNumber(Builder $query, int $number): Builder
    {
        return $query->where("batch", $number);
    }

    public function scopeInReverseOrder(Builder $query): QueryBuilder
    {
        return $query->orderBy("seeder", "desc");
    }

    public static function getNextBatchNumber(): int
    {
        /**
         * @phpstan-ignore-next-line Call to an undefined static method Khalyomede\LaravelSeed\Seeder::max()
         */
        return self::max("batch") + 1;
    }

    /**
     * @return void
     */
    public static function forget(string $seeder)
    {
        /**
         * @phpstan-ignore-next-line Call to an undefined static method Khalyomede\LaravelSeed\Seeder::where()
         */
        self::where("seeder", $seeder)->delete();
    }

    public static function getBatchNumberFromSeederFileName(string $fileName): int
    {
        /**
         * @phpstan-ignore-next-line Call to an undefined static method Khalyomede\LaravelSeed\Seeder::matchingSeederFileName()
         */
        return self::matchingSeederFileName($fileName)->firstOrFail()->batch;
    }
}
