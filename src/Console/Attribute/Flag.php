<?php

namespace Fraction\Console\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Flag {

  public function __construct(private string $name, private string $description, private bool $hasValue = false) {
  }

  /**
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @return bool
   */
  public function hasValue(): bool {
    return $this->hasValue;
  }
}