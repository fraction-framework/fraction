<?php

namespace Fraction\Component\Routing;

use Fraction\Http\Attribute\Parameter;
use Fraction\Http\Enum\RequestMethod;
use Fraction\Http\Request;
use Fraction\Throwable\BadRequestException;

/**
 *
 */
class Route {
  /**
   * @var array|null
   */
  private ?array $paramTemplates = [];
  /**
   * @var array|null
   */
  private ?array $params = [];

  /**
   * @param RequestMethod $method
   * @param string $path
   * @param string $controller
   * @param string $action
   * @param array|null $paramTemplate
   */
  private function __construct(
    private readonly RequestMethod $method,
    private readonly string        $path,
    private readonly string        $controller,
    private readonly string        $action,
    ?array                         $paramTemplate = []
  ) {
    $this->paramTemplates = $this->validateParams($paramTemplate);
  }

  /**
   * @param RequestMethod $method
   * @param string $path
   * @param string $controller
   * @param string $action
   * @param array|null $paramTemplate
   * @return static
   */
  public static function create(RequestMethod $method, string $path, string $controller, string $action, ?array $paramTemplate = []): static {
    return new static($method, $path, $controller, $action, $paramTemplate);
  }

  /**
   * @return string
   */
  public function getAction(): string {
    return $this->action;
  }

  /**
   * @param string $attribute
   * @return \ReflectionAttribute|null
   * @throws \ReflectionException
   */
  public function getAttribute(string $attribute): null|\ReflectionAttribute {
    $attributes = $this->getAttributes($attribute);
    return $attributes === null ? null : $attributes[0];
  }

  /**
   * @param string $attribute
   * @return array|null
   * @throws \ReflectionException
   */
  public function getAttributes(string $attribute): null|array {
    $reflection = new \ReflectionClass($this->getController());
    $method = $reflection->getMethod($this->getAction());
    $attributes = $method->getAttributes($attribute);

    return empty($attributes) ? null : $attributes;
  }

  /**
   * @return string
   */
  public function getController(): string {
    return $this->controller;
  }

  /**
   * @return string
   */
  public function getMethod(): string {
    return $this->method->value;
  }

  /**
   * @param string $param
   * @return string|null
   */
  public function getParam(string $param): ?string {
    return $this->params[$param] ?? null;
  }

  /**
   * @param $name
   * @return string|null
   */
  public function getParamTemplate($name): ?string {
    return $this->paramTemplates[$name];
  }

  /**
   * @return array|null
   */
  public function getParamTemplates(): ?array {
    return $this->paramTemplates;
  }

  /**
   * @param array|null $paramTemplates
   */
  public function setParamTemplates(?array $paramTemplates): void {
    $this->paramTemplates = $paramTemplates;
  }

  /**
   * @return array|null
   */
  public function getParams(): ?array {
    return $this->params;
  }

  /**
   * @param array|null $params
   */
  public function setParams(?array $params): void {
    $this->params = $params;
  }

  /**
   * @return array|null
   */
  public function getParamsFromPath(): ?array {
    $params = [];
    foreach ($this->getPathSegments() as $segment) {
      if (preg_match('/\{(.*)\}/', $segment, $matches)) {
        $params[] = $matches[1];
      }
    }
    return $params;
  }

  /**
   * @return string
   */
  public function getPath(): string {
    return $this->path;
  }

  /**
   * @return array
   */
  public function getPathSegments(): array {
    return explode('/', ltrim($this->path, '/'));
  }

  /**
   * @throws \ReflectionException
   * @throws BadRequestException
   */
  public function validateParametersForRequest(Request $request): void {
    $paramAttributes = $this->getAttributes(Parameter::class);

    if ($paramAttributes === null) {
      return;
    }

    $errors = array_reduce(
      $paramAttributes,
      function ($carry, $attribute) use ($request) {
        $param = $attribute->newInstance();
        $value = $request->get($param->getName(), $param->getDefault());

        if ($value === null && $param->isRequired()) {
          $carry[$param->getName()] = 'This field is required';
        }

        if ($value !== null && !preg_match("/{$param->getPattern()}/", $value)) {
          $carry[$param->getName()] = "Doesn't match the pattern `{$param->getPattern()}`";
        }

        return $carry;
      },
      []
    );

    if (!empty($errors)) {
      throw new BadRequestException($errors);
    }
  }

  /**
   * @param array|null $paramTemplate
   * @return array|null
   */
  private function validateParams(?array $paramTemplate): ?array {
    $params = $this->getParamsFromPath();
    if ($params === null) {
      return null;
    }

    foreach ($params as $param) {
      if (!isset($paramTemplate[$param])) {
        $paramTemplate[$param] = '[^/]+';
      }
    }

    return $paramTemplate;
  }
}
