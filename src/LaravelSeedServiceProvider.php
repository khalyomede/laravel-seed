<?php

namespace Khalyomede\LaravelSeed;

use Illuminate\Support\ServiceProvider;
use Khalyomede\LaravelSeed\Commands\Seed;
use Khalyomede\LaravelSeed\Commands\SeedMake;
use Khalyomede\LaravelSeed\Commands\SeedReset;
use Khalyomede\LaravelSeed\Commands\SeedRollback;
use Khalyomede\LaravelSeed\Commands\SeedStatus;

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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    private function registerDisks()
    {
        app()->config["filesystems.disks.seeders"] = [
            "driver" => "local",
            "root" => database_path("seeders"),
        ];
    }
}
