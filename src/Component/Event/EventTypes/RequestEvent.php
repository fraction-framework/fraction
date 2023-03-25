<?php

namespace Fraction\Component\Event\EventTypes;

use Fraction\Component\Event\Enum\EventType;
use Fraction\Http\Request;

class RequestEvent extends Event {
  /**
   * @param Request $request
   */
  public function __construct(private readonly Request $request) {
    parent::__construct((EventType::Request)->getValue());
  }

  /**
   * @return Request
   */
  public function getRequest(): Request {
    return $this->request;
  }
}