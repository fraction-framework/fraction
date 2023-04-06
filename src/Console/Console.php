<?php

namespace Fraction\Console;

use Fraction\Component\Config\ConfigManager;
use Fraction\Console\Commands\CacheCommand;
use Fraction\DependencyInjection\Container;
use Fraction\DependencyInjection\ContainerInterface;
use JetBrains\PhpStorm\NoReturn;

class Console implements ConsoleInterface {
  /**
   * @var array
   */
  private array $args = [];
  /**
   * @var CommandMetadata[]
   */
  private array $commands = [];
  /**
   * @var array
   */
  private array $flags = [];

  /**
   * @param ContainerInterface $container
   */
  public function __construct(private readonly ContainerInterface $container) {

    $this->registerCommandFromArray([
      CacheCommand::class,
    ]);
  }

  /**
   * @param $argv
   * @return static
   */
  public static function fromArgs($argv): self {
    $container = Container::create([]);
    $container->registerComponents($container->get(ConfigManager::class)->get('components'));

    $console = new self($container);
    try {
      $console->run($argv);
    } catch (\Throwable $e) {
      $console->printUsage($e->getMessage());
    }

    return $console;
  }

  /**
   * @return array
   */
  public function getArgs(): array {
    return $this->args;
  }

  /**
   * @param string $name
   * @return mixed
   */
  public function getFlag(string $name): mixed {
    return $this->flags[$name] ?? null;
  }

  /**
   * @return array
   */
  public function getFlags(): array {
    return $this->flags;
  }

  /**
   * @param string $name
   * @return bool
   */
  public function hasFlag(string $name): bool {
    return isset($this->flags[$name]);
  }

  /**
   * @param string $commandName
   * @return void
   */
  #[NoReturn] public function printCommandUsage(string $commandName): void {
    if (!isset($this->commands[$commandName])) {
      $this->writeln("Command not found: {$commandName}");
      exit(1);
    }

    $command = $this->commands[$commandName];
    $this->writeln("Description: {$command->getDescription()}");
    $this->writeln("Flags:");
    $flags = $command->getFlags();
    foreach ($flags as $flag) {
      $this->writeln("  -{$flag->getName()}  {$flag->getDescription()}");
    }
    exit(1);
  }

  /**
   * @param string $commandClass
   * @return void
   */
  public function registerCommand(string $commandClass): void {
    try {
      $this->commands = array_merge($this->commands, CommandMetadata::fromCommandClass($commandClass));
    } catch (\Throwable $e) {
      $this->writeln("Error registering command: {$e->getMessage()}");
    }
  }

  /**
   * @param array $commandClasses
   * @return void
   */
  public function registerCommandFromArray(array $commandClasses): void {
    foreach ($commandClasses as $commandClass) {
      $this->registerCommand($commandClass);
    }
  }

  /**
   * @param $argv
   * @return void
   */
  public function run($argv): void {
    if (count($argv) < 2) {
      $this->printUsage();
    }

    $commandName = $argv[1];
    $args = array_slice($argv, 2);

    if (!isset($this->commands[$commandName])) {
      $this->printUsage("Unknown command: {$commandName}");
    }

    $command = $this->commands[$commandName];
    $commandInstance = $this->container->get($command->getClassName());

    $availableFlags = $command->getFlags();
    $flags = [];
    $remainingArgs = [];
    while ($args) {
      $arg = array_shift($args);

      if (!str_starts_with($arg, '-')) {
        $remainingArgs[] = $arg;
        continue;
      }

      $flagName = substr($arg, 1);
      if (!isset($availableFlags[$flagName])) {
        $this->printUsage("Unknown flag: `{$arg}`");
      }

      $flag = $availableFlags[$flagName];
      if (!$flag->hasValue()) {
        $flags[$flagName] = true;
        continue;
      }

      $flagValue = array_shift($args);
      if ($flagValue === null) {
        $this->printUsage("Flag `{$arg}` requires a value.");
      }
      $flags[$flagName] = $flagValue;
    }


    $this->args = $remainingArgs;
    $this->flags = $flags;
    $commandInstance->{$command->getMethodName()}($this);
  }

  /**
   * @param string $message
   * @return void
   */
  public function write(string $message): void {
    echo $message;
  }

  /**
   * @param string $message
   * @return void
   */
  public function writeln(string $message): void {
    echo $message . PHP_EOL;
  }

  /**
   * @param string|null $message
   * @return void
   */
  private function printUsage(string $message = null): void {
    if ($message) {
      $this->writeln($message);
    }

    $this->writeln("Usage: {$GLOBALS['argv'][0]} <command> [args]");
    if (empty($this->commands)) {
      $this->writeln("No commands registered.");
      return;
    }


    $this->writeln("Available commands:");
    foreach ($this->commands as $command) {
      $this->writeln("  {$command->getCommandName()} - {$command->getDescription()}");

      $flags = $command->getFlags();
      foreach ($flags as $flag) {
        $this->writeln("    -{$flag->getName()}  {$flag->getDescription()}");
      }
    }

    exit(1);
  }
}