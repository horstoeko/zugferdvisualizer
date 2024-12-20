# ZUGFeRD/XRechnung/Factur-X Visualizer

[![Latest Stable Version](https://img.shields.io/packagist/v/horstoeko/zugferdvisualizer.svg?style=plastic)](https://packagist.org/packages/horstoeko/zugferdvisualizer)
[![PHP version](https://img.shields.io/packagist/php-v/horstoeko/zugferdvisualizer.svg?style=plastic)](https://packagist.org/packages/horstoeko/zugferdvisualizer)
[![License](https://img.shields.io/packagist/l/horstoeko/zugferdvisualizer.svg?style=plastic)](https://packagist.org/packages/horstoeko/zugferdvisualizer)

[![Build Status](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.ci.yml/badge.svg)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.ci.yml)
[![Release Status](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.release.yml/badge.svg)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.release.yml)

## Table of Contents

- [ZUGFeRD/XRechnung/Factur-X Visualizer](#zugferdxrechnungfactur-x-visualizer)
  - [Table of Contents](#table-of-contents)
  - [License](#license)
  - [Overview](#overview)
  - [Dependencies](#dependencies)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Create HTML markup from existing invoice document (XML) using built-in template](#create-html-markup-from-existing-invoice-document-xml-using-built-in-template)
    - [Create a PDF file from existing invoice document (XML) using built-in template](#create-a-pdf-file-from-existing-invoice-document-xml-using-built-in-template)
    - [Create a PDF string from existing invoice document (XML) using built-in template](#create-a-pdf-string-from-existing-invoice-document-xml-using-built-in-template)
    - [Create a PDF string from document builder and merge XML with generated PDF](#create-a-pdf-string-from-document-builder-and-merge-xml-with-generated-pdf)
    - [Create a custom renderer](#create-a-custom-renderer)
    - [Use a custom renderer](#use-a-custom-renderer)
    - [Use the built-in Laravel renderer](#use-the-built-in-laravel-renderer)
    - [Set PDF-Options](#set-pdf-options)
      - [Set options before instanciating the internal PDF-Engine (```setPdfPreInitCallback```)](#set-options-before-instanciating-the-internal-pdf-engine-setpdfpreinitcallback)
      - [Set options after instanciating the internal PDF-Engine (```setPdfRuntimeInitCallback```)](#set-options-after-instanciating-the-internal-pdf-engine-setpdfruntimeinitcallback)
      - [Working with custom fonts](#working-with-custom-fonts)

## License

The code in this project is provided under the [MIT](https://opensource.org/licenses/MIT) license.

## Overview

With `horstoeko/zugferdvisualizer` you can visualize ZUGFeRD/XRechnung/Factur-X documents. This package is an addon for [horstoeko/zugferd](https://github.com/horstoeko/zugferd) package. The system uses a markup template (HTML) to render the output. On top you can create a PDF from the rendered markup

## Dependencies

This package makes use of

- [horstoeko/zugferd](https://github.com/horstoeko/zugferd)
- [mPdf](https://github.com/mpdf/mpdf)

## Installation

There is one recommended way to install `horstoeko/zugferdvisualizer` via [Composer](https://getcomposer.org/):

* adding the dependency to your ``composer.json`` file:

```js
  "require": {
      ..
      "horstoeko/zugferdvisualizer":"^1",
      ..
  },
```

## Usage

### Create HTML markup from existing invoice document (XML) using built-in template

```php
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

require dirname(__FILE__) . "/../vendor/autoload.php";

$document = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/invoice_1.xml");

$visualizer = new ZugferdVisualizer($document);
$visualizer->setDefaultTemplate();

echo $visualizer->renderMarkup();
```

### Create a PDF file from existing invoice document (XML) using built-in template

Find there [full example here](https://github.com/horstoeko/zugferdvisualizer/blob/master/examples/BuildFromDocumentReader.php)

```php
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

require dirname(__FILE__) . "/../vendor/autoload.php";

$document = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/invoice_1.xml");

$visualizer = new ZugferdVisualizer($document);
$visualizer->setDefaultTemplate();
$visualizer->setPdfFontDefault("courier");
$visualizer->renderPdfFile(dirname(__FILE__) . "/invoice_1.pdf");
```

### Create a PDF string from existing invoice document (XML) using built-in template

```php
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

require dirname(__FILE__) . "/../vendor/autoload.php";

$document = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/invoice_1.xml");

$visualizer = new ZugferdVisualizer($document);
$visualizer->setDefaultTemplate();
$visualizer->setPdfFontDefault("courier");

$pdfString = $visualizer->renderPdf();
```

### Create a PDF string from document builder and merge XML with generated PDF

Find there [full example here](https://github.com/horstoeko/zugferdvisualizer/blob/master/examples/BuildFromDocumentBuilder.php)

```php
$document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_EN16931);
$document
    ->setDocumentInformation("471102", "380", \DateTime::createFromFormat("Ymd", "20180305"), "EUR")
    ->...

$reader = ZugferdDocumentReader::readAndGuessFromContent($document->getContent());

$visualizer = new ZugferdVisualizer($reader);
$visualizer->setDefaultTemplate();
$visualizer->setPdfFontDefault("courier");
$visualizer->setPdfPaperSize('A4-P');

$merger = new ZugferdDocumentPdfMerger($document->getContent(), $visualizer->renderPdf());
$merger->generateDocument();
$merger->saveDocument(dirname(__FILE__) . "/invoice_2.pdf");
```

### Create a custom renderer

If you want to implement your own markup renderer, then your class must implement the interface `ZugferdVisualizerMarkupRendererContract`. The interface defines two methods:

* `templateExists`
* `render`

```php
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerTranslatorContract;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract;

class MyOwnRenderer implements ZugferdVisualizerMarkupRendererContract
{
    public function templateExists(string $template): bool
    {
        // Put your logic here
        // Method must return a boolean value
    }

    public function render(ZugferdDocumentReader $document, ZugferdVisualizerTranslatorContract $translator, string $template): string
    {
        // Put your logic here
        // Method must return a string (rendered HTML markup)
    }
}
```

### Use a custom renderer

```php
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

require dirname(__FILE__) . "/../vendor/autoload.php";

$document = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/invoice_1.xml");

$visualizer = new ZugferdVisualizer($document);
$visualizer->setRenderer(new MyOwnRenderer());
$visualizer->setTemplate('/assets/myowntemplate.tmpl');

echo $visualizer->renderMarkup();
```

### Use the built-in Laravel renderer

The ```ZugferdVisualizerLaravelRenderer``` can be used within the Laravel-Framework:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerLaravelRenderer;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

class ZugferdController extends Controller
{
    public function index(Request $request)
    {
        $document = ZugferdDocumentReader::readAndGuessFromFile(storage_path('app/invoice_1.xml'));

        $visualizer = new ZugferdVisualizer($document);
        $visualizer->setRenderer(app(ZugferdVisualizerLaravelRenderer::class));
        $visualizer->setTemplate('zugferd'); // ~/resources/views/zugferd.blade.php

        return $visualizer->renderMarkup();
    }

    public function download(Request $request)
    {
        $document = ZugferdDocumentReader::readAndGuessFromFile(storage_path('app/invoice_1.xml'));

        $visualizer = new ZugferdVisualizer($document);
        $visualizer->setRenderer(app(ZugferdVisualizerLaravelRenderer::class));
        $visualizer->setTemplate('zugferd');
        $visualizer->setPdfFontDefault("courier");
        $visualizer->setPdfPaperSize('A4-P');
        $visualizer->renderPdfFile(storage_path('app/invoice_1.pdf'));

        $headers = [
            'Content-Type: application/pdf',
        ];

        return response()->download(storage_path('app/invoice_1.pdf'), "invoice_1.pdf", $headers);
    }
}
```

### Set PDF-Options

If you want to make further settings to the internal PDF engine, then you can change further settings using a callback.
The usage is as follows:

#### Set options before instanciating the internal PDF-Engine (```setPdfPreInitCallback```)

```php
use horstoeko\zugferdvisualizer\ZugferdVisualizer;
use Mpdf\Mpdf;

$visualizer = new ZugferdVisualizer(static::$document);
$visualizer->setDefaultTemplate();
$visualizer->setPdfPreInitCallback(function (array $config, ZugferdVisualizer $visualizer) {
    $config["orientation"] = "L";
    return $config;
});
```

#### Set options after instanciating the internal PDF-Engine (```setPdfRuntimeInitCallback```)

```php
use horstoeko\zugferdvisualizer\ZugferdVisualizer;
use Mpdf\Mpdf;

$visualizer = new ZugferdVisualizer(static::$document);
$visualizer->setDefaultTemplate();
$visualizer->setPdfRuntimeInitCallback(function (Mpdf $mpdf, ZugferdVisualizer $visualizer) {
    $mpdf->pdf_version = "1.7";
});
```

#### Working with custom fonts

If you would like to use your own fonts, that's no problem at all. First you have to specify one or more directories in which your fonts are located:

```php
use horstoeko\zugferdvisualizer\ZugferdVisualizer;
use Mpdf\Mpdf;

$visualizer = new ZugferdVisualizer(static::$document);
$visualizer->addPdfFontDirectory('/var/fonts1/');
$visualizer->addPdfFontDirectory('/var/fonts2/');
```

Next, you need to define the font properties:

* The first parameter sets the name of the font-family
* Thé second parameter sets the type of the font
  * R - Regular
  * I - Italic
  * B - Bold
  * BI - Bold & Italic
* The third parameter sets the filename under which the font can be found in the specified font-directories

```php
$visualizer->addPdfFontData('comicsans', 'R', 'comic.ttf');
$visualizer->addPdfFontData('comicsans', 'I', 'comici.ttf');
```

If you want to set a custom font as the default font, you can use the following method:

```php
$visualizer->setPdfFontDefault("comicsans");
```

You can also use the name of the font family in the style attribute of any HTML elements in your template:

```html
<p style="font-family: comicsans">Text in Comic Sans</p>
```

For more configuration options, please consult the documentation of [mPdf](https://mpdf.github.io/configuration/configuration-v7-x.html)
