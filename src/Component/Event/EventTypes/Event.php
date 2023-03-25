<?php

namespace Fraction\Component\Event\EventTypes;

class Event {

  /**
   * @var mixed
   */
  private mixed $data;
  /**
   * @var string
   */
  private string $name;

  /**
   * @param string $name
   * @param array $data
   */
  public function __construct(string $name, mixed $data = []) {
    $this->name = $name;
    $this->data = $data;
  }

  /**
   * @return mixed
   */
  public function getData(): mixed {
    return $this->data;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }
}