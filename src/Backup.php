<?php

namespace Laravel\Backup;

use Illuminate\Console\Command;

class Backup extends Command
{

  protected $signature = 'p:backup';
  protected $description = 'Bacup for Pterodactyl';

  public function handle()
  {
    $this->backup();
  }

  private function backup()
  {
  }
}
