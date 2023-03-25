<?php

namespace Fraction\DependencyInjection;

interface ContainerInterface {
  public function get(string $id): mixed;
  public function has(string $id): bool;
  public function set(string $id, callable|string $value): void;
}
