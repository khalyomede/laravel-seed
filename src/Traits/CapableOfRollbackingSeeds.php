<?php

namespace Khalyomede\LaravelSeed\Traits;

use Illuminate\Support\Facades\Storage;

trait CapableOfRollbackingSeeds
{
    use CapableOfRunningSeeds;

    private function rollbackSeed()
    {
        if (!$this->hasSeederInDisk() && $this->option("ignore-deleted") === null) {
            $this->line("\n");
            $this->error("Seeder {$this->seedFileName} does not exist in disk.  Use --ignore-deleted to skip this error message.");

            exit(1);
        }

        if (!$this->hasSeederInDisk() && $this->option("ignore-deleted") !== null) {
            return;
        }

        include $this->getAbsoluteSeederFilePath();

        $class = $this->getSeederClassName();

        $instance = new $class();

        $instance->down();
    }

    private function hasSeederInDisk(): bool
    {
        return Storage::disk("seeders")->exists("{$this->seedFileName}.php");
    }
}
