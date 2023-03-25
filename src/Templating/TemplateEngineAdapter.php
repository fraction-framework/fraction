<?php

namespace Fraction\Templating;

abstract class TemplateEngineAdapter {
  abstract public function render(string $template, array $data = []): string;
}