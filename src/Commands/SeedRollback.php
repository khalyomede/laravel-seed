<?php

namespace Khalyomede\LaravelSeed\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Khalyomede\LaravelSeed\Seeder;
use Khalyomede\LaravelSeed\Traits\CapableOfRollbackingSeeds;
use Khalyomede\LaravelSeed\Traits\CapableOfRunningSeeds;

class SeedRollback extends Command
{
    use CapableOfRollbackingSeeds;
    use CapableOfRunningSeeds;

    protected $signature = "seed:rollback {--i|ignore-deleted : Don't raise errors if the rollbacked seed does not exist in disk.}";
    protected $description = "Rollback all the seeds.";

    /**
     * @var string
     */
    private $seedFileName;

    public function __construct()
    {
        parent::__construct();

        $this->seedFileName = "";
    }

    /**
     * @return void
     */
    public function handle()
    {
        $seedFileNames = $this->getSeedFileNamesInReverseOrder();
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

    /**
     * @return Collection<string>
     */
    private function getSeedFileNamesInReverseOrder(): Collection
    {
        return $this->getSeedFileNamesMatchingBatchNumber($this->getLastSeedBatchNumber());
    }

    /**
     * @return void
     */
    private function forgetSeed()
    {
        Seeder::forget($this->seedFileName);
    }

    private function getLastSeedBatchNumber(): int
    {
        return Seeder::getBatchNumberFromSeederFileName($this->getLastSeederFileName());
    }

    /**
     * @return Collection<string>
     */
    private function getSeedFileNamesMatchingBatchNumber(int $batchNumber): Collection
    {
        /**
         * @phpstan-ignore-next-line Call to an undefined static method Khalyomede\LaravelSeed\Seeder::matchingBatchNumber()
         */
        return Seeder::matchingBatchNumber($batchNumber)
            ->inReverseOrder()
            ->pluck("seeder");
    }

    private function getLastSeederFileName(): string
    {
        /**
         * @phpstan-ignore-next-line Call to an undefined static method Khalyomede\LaravelSeed\Seeder::latest()
         */
        $lastSeeder = Seeder::latest("id")->first();

        if (!($lastSeeder instanceof Seeder)) {
            $this->error("No seeder ran yet, nothing to rollback.");

            exit(1);
        }

        return $lastSeeder->seeder;
    }
}
