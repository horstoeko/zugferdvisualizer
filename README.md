# ZUGFeRD/XRechnung/Factur-X Visualizer

[![Latest Stable Version](https://poser.pugx.org/horstoeko/zugferdvisualizer/v/stable.png)](https://packagist.org/packages/horstoeko/zugferdvisualizer) [![Total Downloads](https://poser.pugx.org/horstoeko/zugferdvisualizer/downloads.png)](https://packagist.org/packages/horstoeko/zugferdvisualizer) [![Latest Unstable Version](https://poser.pugx.org/horstoeko/zugferdvisualizer/v/unstable.png)](https://packagist.org/packages/horstoeko/zugferdvisualizer) [![License](https://poser.pugx.org/horstoeko/zugferdvisualizer/license.png)](https://packagist.org/packages/horstoeko/zugferdvisualizer) [![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/horstoeko/zugferdvisualizer)

[![CI (Ant, PHP 7.3)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php73.ant.yml/badge.svg)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php73.ant.yml) [![CI (Ant, PHP 7.4)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php74.ant.yml/badge.svg)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php74.ant.yml) [![CI (PHP 8.0)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php80.ant.yml/badge.svg)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php80.ant.yml) [![CI (PHP 8.1)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php81.ant.yml/badge.svg)](https://github.com/horstoeko/zugferdvisualizer/actions/workflows/build.php81.ant.yml)

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
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract;

class MyOwnRenderer implements ZugferdVisualizerMarkupRendererContract
{
    public function templateExists(string $template): bool
    {
        // Put your logic here
        // Method must return a boolean value
    }

    public function render(ZugferdDocument $document, string $template): string
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