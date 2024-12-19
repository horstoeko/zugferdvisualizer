<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\renderer;

use horstoeko\zugferd\codelists\ZugferdInvoiceType;

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
class ZugferdVisualizerGermanTransformer extends ZugferdVisualizerDefaultTransformer{

	/**
	 * @param string $code
	 * @return string
	 */
	#[\Override]
	public function transformDocTypeCode(string $code): string{
		switch($code){
			case ZugferdInvoiceType::INVOICE: return 'Rechnung';
			case ZugferdInvoiceType::CORRECTION: return 'Rechnungskorrektur';
			case ZugferdInvoiceType::CREDITNOTE: return 'Gutschrift';
			case ZugferdInvoiceType::PREPAYMENTINVOICE: return 'Vorrauszahlung';
			case ZugferdInvoiceType::SELFBILLEDINVOICE: return 'Gutschrift im Gutschriftverfahren';
			case ZugferdInvoiceType::INSURERSINVOICE: return 'Versicherungsrechnung';
			case ZugferdInvoiceType::HIREINVOICE: return 'Mietrechnung';
			case ZugferdInvoiceType::PAYMENTVALUATION: return 'Baurechnung';
			default: return 'Unbekannt: ' . $code;
		}
	}

	#[\Override]
	public function transformUnit(string $unitCode): string{
		switch($unitCode){
			case ZugferdUnitCodes::REC20_PIECE;
			case ZugferdUnitCodes::REC21_PIECE: return 'Stck';
			case ZugferdUnitCodes::REC20_DAY: return 'Tag(e)';
			case ZugferdUnitCodes::REC20_MONTH: return 'Monat(e)';
			case ZugferdUnitCodes::REC20_YEAR: return 'Jahr(e)';
			case ZugferdUnitCodes::REC20_HOUR: return 'Stunde(n)';
			default: return parent::transformUnit($unitCode);
		}
	}

	#[\Override]
	public function transformPayment(string $payment): string{
		switch($payment){
			case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_30;
			case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_58: return 'Überweisung';
			case \horstoeko\zugferd\codelists\ZugferdPaymentMeans::UNTDID_4461_59: return 'Lastschrift';
			default: return parent::transformPayment($payment);
		}
	}

	#[\Override]
	public function formatCurrency(float $number, string $currency): string{
		return number_format($number, 2, ',', '.') . '&nbsp;' . self::transformCurrenySymbol($currency);
	}

	#[\Override]
	public function formatDate(?\DateTime $date): string{
		return $date ? ($date->format('d.m.Y') ?: '') : '';
	}

	#[\Override]
	public function getString(string $lookup): string{
		switch($lookup){
			case 'Invoice date': return 'Rechnungsdatum';
			case 'Delivery date': return 'Lieferdatum';
			case 'Invoice no': return 'Rechnungsnummer';
			case 'Customer no': return 'Kundennummer';
			case 'Reference': return 'Kundenreferenz';
			case 'Description': return 'Bezeichnung';
			case 'Qty': return 'Menge';
			case 'Price': return 'Einzelpreis';
			case 'VAT': return 'Steuer';
			case 'Amount': return 'Gesamtpreis';
			case 'VAT Breakdown': return 'Steuerübersicht';
			case 'Allowance/Charge': return 'Nachlass/Gebühren';
			case 'Charge': return 'Gebühr';
			case 'Allowance': return 'Nachlass';
			case 'Totals': return 'Summe';
			case 'Total': return 'Gesamt';
			case 'Net Total': return 'Netto gesamt';
			case 'Charge Total': return 'Gebühren Gesamt';
			case 'Allowance Total': return 'Nachlass Gesamt';
			case 'Tax': return 'Steuer';
			case 'Gross Total': return 'Brutto Gesamt';
			case 'Already paid': return 'Bereits bezahlt';
			case 'Amount to pay': return 'Zahlbetrag';
			case 'Account name': return 'Kontoname';
			case 'Payment': return 'Zahlweise';
			case 'Payment reference': return 'Verwendungszweck';
			case 'Due date': return 'Zahlbar bis';
			case 'Net': return 'Netto';
			case 'Gross': return 'Brutto';
			case 'Payment information': return 'Zahlungsinformationen';
			case 'Notes': return 'Bemerkungen';
			case 'Mandate': return 'Mandatsreferenz';
			case 'Creditor reference': return 'Gläubiger ID';
			case 'Byer IBAN': return 'Käufer IBAN';
			case 'Payee name': return 'Zahlungsempfänger';
			default: return parent::getString($lookup);
		}
	}
}
