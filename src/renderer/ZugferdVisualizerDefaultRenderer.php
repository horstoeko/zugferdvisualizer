<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\renderer;

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerTranslatorContract;
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
    public function render(ZugferdDocumentReader $document, ZugferdVisualizerTranslatorContract $translator, string $template): string
    {
        ob_start();
        include $template;
        $markup = ob_get_clean();

        return $markup;
    }
}
