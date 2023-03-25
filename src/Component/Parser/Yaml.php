<?php

namespace Fraction\Component\Parser;

class Yaml extends AbstractParser {

  public function parse(string $input): array {
    $lines = explode("\n", $input);
    return $this->parseLines($lines);
  }

  public function parseFile(string $file): array {
    return $this->parse(file_get_contents($file));
  }

  private function parseLines(array $lines, int $level = 0): array {
    $result = [];
    $currentKey = null;
    $currentIndent = null;

    for ($i = 0; $i < count($lines); $i++) {
      $line = $lines[$i];
      if (trim($line) === '' || $line[0] === '#') {
        continue;
      }

      $indent = 0;
      while (isset($line[$indent]) && $line[$indent] === ' ') {
        $indent++;
      }

      if ($currentIndent === null) {
        $currentIndent = $indent;
      }

      if ($indent === $currentIndent) {
        // if multiline array
        if (preg_match('/- (.+)/', $line, $matches)) {
          $leadingItem = $matches[1];

          if (strpos($leadingItem, ':')) {
            // if nested array
            $arrayLines = [$leadingItem];
            for ($j = $i + 1; $j < count($lines); $j++) {
              if (preg_match('/^ {' . ($indent + 1) . ',}(.+)/', $lines[$j], $matches)) {
                $arrayLines[] = $matches[1];
                $i++;
              } else {
                break;
              }
            }

            $parsedValue = $this->parseLines($arrayLines, $level + 1);
          } else {
            $parsedValue = $leadingItem;
          }

          if ($currentKey === null) {
            $result[] = $parsedValue;
          } else {
            $result[$currentKey][] = $parsedValue;
          }
        } else {
          $keyValuePair = explode(':', $line, 2);
          if (count($keyValuePair) === 2) {
            $currentKey = trim($keyValuePair[0]);
            $value = trim($keyValuePair[1]);

            // if one line array
            if (preg_match('/^\[(.+)]$/', $value, $matches)) {
              $value = explode(',', $matches[1]);
              $value = array_map(fn($item) => trim($item), $value);
            }

            // no value, check if next line is indented
            if ($value === '') {
              $subLines = [];
              while ($i + 1 < count($lines) && preg_match('/^ {' . ($indent + 1) . ',}/', $lines[$i + 1])) {
                $subLines[] = $lines[++$i];
              }
              $value = $this->parseLines($subLines, $level + 1);
            }

            $result[$currentKey] = $value;
          }
        }
      } elseif ($indent > $currentIndent && $currentKey !== null) {
        $subLines = [];
        while ($i < count($lines) && preg_match('/^ {' . ($currentIndent + 1) . ',}/', $lines[$i])) {
          $subLines[] = $lines[$i++];
        }
        $i--;
        $result[$currentKey] = $this->parseLines($subLines, $level + 1);
      }
    }

    return $result;
  }
}
