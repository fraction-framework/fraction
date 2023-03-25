<?php

namespace Fraction\Component\Parser;

class Env extends AbstractParser {

  public function parse(string $input): array {
    $lines = preg_split('/\r\n|\r|\n/', $input);
    $result = [];

    foreach ($lines as $line) {
      // Ignore comments and empty lines
      if (preg_match('/^\s*(?:#.*|\/\/.*)?$/', $line)) {
        continue;
      }

      // Match key-value pairs
      if (preg_match('/^\s*([\w\.\-]+)\s*=\s*(.*)?\s*$/', $line, $matches)) {
        $key = $matches[1];
        $value = $matches[2] ?? '';

        // Remove quotes if present
        if (preg_match('/^([\'"])(.*)\1$/', $value, $quoted)) {
          $value = $quoted[2];
        }

        $result[$key] = $value;
      }
    }

    return $result;
  }
}