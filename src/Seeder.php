<?php

namespace Khalyomede\LaravelSeed;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeInReverseOrder(Builder $query): Builder
    {
        return $query->orderBy("seeder", "desc");
    }

    public static function getNextBatchNumber(): int
    {
        return self::max("batch") + 1;
    }

    public static function forget(string $seeder)
    {
        self::where("seeder", $seeder)->delete();
    }

    public static function getBatchNumberFromSeederFileName(string $fileName): int
    {
        return self::matchingSeederFileName($fileName)->firstOrFail()->batch;
    }
}
