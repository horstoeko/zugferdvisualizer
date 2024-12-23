<?php

use horstoeko\zugferd\codelists\ZugferdUnitCodes;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;

return [
    'unitcodes' => [
        ZugferdUnitCodes::REC20_PIECE => 'St.',
        ZugferdUnitCodes::REC21_PIECE => 'St.',
        ZugferdUnitCodes::REC20_KILOGRAM => 'kg',
        ZugferdUnitCodes::REC20_LITRE => 'l',
        ZugferdUnitCodes::REC20_SQUARE_METRE => 'm<sup>2</sup>',
        ZugferdUnitCodes::REC20_ONE => '',
        ZugferdUnitCodes::REC20_DAY => 'Tag',
        ZugferdUnitCodes::REC20_MINUTE_UNIT_OF_TIME => 'min',
        ZugferdUnitCodes::REC20_KILOWATT_HOUR => 'kWh',
        ZugferdUnitCodes::REC20_LUMP_SUM => 'pausch.',
        ZugferdUnitCodes::REC20_SQUARE_MILLIMETRE => 'mm<sup>2</sup>',
        ZugferdUnitCodes::REC20_MILLIMETRE => 'mm',
        ZugferdUnitCodes::REC20_CUBIC_METRE => 'm<sup>3</sup>',
        ZugferdUnitCodes::REC20_METRE => 'm',
        ZugferdUnitCodes::REC20_NUMBER_OF_ARTICLES => 'mal',
        ZugferdUnitCodes::REC20_PERCENT => '%',
        ZugferdUnitCodes::REC20_SET => 'Set(s)',
        ZugferdUnitCodes::REC20_TONNE_METRIC_TON => 't',
        ZugferdUnitCodes::REC20_RECIPROCAL_WEEK => 'Woche',
        ZugferdUnitCodes::REC20_HOUR => 'h',
        'KTM' => 'km',
    ],
    'documenttype' => [
        ZugferdInvoiceType::INVOICE => 'Rechnung',
        ZugferdInvoiceType::CREDITNOTE => 'Gutschrift',
        ZugferdInvoiceType::CORRECTION => 'Rechnungskorrektur',
        ZugferdInvoiceType::PREPAYMENTINVOICE => 'Vorauszahlungsrechnung',
    ],
    'generaltexts' => [
        'greeting' => 'Werter Kunde',
        'leadingtext1' => 'Wir erlauben uns, folgende Positionen zu berechnen',
        'documentno' => 'Rechnungsnummer',
        'documentdate' => 'Rechnungsdatum',
        'deliverydate' => 'Lieferdatum',
        'customerno' => 'Kundennummer',
        'reference' => 'Kundenreferenz',
        'postableheader' => [
            'posno' => 'Pos.',
            'description' => 'Beschreibung',
            'quantity' => 'Menge',
            'price' => 'Einzelpreis',
            'linemount' => 'Gesamtpreis',
            'vatpercent' => 'Steuer %',
        ],
        'chargeindicator' => [
            'allowance' => 'Rabatt',
            'charge' => 'Zuschlag',
        ],
        'totals' => [
            'heading' => 'Summen',
            'netamount' => 'Nettogesamtbetrag',
            'chargetotalamount' => 'Zuschlagsbetrag',
            'allowancetotalamount' => 'Rabattbetrag',
            'taxtotalamount' => 'Steuergesamtbetrag',
            'grandtotalamount' => 'Bruttogesamtbetrag',
            'alreadypaid' => 'Bereits bezahlt',
            'amounttopay' => 'Fälliger Gesamtbetrag',
        ],
        'vattotals' => [
            'heading' => 'Steuerübersicht',
            'heading2' => 'Total',
        ],
        'formats' => [
            'date' => 'd.m.Y',
        ],
    ],
];
