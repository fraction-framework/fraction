<?php

namespace Fraction\Component\View\Serializer;

class JsonSerializer extends AbstractSerializer {
  public function serialize($data): string {
    return json_encode($data);
  }
}
