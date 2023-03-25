<?php

namespace Fraction\DependencyInjection;

use Fraction\Component\Cache\CacheEntity;
use Fraction\Component\Locator;
use Fraction\Component\Reader;

readonly class BinderReader {

  public function __construct(private Locator $locator, private Reader $reader) {
  }

  public function getBinders(): array {
    $files = $this->reader->retrieveFiles(Binder::class, $this->locator->getSourceDir(), CacheEntity::BINDER);

    return array_reduce($files, fn($carry, $class) => [...$carry, new $class()], []);
  }
}