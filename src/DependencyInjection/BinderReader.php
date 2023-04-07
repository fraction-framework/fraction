<?php

namespace Fraction\DependencyInjection;

use Fraction\Component\Cache\CacheEntity;
use Fraction\Component\Reader;

readonly class BinderReader {

  public function __construct(private Reader $reader) {
  }

  public function getBinders(): array {
    $files = $this->reader->getClasses(Binder::class, CacheEntity::BINDER);

    return array_reduce($files, fn($carry, $class) => [...$carry, new $class()], []);
  }
}