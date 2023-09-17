<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\renderer;

use horstoeko\zugferd\ZugferdDocument;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract;

/**
 * Class representing the default markup renderer
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizerDefaultRenderer implements ZugferdVisualizerMarkupRendererContract
{
    /**
     * @inheritDoc
     */
    public function templateExists(string $template): bool
    {
        return file_exists($template);
    }

    /**
     * @inheritDoc
     */
    public function render(ZugferdDocument $document, string $template): string
    {
        ob_start();
        include $template;
        $markup = ob_get_clean();

        if (false === $markup) {
            throw new \RuntimeException("Failed to render markup");
        }

        return $markup;
    }
}
