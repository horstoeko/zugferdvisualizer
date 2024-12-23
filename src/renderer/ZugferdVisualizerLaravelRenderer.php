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
 * Class representing the markup renderer for Laravel environments (Blade engine)
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizerLaravelRenderer implements ZugferdVisualizerMarkupRendererContract
{
    /**
     * @inheritDoc
     */
    public function templateExists(string $template): bool
    {
        if (!function_exists("view")) {
            return false;
        }

        /**
         * @var \Illuminate\Contracts\View\Factory
         */
        $view = call_user_func_array("view", []);

        return $view->exists($template);
    }

    /**
     * @inheritDoc
     */
    public function render(ZugferdDocumentReader $document, ZugferdVisualizerTranslatorContract $translator, string $template): string
    {
        if (!function_exists("view")) {
            return "";
        }

        /**
         * @var \Illuminate\Contracts\View\View
         */
        $view = call_user_func_array("view", [$template, ["document" => $document, 'translator' => $translator]]);

        return $view->render();
    }
}
