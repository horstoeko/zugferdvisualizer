<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\translators;

use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerTranslatorContract;

/**
 * Class representing the translator for Laravel environments
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizerLaravelTranslator implements ZugferdVisualizerTranslatorContract
{
    /**
     * The currently selected language
     *
     * @var string
     */
    private $currentLanguage = 'en-US';

    /**
     * Sets the current language.
     *
     * @param  string $language
     * @return ZugferdVisualizerLaravelTranslator
     * @throws InvalidArgumentException
     */
    public function setCurrentLanguage(string $language): ZugferdVisualizerLaravelTranslator
    {
        $this->currentLanguage = $language;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function translate(string $key, array $placeHolders = [], ?string $domain = null): string
    {
        if (!function_exists("trans")) {
            return $key;
        }

        if ($domain) {
            $key = rtrim(ltrim($domain, ". \t\n\r\0\x0B"), ". \t\n\r\0\x0B") . "." . rtrim(ltrim($key, ". \t\n\r\0\x0B"), ". \t\n\r\0\x0B");
        }

        return call_user_func("trans", $key, $placeHolders, $this->currentLanguage);
    }
}
