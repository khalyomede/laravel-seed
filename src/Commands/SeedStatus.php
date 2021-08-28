<?php

namespace Khalyomede\LaravelSeed\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Khalyomede\LaravelSeed\Seeder;
use Khalyomede\LaravelSeed\Traits\CapableOfLookingForSeeds;

class SeedStatus extends Command
{
    use CapableOfLookingForSeeds;

    protected $signature = "seed:status";
    protected $description = "Create a new seeder.";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $seedFileNamesAndStatuses = $this->getSeedFileNamesAndStatuses();

        $this->table(["file", "status"], $seedFileNamesAndStatuses);
        $this->line("");
        $this->line("{$seedFileNamesAndStatuses->count()} row(s) displayed.");
    }

    /**
     * @return Collection<array>
     */
    private function getSeedFileNamesAndStatuses(): Collection
    {
        $seedFileNamesAndStatuses = collect();
        $seedFileNamesOnDisk = $this->getSeedFileNames();
        $seedFileNamesInTable = $this->getSeedFileNamesInTable();

        foreach ($seedFileNamesOnDisk as $seedFileNameOnDisk) {
            $seedFileNamesAndStatuses->push([
                "file" => $seedFileNameOnDisk,
                "status" => $seedFileNamesInTable->contains($seedFileNameOnDisk) ? "ran" : "not ran",
            ]);
        }

        foreach ($seedFileNamesInTable as $seedFileNameOnTable) {
            if (!$seedFileNamesOnDisk->contains($seedFileNameOnTable)) {
                $seedFileNamesAndStatuses->push([
                    "file" => $seedFileNameOnTable,
                    "status" => "deleted from disk",
                ]);
            }
        }

        return $seedFileNamesAndStatuses;
    }

    /**
     * @return Collection<string>
     */
    private function getSeedFileNamesInTable(): Collection
    {
        /**
         * @phpstan-ignore-next-line Call to an undefined static method Khalyomede\LaravelSeed\Seeder::pluck()
         */
        return Seeder::pluck("seeder");
    }
}
