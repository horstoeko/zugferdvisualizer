<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\contracts;

/**
 * Interface representing the translator contract
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
interface ZugferdVisualizerTranslatorContract
{
    /**
     * Translated a key with optional place holders.
     * The key can be specified in the path.to.translation 'to search nested arrays.
     *
     * @param  string      $key
     * @param  array       $placeholders
     * @param  string|null $domain
     * @return string
     */
    public function translate(string $key, array $placeholders = [], ?string $domain = null): string;
}
