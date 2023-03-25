<?php

namespace Fraction\Component\Event\EventTypes;

use Fraction\Component\Event\Enum\EventType;
use Fraction\Component\Routing\Route;

class ControllerEvent extends Event {
  /**
   * @param ?Route $route
   */
  public function __construct(private readonly ?Route $route) {
    parent::__construct((EventType::Controller)->getValue());
  }

  /**
   * @return ?Route
   */
  public function getRoute(): ?Route {
    return $this->route;
  }
}