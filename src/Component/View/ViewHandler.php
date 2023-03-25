<?php

namespace Fraction\Component\View;

use Fraction\Component\Config\ConfigManager;
use Fraction\Component\Routing\Route;
use Fraction\Component\View\Serializer\AbstractSerializer;
use Fraction\Component\View\Serializer\JsonSerializer;
use Fraction\Component\View\Serializer\PlainText;
use Fraction\Component\View\Serializer\XmlSerializer;
use Fraction\Http\Attribute\View;
use Fraction\Http\Enum\ResponseStatus;
use Fraction\Http\Enum\ResponseType;
use Fraction\Http\Response;
use Fraction\Templating\TemplateEngine;
use Fraction\Templating\TemplateEngineFactory;
use Fraction\Throwable\FractionException;
use Fraction\Throwable\NotFoundException;

class ViewHandler {
  private mixed $data;
  private ResponseStatus $responseStatus = ResponseStatus::OK;

  private ResponseType $responseType = ResponseType::PLAIN;
  private ?string $template;
  private TemplateEngine $templateEngine;
  private string $templatesPath;

  /**
   * @param Route $route
   * @param callable $callback
   * @return mixed
   * @throws NotFoundException
   * @throws \ReflectionException
   */
  public function forRoute(Route $route, callable $callback): static {
    $view = $route->getAttribute(View::class);

    if ($view) {
      $attributeInstance = $view->newInstance();
      $this->setTemplate($attributeInstance->getTemplate());
      $this->setResponseType($attributeInstance->getResponseType());
    }

    $response = $callback($route);
    $this->setData($response);

    return $this;
  }

  /**
   * @return ResponseType
   */
  public function getResponseType(): ResponseType {
    return $this->responseType;
  }

  /**
   * @param ResponseType $responseType
   */
  public function setResponseType(ResponseType $responseType): void {
    $this->responseType = $responseType;
  }

  /**
   * @param ConfigManager $configManager
   * @return $this
   */
  public function initializeFromConfig(ConfigManager $configManager): static {
    $this->setResponseType($configManager->get('view.response.format'));
    $this->setTemplateEngine($configManager->get('templating.engine'));
    $this->setTemplatesPath($configManager->get('templating.template_dir'));

    return $this;
  }

  /**
   * @return Response
   */
  public function render(): Response {
    $data = $this->data;

    if ($data instanceof Response) {
      return $data;
    }

    if ($this->getResponseType() === ResponseType::JSON) {
      $body = AbstractSerializer::create(JsonSerializer::class)->serialize($data);
      return new Response(status: $this->responseStatus, body: $body, headers: ['Content-Type' => 'application/json']);
    }

    if ($this->getResponseType() === ResponseType::XML) {
      $body = AbstractSerializer::create(XmlSerializer::class)->serialize($data);
      return new Response(status: $this->responseStatus, body: $body, headers: ['Content-Type' => 'application/xml']);
    }

    if ($this->getResponseType() === ResponseType::PLAIN) {
      $body = AbstractSerializer::create(PlainText::class)->serialize($data);
      return new Response(status: $this->responseStatus, body: $body, headers: ['Content-Type' => 'text/plain']);
    }

    if ($this->getResponseType() === ResponseType::HTML) {
      if (!file_exists($this->templatesPath)) {
        mkdir($this->templatesPath, 0755, true);
      }


      try {
        $templateEngine = TemplateEngineFactory::create($this->templateEngine, $this->templatesPath);
      } catch (FractionException $e) {
        return new Response(status: ResponseStatus::InternalServerError, body: 'Template engine not found');
      }
      $body = $templateEngine->render($this->template, $data);
      return new Response(status: $this->responseStatus, body: $body, headers: ['Content-Type' => 'text/html']);
    }

    return new Response(status: ResponseStatus::OK, body: $data);
  }

  /**
   * @param mixed $data
   * @param ResponseStatus $responseStatus
   */
  public function setData(mixed $data, ResponseStatus $responseStatus = ResponseStatus::OK): void {
    $this->data = $data;
    $this->responseStatus = $responseStatus;
  }

  /**
   * @param ?string $template
   */
  public function setTemplate(?string $template): void {
    $this->template = $template;
  }

  /**
   * @param TemplateEngine $templateEngine
   */
  public function setTemplateEngine(TemplateEngine $templateEngine): void {
    $this->templateEngine = $templateEngine;
  }

  /**
   * @param string $templatesPath
   */
  public function setTemplatesPath(string $templatesPath): void {
    $this->templatesPath = $templatesPath;
  }
}
