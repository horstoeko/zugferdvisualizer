<?php

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

require dirname(__FILE__) . "/../vendor/autoload.php";

// Load from existing XML file

$document = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/invoice_1.xml");

// Run the visualizer using the default renderer

$visualizer = new ZugferdVisualizer($document);
$visualizer->setDefaultTemplate();
$visualizer->addPdfFontDirectory(dirname(__FILE__) . '/fonts/');
$visualizer->addPdfFontData('comicsans', 'R', 'comic.ttf');
$visualizer->addPdfFontData('comicsans', 'I', 'comici.ttf');
$visualizer->setPdfFontDefault("courier");
$visualizer->setPdfPaperSize('A4-P');
$visualizer->renderPdfFile(dirname(__FILE__) . "/invoice_1.pdf");
