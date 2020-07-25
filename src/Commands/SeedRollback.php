<?php

namespace Khalyomede\LaravelSeed\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Khalyomede\LaravelSeed\Seeder;
use Khalyomede\LaravelSeed\Traits\CapableOfRollbackingSeeds;
use Khalyomede\LaravelSeed\Traits\CapableOfRunningSeeds;

class SeedRollback extends Command
{
    use CapableOfRunningSeeds, CapableOfRollbackingSeeds;

    protected $signature = "seed:rollback {--i|ignore-deleted : Don't raise errors if the rollbacked seed does not exist in disk.}";
    protected $description = "Rollback all the seeds.";
    private $seedFileName;

    public function __construct()
    {
        parent::__construct();

        $this->seedFileName = "";
    }

    public function handle()
    {
        $seedFileNames = $this->getSeedFileNames();
        $numberOfRollbackedSeeds = 0;
        $seedsRollbacked = [];
        $bar = $this->output->createProgressBar(count($seedFileNames));

        if ($seedFileNames->count() > 0) {
            $bar->start();
        }

        foreach ($seedFileNames as $seedFileName) {
            $this->seedFileName = $seedFileName;

            $this->rollbackSeed();
            $this->forgetSeed();

            $seedsRollbacked[] = [
                "file" => $seedFileName,
            ];
            $numberOfRollbackedSeeds++;
            $bar->advance();
        }

        if ($seedFileNames->count() > 0) {
            $bar->finish();
        }

        $this->line("\n");
        $this->table(["file"], $seedsRollbacked);
        $this->line("");
        $this->line("$numberOfRollbackedSeeds seed(s) rollbacked.");
    }

    private function getSeedFileNames(): Collection
    {
        return $this->getSeedFileNamesMatchingBatchNumber($this->getLastSeedBatchNumber());
    }

    private function forgetSeed()
    {
        Seeder::forget($this->seedFileName);
    }

    private function getLastSeedBatchNumber(): int
    {
        return Seeder::getBatchNumberFromSeederFileName($this->getLastSeederFileName());
    }

    private function getSeedFileNamesMatchingBatchNumber(int $batchNumber): Collection
    {
        return Seeder::matchingBatchNumber($batchNumber)->pluck("seeder");
    }

    private function getLastSeederFileName(): string
    {
        $lastSeeder = Seeder::latest("id")->first();

        if (!($lastSeeder instanceof Seeder)) {
            $this->error("No seeder ran yet, nothing to rollback.");

            exit(1);
        }

        return $lastSeeder->seeder;
    }
}
