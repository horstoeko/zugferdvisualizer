<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information; please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\renderer;

use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerCodelistTransform;

horstoeko\zugferd\codelists\ZugferdInvoiceType;
horstoeko\zugferd\codelists\ZugferdUnitCodes;

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

	#[\Override]
	public function transformDocTypeCode(string $code): string{
		switch($code){
			case ZugferdInvoiceType::INVOICE: return 'Invoice';
			case ZugferdInvoiceType::CORRECTION: return 'Corrected Invoice';
			case ZugferdInvoiceType::CREDITNOTE: return 'Credit note';
			case ZugferdInvoiceType::PREPAYMENTINVOICE: return 'Prepaid invoice';
			case ZugferdInvoiceType::SELFBILLEDINVOICE: return 'Self-billed invoice';
			case ZugferdInvoiceType::INSURERSINVOICE: return 'Insurance invoice';
			case ZugferdInvoiceType::HIREINVOICE: return 'Hire invoice';
			case ZugferdInvoiceType::PAYMENTVALUATION: return 'Building invoice';
			default: return 'Unknown: ' . $code;
		}
	}

	#[\Override]
	public function transformUnit(string $unitCode): string{
		switch($unitCode){
			case ZugferdUnitCodes::REC20_PIECE;
			case ZugferdUnitCodes::REC21_PIECE: return 'pc';
			case ZugferdUnitCodes::REC20_KILOGRAM: return 'kg';
			case ZugferdUnitCodes::REC20_LITRE: return 'l';
			case ZugferdUnitCodes::REC20_SQUARE_METRE: return 'm<sup>2</sup>';
			case ZugferdUnitCodes::REC20_ONE: return '';
			case ZugferdUnitCodes::REC20_DAY: return 'day(s)';
			case ZugferdUnitCodes::REC20_MONTH: return 'month(s)';
			case ZugferdUnitCodes::REC20_YEAR: return 'year(s)';
			case ZugferdUnitCodes::REC20_KILOWATT_HOUR: return 'kW/h';
			case ZugferdUnitCodes::REC20_HOUR: return 'hour(s)';
			default: return '??(' . $unitCode . ')';
		}
	}

	#[\Override]
	public function transformPayment(string $payment): string{
		switch($payment){
			case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_30;
			case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_58: return 'bank transfer';
			case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_59: return 'direct debit';
			default: return '??(' . $payment . ')';
		}
	}

	#[\Override]
	public function formatCurrency(float $number, string $currency): string{
		return number_format($number, 2) . '&nbsp;' . self::transformCurrenySymbol($currency);
	}

	#[\Override]
	public function formatDate(?\DateTime $date): string{
		return $date ? ($date->format('Y-m-d') ?: '') : '';
	}

	public static function transformCurrenySymbol(string $currency): string{
		switch($currency){
			case 'EUR': return '&euro;';
			default: return$currency;
		}
	}

	#[\Override]
	public function getString(string $lookup): string{
		//we assume all lookups are already english
		return $lookup;
	}
}
