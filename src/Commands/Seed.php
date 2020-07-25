<?php

namespace Khalyomede\LaravelSeed\Commands;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Jawira\CaseConverter\Convert;
use Illuminate\Support\Collection;
use Khalyomede\LaravelSeed\Seeder;
use Khalyomede\LaravelSeed\Traits\CapableOfLookingForSeeds;

class Seed extends Command
{
    use CapableOfLookingForSeeds;

    protected $signature = "seed";
    protected $description = "Runs the seeders that have not been run yet.";
    private $seedFileName;
    private $batchNumber;

    public function __construct()
    {
        parent::__construct();

        $this->seedFileName = "";
        $this->batchNumber = 0;
    }

    public function handle()
    {
        $this->batchNumber = Seeder::getNextBatchNumber();

        $seedFileNames = $this->getSeedFiles();
        $numberOfSeedsRan = 0;
        $seeds = [];
        $bar = $this->output->createProgressBar(count($seedFileNames));

        if ($seedFileNames->count() > 0) {
            $bar->start();
        }

        foreach ($seedFileNames as $seedFileName) {
            $this->seedFileName = $seedFileName;

            $this->runSeeder();
            $this->rememberThatSeederHaveBeenRun();

            $seeds[] = [
                "file" => $this->seedFileName,
            ];
            $numberOfSeedsRan++;
            $bar->advance();
        }

        if ($seedFileNames->count() > 0) {
            $bar->finish();
        }

        $this->line("\n");
        $this->table(["file"], $seeds);
        $this->line("");
        $this->info("{$numberOfSeedsRan} seed(s) ran.");
    }

    private function getSeedFiles(): Collection
    {
        return $this->getSeedFileNames()->diff(Seeder::pluck("seeder"));
    }

    private function runSeeder()
    {
        include_once $this->getAbsoluteSeederFilePath();

        $className = $this->getSeederClassName();

        $instance = new $className;

        $instance->up();
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

    private function getAbsoluteSeederFilePath(): string
    {
        return database_path("seeders/{$this->seedFileName}.php");
    }

    private function rememberThatSeederHaveBeenRun()
    {
        Seeder::insert([
            "seeder" => $this->seedFileName,
            "batch" => $this->batchNumber,
        ]);
    }
}
