<?php

namespace Fraction\DependencyInjection;

use Fraction\Component\Config\EnvironmentParameterBag;
use Fraction\Component\Locator;

abstract class Binder {

  abstract public function configure(EnvironmentParameterBag $environmentParameterBag, Locator $locator): object;

  abstract public function getClassName(): string;
}