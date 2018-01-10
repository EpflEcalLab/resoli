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

// Write the .po file
$fh = fopen("../config/d8/lang/tmp.po", 'w');
fwrite($fh, "msgid \"\"\n");
fwrite($fh,  "msgstr \"\"\n\"Content-Type: text/plain; charset=UTF-8\\n\"\n\"Content-Transfer-Encoding: 8bit\\n\"\n");

foreach ($translations as $value) {
    $value = addslashes(trim($value));
    fwrite($fh, "\n");
    fwrite($fh, "msgid \"$value\"\n");
    fwrite($fh, "msgstr \"\"\n");
}
fclose($fh);
