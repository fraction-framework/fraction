<?php

namespace Fraction\Http;

use Fraction\Http\Enum\ResponseStatus;

class Response {
  public function __construct(
    private readonly ResponseStatus $status = ResponseStatus::OK,
    private readonly string         $body = '',
    private readonly array          $headers = [],
  ) {
  }

  public function send(): void {
    http_response_code($this->status->value);

    foreach ($this->headers as $name => $value) {
      header("{$name}: {$value}");
    }

    echo $this->body;
  }
}