<?php

namespace Laravel\Backup;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Backup\Backup;

class BackupServiceProvider extends ServiceProvider
{
  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->commands([Backup::class]);
    }

    $this->app->booted(function () {
      $schedule = app(Schedule::class);
      $schedule->command('p:backup')->->daily();
    });
  }
  public function register()
  {
  }
}
