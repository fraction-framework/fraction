<?php

namespace Fraction\Component\Event\EventTypes;

use Fraction\Component\Event\Enum\EventType;

class ExceptionEvent extends Event {

  /**
   * @param \Throwable $exception
   */
  public function __construct(private readonly \Throwable $exception) {
    parent::__construct((EventType::Exception)->getValue());
  }

  /**
   * @return \Throwable
   */
  public function getException(): \Throwable {
    return $this->exception;
  }
}