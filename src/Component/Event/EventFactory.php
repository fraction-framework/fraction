<?php

namespace Fraction\Component\Event;

use BackedEnum;
use Fraction\Component\Event\Enum\EventType;
use Fraction\Component\Event\EventTypes\Event;

class EventFactory {
  public function createEvent(EventType|BackedEnum|string $name, mixed $data = null): Event {
    if ($name instanceof BackedEnum && class_exists($name->value)) {
      return new $name->value($data);
    }

    return new Event($name->value ?? $name, $data);
  }
}