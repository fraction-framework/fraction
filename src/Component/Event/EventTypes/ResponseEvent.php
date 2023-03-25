<?php

namespace Fraction\Component\Event\EventTypes;

use Fraction\Component\Event\Enum\EventType;
use Fraction\Http\Response;

class ResponseEvent extends Event {

  /**
   * @param Response $response
   */
  public function __construct(private readonly Response $response) {
    parent::__construct((EventType::Response)->getValue());
  }

  /**
   * @return Response
   */
  public function getResponse(): Response {
    return $this->response;
  }
}