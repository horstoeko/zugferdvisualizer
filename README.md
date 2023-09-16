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
