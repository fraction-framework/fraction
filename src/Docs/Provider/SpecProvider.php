<?php

namespace Fraction\Docs\Provider;

use Fraction\Component\Cache\CacheComponent;
use Fraction\Component\Config\ConfigManager;
use Fraction\Component\Reader;

abstract class SpecProvider {
  public function __construct(protected Reader $reader, protected ConfigManager $config, protected CacheComponent $cache) {
  }

  public function getSpec(): array {
    return $this->generateSpec();
  }

  abstract protected function generateSpec(): array;

}