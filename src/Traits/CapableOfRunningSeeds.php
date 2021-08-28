<?php

namespace Khalyomede\LaravelSeed\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Jawira\CaseConverter\Convert;
use RuntimeException;

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

    private function createSeedersTableIfItDoesNotExistYet()
    {
        if (!Schema::hasTable("seeders")) {
            Schema::create("seeders", function (Blueprint $table) {
                $table->increments("id");
                $table->string("seeder");
                $table->bigInteger("batch");
            });
        }
    }
}
