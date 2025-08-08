<?php

require_once __DIR__ . '/vendor/autoload.php';

$configurator = new s9e\TextFormatter\Configurator;
$configurator->BBCodes->addFromRepository('B');
$configurator->BBCodes->addFromRepository('I');
$configurator->BBCodes->addFromRepository('URL');
$configurator->BBCodes->addFromRepository('QUOTE');
$configurator->BBCodes->addFromRepository('IMG');

$configurator->plugins->load('MediaEmbed');
$configurator->MediaEmbed->add('bandcamp');
$configurator->MediaEmbed->add('dailymotion');
$configurator->MediaEmbed->add('imgur');
$configurator->MediaEmbed->add('mixcloud');
$configurator->MediaEmbed->add('soundcloud');
$configurator->MediaEmbed->add('vimeo');
$configurator->MediaEmbed->add('youtube');

$configurator->Autolink;

$configurator->plugins->load('Autoimage');
$configurator->Autoimage->fileExtensions = ['avif', 'bmp', 'jpg', 'png', 'gif', 'webp', 'svg', 'tif', 'tiff'];

$configurator->plugins->load('HTMLEntities');

$configurator->rootRules->enableAutoLineBreaks();

// Get an instance of the parser and the renderer
extract($configurator->finalize());

if (!isset($_GET['text']) || !is_string($_GET['text']) || $_GET['text'] === '') {
    http_response_code(400);
    echo 'Bad Request: No text provided.';
    var_dump($_REQUEST);
    exit;
}

$text = filter_input(INPUT_GET, 'text');

if ($text) {
    $text = urldecode($text);
    try {
      $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8, ISO-8859-1');
      $text = str_replace("<t>", "", $text);
      $text = str_replace("</t>", "", $text);
      $xml  = $parser->parse($text);
      $html = $renderer->render($xml);
    } catch (Exception $e) {
      $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
      $text = str_replace("<t>", "", $text);
      $text = str_replace("</t>", "", $text);
      $xml  = $parser->parse($text);
      $html = $renderer->render($xml);
    }
} else {
    $html = sprintf('<p>S9ERROR : %s</p>', urldecode($_GET['text']));
}

echo html_entity_decode($html);