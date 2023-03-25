<?php

namespace Fraction\Component\Event;

use BackedEnum;
use Fraction\Component\Event\Enum\EventType;
use Fraction\Component\Event\EventTypes\ControllerEvent;
use Fraction\Component\Event\EventTypes\Event;
use Fraction\Component\Event\EventTypes\ExceptionEvent;
use Fraction\Component\Event\EventTypes\RequestEvent;
use Fraction\Component\Event\EventTypes\ResponseEvent;
use Fraction\Component\Event\EventTypes\ViewEvent;

class EventFactory {
  public function createEvent(EventType|BackedEnum|string $name, mixed $data = null): Event {
    if (is_string($name)) {
      return new Event($name, $data);
    }

    return match ($name) {
      EventType::Request => new RequestEvent($data),
      EventType::Response => new ResponseEvent($data),
      EventType::Controller => new ControllerEvent($data),
      EventType::View => new ViewEvent($data),
      EventType::Exception => new ExceptionEvent($data),
      default => new Event($name->value, $data)
    };
  }
}