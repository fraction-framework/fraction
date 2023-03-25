<?php

namespace Fraction\Component\View\Serializer;

class PlainText extends AbstractSerializer {
  public function serialize($data): string {
    if (isset($data['message'])) {
      $message = $data['message'];

      if (isset($data['file'])) {
        $message .= " in {$data['file']}";
      }

      if (isset($data['line'])) {
        $message .= " on line {$data['line']}";
      }

      if (isset($data['errors'])) {
        $errors = array_map(fn($item, $key) => "[{$key}] {$item}", $data['errors'], array_keys($data['errors']));
        $message .= PHP_EOL . implode(PHP_EOL, $errors);
      }

      return $message;
    }

    return is_string($data) ? $data : json_encode($data);
  }
}
