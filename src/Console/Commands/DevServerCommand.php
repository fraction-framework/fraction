<?php

namespace Fraction\Console\Commands;

use Fraction\Component\Locator;
use Fraction\Console\Attribute\Command;
use Fraction\Console\Attribute\Flag;
use Fraction\Console\ConsoleInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

#[Command(name: 'server', description: 'Development server')]
readonly class DevServerCommand {

  public function __construct(private Locator $locator) {
  }

  #[Command(name: 'start', description: 'Start development server')]
  #[Flag(name: 'p', description: 'Port', hasValue: true)]
  public function start(ConsoleInterface $console): void {
    $port = $console->getFlag('p') ?? 8080;

    $srcDir = $this->locator->getSourceDir();
    $projectDir = realpath($srcDir . '/../');
    $publicDir = realpath($srcDir . '/../public');

    $serverCommand = "php -S localhost:{$port}";

    // Kill any existing PHP servers
    exec("pkill -f '{$serverCommand}'");

    // Start the PHP server
    $pipesServer = [];
    $descriptorSpec = [
      ["pipe", "r"],
      ["pipe", "w"],
      ["pipe", "w"]
    ];
    $server = proc_open($serverCommand, $descriptorSpec, $pipesServer, $publicDir);

    stream_set_blocking($pipesServer[2], false);

    $lastUpdate = time();
    $dirIterator = new RecursiveDirectoryIterator($srcDir);
    $iterator = new RecursiveIteratorIterator($dirIterator);

    $pipesCache = [];
    while (true) {
      if (isset($pipesServer[2]) && $output = stream_get_contents($pipesServer[2])) {
        $console->write($output);
      }

      if (isset($pipesCache[1]) && $output = stream_get_contents($pipesCache[1])) {
        $console->write($output);
      }

      foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile() && $lastUpdate < $fileinfo->getMTime()) {
          $lastUpdate = time();
          $cache = proc_open("{$projectDir}/fraction cache.clear -a", $descriptorSpec, $pipesCache);
          stream_set_blocking($pipesCache[1], false);
        }
      }

      usleep(1000);
    }
  }
}