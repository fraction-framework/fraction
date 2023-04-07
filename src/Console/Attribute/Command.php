<?php

namespace Fraction\Console\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Command {
  /**
   * @param string $name
   * @param string $description
   */
  public function __construct(
    private string $name,
    private string $description,
  ) {
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
}