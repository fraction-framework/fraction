<?php

namespace Fraction\Component\Event\Enum;

use Fraction\Component\Event\EventTypes\ControllerEvent;
use Fraction\Component\Event\EventTypes\Event;
use Fraction\Component\Event\EventTypes\ExceptionEvent;
use Fraction\Component\Event\EventTypes\RequestEvent;
use Fraction\Component\Event\EventTypes\ResponseEvent;
use Fraction\Component\Event\EventTypes\ViewEvent;

enum EventType: string {
  case Request = RequestEvent::class;
  case Response = ResponseEvent::class;
  case Controller = ControllerEvent::class;
  case View = ViewEvent::class;
  case Exception = ExceptionEvent::class;
  case Terminate = Event::class;

  public function getValue(): string {
    return $this->value;
  }
}
