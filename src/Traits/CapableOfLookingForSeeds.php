<?php

namespace Khalyomede\LaravelSeed\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait CapableOfLookingForSeeds
{
    private function getSeedFilePaths(): Collection
    {
        return collect(Storage::disk("seeders")->files());
    }

    private function getSeedFileNames(): Collection
    {
        return $this->getSeedFilePaths()->map(function ($path) {
            return preg_replace("/\.php$/", "", $path);
        });
    }
}
