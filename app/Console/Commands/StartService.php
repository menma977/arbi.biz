<?php

namespace App\Console\Commands;

use BeyondCode\LaravelWebSockets\Console\Commands\StartServer as startThisService;

class StartService extends startThisService
{
  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->configureLoggers();

    $this->configureManagers();

    $this->configureStatistics();

    $this->configureRestartTimer();

    $this->configureRoutes();

    if (extension_loaded('pcntl')) {
      $this->configurePcntlSignal();
    }

    $this->configurePongTracker();

    $this->startServer();
  }
}
