<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\contracts;

use horstoeko\zugferd\ZugferdDocument;

/**
 * Interface representing the markup renderer contract
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
interface ZugferdVisualizerMarkupRendererContract
{
    /**
     * Returns true if the given template exists, otherwise false
     *
     * @param  string $template
     * @return boolean
     */
    public function templateExists(string $template): bool;

    /**
     * Render the HTML markup for the Zugferd document
     *
     * @param  ZugferdDocument $document
     * @param  string          $template
     * @return string
     */
    public function render(ZugferdDocument $document, string $template): string;
}
