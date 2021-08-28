<?php

namespace Khalyomede\LaravelSeed\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Khalyomede\LaravelSeed\Seeder;
use Khalyomede\LaravelSeed\Traits\CapableOfRollbackingSeeds;
use Khalyomede\LaravelSeed\Traits\CapableOfRunningSeeds;

class SeedReset extends Command
{
    use CapableOfRollbackingSeeds;
    use CapableOfRunningSeeds;

    protected $signature = "seed:reset {--i|ignore-deleted : Don't raise errors if the rollbacked seed does not exist in disk.}";
    protected $description = "Rollback all the seeds.";
    private $seedFileName;

    public function __construct()
    {
        parent::__construct();

        $this->seedFileName = "";
    }

    public function handle()
    {
        $seedFileNames = $this->getSeedsFileNamesInReverseOrder();
        $numberOfSeedsRollbacked = 0;
        $seeds = [];
        $bar = $this->output->createProgressBar(count($seedFileNames));

        if ($seedFileNames->count() > 0) {
            $bar->start();
        }

        foreach ($seedFileNames as $seedFileName) {
            $this->seedFileName = $seedFileName;

            $this->rollbackSeed();
            $this->forgetSeed();

            $seeds[] = [
                "file" => $this->seedFileName,
            ];
            $numberOfSeedsRollbacked++;
            $bar->advance();
        }

        if ($seedFileNames->count() > 0) {
            $bar->finish();
        }

        $this->line("\n");
        $this->table(["file"], $seeds);
        $this->line("");
        $this->info("$numberOfSeedsRollbacked seed(s) rollbacked.");
    }

    private function getSeedsFileNamesInReverseOrder(): Collection
    {
        return Seeder::inReverseOrder()->pluck("seeder");
    }

    private function forgetSeed()
    {
        Seeder::forget($this->seedFileName);
    }
}
