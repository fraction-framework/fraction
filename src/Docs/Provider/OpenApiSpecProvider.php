<?php

namespace Fraction\Docs\Provider;

use Fraction\Component\Cache\CacheEntity;
use Fraction\Component\Controller;
use Fraction\Http\Attribute\Error;
use Fraction\Http\Attribute\Parameter;
use Fraction\Http\Attribute\Route;
use Fraction\Http\Attribute\View;
use Fraction\Http\Enum\RequestMethod;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

class OpenApiSpecProvider extends SpecProvider {

  protected function generateSpec(): array {
    $controllers = $this->reader->retrieveFiles(Controller::class, $this->locator->getSourceDir(), CacheEntity::CONTROLLER);

    $openApiSpec = [
      'openapi' => '3.0.0',
      'info' => [
        'title' => $this->config->get('api.docs.title'),
        'version' => $this->config->get('api.docs.version'),
      ],
      'servers' => $this->config->get('api.docs.servers'),
    ];

    $openApiSpec['paths'] = array_reduce($controllers, fn($carry, $controller) => array_merge($carry, $this->parseController($controller)), []);

    return $openApiSpec;
  }

  private function convertPhpTypeToOpenApiType(string $phpType): string {
    $mapping = [
      'int' => 'integer',
      'float' => 'number',
      'bool' => 'boolean',
      'string' => 'string',
      'array' => 'array',
      'object' => 'object',
    ];

    return $mapping[$phpType] ?? 'string';
  }

  /**
   * @throws \ReflectionException
   */
  private function generatePropertySchema(ReflectionProperty $property, ?ReflectionType $propertyType): array {
    if (!$propertyType) {
      return ['type' => 'string'];
    }

    $propertyTypeName = $propertyType->getName();

    if ($propertyType->isBuiltin()) {
      return ['type' => $this->convertPhpTypeToOpenApiType($propertyTypeName)];
    } else {
      return $this->generateResponseSchema($propertyTypeName);
    }
  }

  /**
   * @throws \ReflectionException
   */
  private function generateResponseSchema(string $resource): array {
    $schema = [
      'type' => 'object',
      'properties' => [],
    ];

    $refResource = new ReflectionClass($resource);

    foreach ($refResource->getProperties() as $property) {
      $propertyName = $property->getName();
      $propertyType = $property->getType();

      $schema['properties'][$propertyName] = $this->generatePropertySchema($property, $propertyType);
    }

    return $schema;
  }

  /**
   * @throws \ReflectionException
   */
  private function parseController(string $controller): array {
    $paths = [];

    $refClass = new ReflectionClass($controller);

    foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
      $route = $method->getAttributes(Route::class)[0] ?? null;

      if ($route) {
        $routeInstance = $route->newInstance();
        $path = $routeInstance->getPath();
        $httpMethod = strtolower($routeInstance->getMethodValue());

        $parameters = [];
        $paramsAttributes = $method->getAttributes(Parameter::class);

        foreach ($paramsAttributes as $paramAttribute) {
          $paramInstance = $paramAttribute->newInstance();

          $schema = [
            'type' => $this->convertPhpTypeToOpenApiType($paramInstance->getTypeValue()),
          ];

          if ($default = $paramInstance->getDefault()) {
            $schema['default'] = $default;
          }

          if ($pattern = $paramInstance->getPattern()) {
            $schema['pattern'] = $pattern;
          }


          $parameters[] = [
            'name' => $paramInstance->getName(),
            'in' => $routeInstance->getMethod() === RequestMethod::GET ? 'query' : 'body',
            'required' => $paramInstance->isRequired(),
            'description' => $paramInstance->getDescription(),
            'schema' => $schema
          ];
        }

        $operation = [
          'summary' => $routeInstance->description,
          'operationId' => $method->getName(),
          'parameters' => $parameters,
        ];

        // Add response object for 200 OK status
        $view = $method->getAttributes(View::class)[0] ?? null;

        if ($view) {
          $viewInstance = $view->newInstance();
          $responseResource = $viewInstance->getResource();
          $responseType = $viewInstance->getResponseTypeValue();

          if (!$responseResource || !$responseType) {
            continue;
          }

          $responseSchema = $this->generateResponseSchema($responseResource);
          $operation['responses'] = [
            '200' => [
              'description' => 'OK',
              'content' => [
                $responseType => [
                  'schema' => $responseSchema,
                ],
              ],
            ],
          ];

          // Add response objects for other status codes
          $errors = $method->getAttributes(Error::class);
          foreach ($errors as $error) {
            $errorInstance = $error->newInstance();
            $errorResource = $errorInstance->getReference();
            $errorStatus = $errorInstance->getStatusCode();

            if (!$errorResource) {
              continue;
            }

            $errorSchema = $this->generateResponseSchema($errorResource);
            $operation['responses'][$errorStatus] = [
              'description' => $errorInstance->getStatusMessage(),
              'content' => [
                $responseType => [
                  'schema' => $errorSchema,
                ],
              ],
            ];
          }

          $paths[$path][$httpMethod] = $operation;
        }
      }
    }

    return $paths;
  }
}