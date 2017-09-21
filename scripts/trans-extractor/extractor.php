<?php

require 'TwigExtractor.php';
require 'TwigTranslationExtractorExtension.php';
require 'TranslationNodeVisitor.php';

$path = __DIR__ . '/../../web/themes/quartiers_solidaires/templates';
$locale = 'fr';

$twig = \Drupal::service('twig');
$twig->addExtension(new TwigTranslationExtractorExtension());

$extractor = new \TwigExtractor($twig);

$translations = $extractor->extract($path);

var_dump($translations);