<?php

namespace Fraction\Http;

use Fraction\Http\Enum\RequestMethod;

class Request {
  private function __construct(
    private readonly RequestMethod $method,
    private readonly string        $path,
    public ParameterBag            $headers,
    public ParameterBag            $cookies,
    private readonly ParameterBag  $query,
    private readonly ParameterBag  $body,
    public ParameterBag            $files,
    public ParameterBag            $server
  ) {
  }

  /**
   * @return static
   */
  public static function createFromGlobals(): static {
    return new static(
      method: RequestMethod::from($_SERVER['REQUEST_METHOD']),
      path: strtok($_SERVER['REQUEST_URI'], '?'),
      headers: ParameterBag::createFormArray(getallheaders()),
      cookies: ParameterBag::createFormArray($_COOKIE),
      query: ParameterBag::createFormArray($_GET),
      body: ParameterBag::createFromStdIn(),
      files: ParameterBag::createFormArray($_FILES),
      server: ParameterBag::createFormArray($_SERVER)
    );
  }

  public function all(): array {
    if ($this->method === RequestMethod::GET) {
      return $this->query->all();
    }

    return $this->body->all();
  }

  /**
   * Returns the value of a parameter from the query or body.
   *
   * @param string $name
   * @param mixed|null $default
   * @return mixed
   */
  public function get(string $name, mixed $default = null): mixed {
    if ($this->method === RequestMethod::GET) {
      return $this->query->get($name, $default);
    }

    return $this->body->get($name, $default);
  }

  /**
   * @return string
   */
  public function getMethod(): string {
    return $this->method->value;
  }

  /**
   * @return string
   */
  public function getPath(): string {
    return $this->path;
  }
}
