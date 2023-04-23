<?php

namespace Fraction\Http;

use Fraction\Component\Config\ConfigManager;

class Cors {

  private function __construct(
    private string $origin = '*',
    private string $methods = 'GET, POST, PUT, DELETE, OPTIONS',
    private string $headers = 'Content-Type, Authorization, X-Requested-With',
    private string $credentials = 'true',
    private int    $maxAge = 3600,
  ) {
  }

  public static function fromConfig(ConfigManager $config): self {

    return new self(
      ...array_map(
      fn($key) => $config->get("cors.{$key}"),
      ['Access-Control-Allow-Origin', 'Access-Control-Allow-Methods', 'Access-Control-Allow-Headers', 'Access-Control-Allow-Credentials', 'Access-Control-Max-Age']
    ));
  }

  public function send(Request $request): void {
    if ($request->getMethod() === 'OPTIONS') {
      header("Access-Control-Allow-Origin: {$this->origin}");
      header("Access-Control-Allow-Methods: {$this->methods}");
      header("Access-Control-Allow-Headers: {$this->headers}");
      header("Access-Control-Allow-Credentials: {$this->credentials}");
      header("Access-Control-Max-Age: {$this->maxAge}");

      http_response_code(204);
      exit;
    }
  }

}