<?php

namespace Laravel\Backup;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class Backup extends Command
{

  protected $signature = 'p:backup';
  protected $description = 'Backup for Pterodactyl';

  public function handle()
  {
    $this->backup();
  }

  private function backup()
  {
  }
}
