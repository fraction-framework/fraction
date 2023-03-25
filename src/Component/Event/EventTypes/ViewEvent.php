<?php

namespace Fraction\Component\Event\EventTypes;

use Fraction\Component\Event\Enum\EventType;
use Fraction\Component\View\ViewHandler;

class ViewEvent extends Event {

  /**
   * @param ViewHandler $viewHandler
   */
  public function __construct(private readonly ViewHandler $viewHandler) {
    parent::__construct((EventType::View)->getValue());
  }

  /**
   * @return ViewHandler
   */
  public function getViewHandler(): ViewHandler {
    return $this->viewHandler;
  }
}