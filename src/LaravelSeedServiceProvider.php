<?php

namespace Khalyomede\LaravelSeed;

use Illuminate\Support\ServiceProvider;
use Khalyomede\LaravelSeed\Commands\Seed;
use Khalyomede\LaravelSeed\Commands\SeedMake;
use Khalyomede\LaravelSeed\Commands\SeedReset;
use Khalyomede\LaravelSeed\Commands\SeedStatus;
use Khalyomede\LaravelSeed\Commands\SeedRollback;

class LaravelSeedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerDisks();
        }
    }

    private function registerCommands()
    {
        $this->commands([
            Seed::class,
            SeedMake::class,
            SeedReset::class,
            SeedRollback::class,
            SeedStatus::class,
        ]);
    }

    private function registerDisks()
    {
        app()->config["filesystems.disks.seeders"] = [
            "driver" => "local",
            "root" => database_path("seeders"),
        ];
    }
}
