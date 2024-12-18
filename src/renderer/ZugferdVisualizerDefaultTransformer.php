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
use horstoeko\zugferd\codelists\ZugferdUnitCodes;

/**
 * Class representing the default transformer for strings
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   M. Krämer
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizerDefaultTransformer implements ZugferdVisualizerCodelistTransform{

	public static function transformDocTypeCode(string $code): string{
		return match($code){
			ZugferdInvoiceType::INVOICE => 'Invoice',
			ZugferdInvoiceType::CORRECTION => 'Corrected Invoice',
			ZugferdInvoiceType::CREDITNOTE => 'Credit note',
			ZugferdInvoiceType::PREPAYMENTINVOICE => 'Prepaid invoice',
			default => 'Unknown: ' . $code,
		};
	}

	public static function transformUnit(string $unitCode): string{
		return match($unitCode){
			ZugferdUnitCodes::REC20_PIECE,
			ZugferdUnitCodes::REC21_PIECE => 'pc',
			ZugferdUnitCodes::REC20_KILOGRAM => 'kg',
			ZugferdUnitCodes::REC20_LITRE => 'l',
			ZugferdUnitCodes::REC20_SQUARE_METRE => 'm<sup>2</sup>',
			ZugferdUnitCodes::REC20_ONE => '',
			default => '??(' . $unitCode . ')',
		};
	}
}
