<?php

namespace Fraction\Component\Event\Enum;

enum EventType: string {
  case Request = 'request';
  case Response = 'response';
  case Controller = 'controller';
  case View = 'view';
  case Exception = 'exception';
  case Terminate = 'terminate';

  public function getValue(): string {
    return $this->value;
  }
}
