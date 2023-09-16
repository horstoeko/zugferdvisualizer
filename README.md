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
    - [Visualize an existing invoice document (XML)](#visualize-an-existing-invoice-document-xml)
    - [Create own render](#create-own-render)

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

### Visualize an existing invoice document (XML)

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

### Create own render

If you want to implement your own markup renderer, then your class must implement the interface `ZugferdVisualizerMarkupRendererContract`. Das Interface definiert zwei Methoden

* `templateExists`
* `render`

```php
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