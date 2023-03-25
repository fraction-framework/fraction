<?php

namespace Fraction\Templating\Adapters;

use Fraction\Templating\TemplateEngineAdapter;

class TwigAdapter extends TemplateEngineAdapter {


  public function __construct(private \Twig\Environment $twig) {

  }

  public function render(string $template, array $data = []): string {
    return $this->twig->render($template, $data);
  }
}