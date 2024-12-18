<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\renderer;

use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerCodelistTransform;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;

/**
 * Class representing the default transformer for strings
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   M. Krämer
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizerGermanTransformer extends ZugferdVisualizerDefaultTransformer{

	/**
	 * @param string $code
	 * @return string
	 */
	public static function transformDocTypeCode(string $code): string{
		return match($code){
			ZugferdInvoiceType::INVOICE => 'Rechnung',
			ZugferdInvoiceType::CORRECTION => 'Rechnungskorrektur',
			ZugferdInvoiceType::CREDITNOTE => 'Gutschrift',
			ZugferdInvoiceType::PREPAYMENTINVOICE => 'Prepaid invoice',
			default => 'Unbekannt: ' . $code,
		};
	}
}
