<?php

namespace Fraction\Component\Event\Attribute;

use Attribute;
use BackedEnum;
use Fraction\Component\Event\Enum\EventType;

#[Attribute(Attribute::TARGET_METHOD)]
class EventListener {

  /**
   * @param EventType|BackedEnum|string $eventType
   */
  public function __construct(private readonly EventType|BackedEnum|string $eventType) {
  }

  /**
   * @return EventType|BackedEnum|string
   */
  public function getEventType(): EventType|BackedEnum|string {
    return $this->eventType;
  }
}