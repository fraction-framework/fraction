<?php

namespace Fraction\Http\Attribute;

use Attribute;
use Fraction\Http\Enum\ParameterType;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Parameter {
  private array $defaultFormatPatterns = [
    'string' => '[a-zA-Z0-9\.\-]+',
    'int' => '\d+',
    'float' => '\d+\.\d+',
    'bool' => 'true|false',
  ];

  public function __construct(
    private readonly string        $name,
    private readonly ParameterType $type = ParameterType::STRING,
    private readonly bool          $required = false,
    private readonly mixed         $default = null,
    private ?string                $pattern = null,
    private readonly ?string       $description = null
  ) {
    if ($this->pattern === null) {
      $this->pattern = $this->defaultFormatPatterns[$this->type->value];
    }
  }

  /**
   * @return mixed
   */
  public function getDefault(): mixed {
    return $this->default;
  }

  /**
   * @return string|null
   */
  public function getDescription(): ?string {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @return string|null
   */
  public function getPattern(): ?string {
    return $this->pattern;
  }

  /**
   * @return ParameterType
   */
  public function getType(): ParameterType {
    return $this->type;
  }

  /**
   * @return string
   */
  public function getTypeValue(): string {
    return $this->type->value;
  }

  /**
   * @return bool
   */
  public function isRequired(): bool {
    return $this->required;
  }
}
