<?php

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

require __DIR__ . "/../vendor/autoload.php";

// Load from existing XML file

$document = ZugferdDocumentReader::readAndGuessFromFile(__DIR__ . "/invoice_2.xml");

// Run the visualizer using the default renderer

$visualizer = new ZugferdVisualizer($document);
$visualizer->setDefaultTemplate();
$visualizer->addPdfFontDirectory(__DIR__ . '/fonts/');
$visualizer->addPdfFontData('comicsans', 'R', 'comic.ttf');
$visualizer->addPdfFontData('comicsans', 'I', 'comici.ttf');
$visualizer->setPdfFontDefault("courier");
$visualizer->setPdfPaperSize('A4-P');
$visualizer->renderPdfFile(__DIR__ . "/invoice_2.pdf");
