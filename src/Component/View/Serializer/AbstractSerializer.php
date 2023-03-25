<?php

namespace Fraction\Component\View\Serializer;

abstract class AbstractSerializer {
  public static function create(string $className): static {
    $class = new $className();
    if (!($class instanceof static)) {
      throw new \InvalidArgumentException('Class must be instance of ' . static::class);
    }
    return $class;
  }
  abstract public function serialize($data): string;
}
