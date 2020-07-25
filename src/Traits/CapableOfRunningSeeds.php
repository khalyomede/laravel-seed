<?php

namespace Khalyomede\LaravelSeed\Traits;

use RuntimeException;
use Illuminate\Support\Str;
use Jawira\CaseConverter\Convert;

trait CapableOfRunningSeeds
{
    private function getAbsoluteSeederFilePath(): string
    {
        return database_path("seeders/{$this->seedFileName}.php");
    }

    private function getSeederClassName(): string
    {
        $matches = [];
        $succeeded = preg_match("/\d+_\d+_\d+_\d+_([\w_]+)$/", $this->seedFileName, $matches);

        if ($succeeded === false) {
            throw new RuntimeException("An error occured while trying to get the name of the seeder class");
        }

        if (count($matches) !== 2) {
            throw new RuntimeException("An error occured while trying to get the name of the seeder class");
        }

        return Str::plural((new Convert($matches[1]))->toPascal());
    }
}
