<?php

namespace Fraction\Component\Routing\Provider;

use Fraction\Component\Locator;
use Fraction\Component\Reader;
use Fraction\Component\Routing\Route;

abstract class RoutingProvider {
  public function __construct(protected readonly Locator $locator, protected readonly Reader $reader) {
  }

  /**
   * @return Route[]
   */
  abstract public function fetchRoutes(array $controllers): array;
}
