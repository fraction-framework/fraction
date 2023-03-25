<?php

namespace Fraction\Component\Config;

use Fraction\Component\Locator;
use Fraction\Component\Parser\Env;

class EnvironmentParameterBag {
  /**
   * @var array
   */
  private array $parameters = [];

  /**
   * @param Locator $locator
   * @param Env $envParser
   */
  public function __construct(
    private readonly Locator $locator,
    private readonly Env     $envParser
  ) {
    $this->load();
  }

  /**
   * @return array
   */
  public function all(): array {
    return $this->parameters;
  }

  /**
   * @param string $key
   * @param mixed|null $default
   * @return mixed
   */
  public function get(string $key, mixed $default = null): mixed {
    return $this->parameters[$key] ?? $default;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function has(string $key): bool {
    return isset($this->parameters[$key]);
  }

  /**
   * @return void
   */
  public function load(): void {
    $projectDir = $this->locator->getProjectRoot();
    $envFiles = [
      $projectDir . '/.env',
      $projectDir . '/.env.local',
    ];


    foreach ($envFiles as $envFile) {
      if (file_exists($envFile)) {
        $this->parameters = [...$this->parameters, ...$this->envParser->parse(file_get_contents($envFile))];
      }
    }
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function set(string $key, mixed $value): void {
    $this->parameters[$key] = $value;
  }
}