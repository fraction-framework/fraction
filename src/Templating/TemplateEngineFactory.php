<?php

namespace Fraction\Templating;

use Fraction\Templating\Adapters\TwigAdapter;
use Fraction\Throwable\FractionException;

class TemplateEngineFactory {
  /**
   * @throws FractionException
   */
  public static function create(TemplateEngine $engine, string $templateDir): TemplateEngineAdapter {
    switch ($engine) {
      case TemplateEngine::TWIG:
        if (!class_exists('Twig\Environment')) {
          throw new FractionException("Twig is not installed.");
        }

        $loader = new \Twig\Loader\FilesystemLoader($templateDir);
        $twig = new \Twig\Environment($loader);
        return new TwigAdapter(twig: $twig);
      default:
        throw new FractionException("Template engine not supported yet.");
    }
  }
}