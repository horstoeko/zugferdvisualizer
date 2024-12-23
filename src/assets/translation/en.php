<?php

use horstoeko\zugferd\codelists\ZugferdUnitCodes;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;

return [
    'unitcodes' => [
        ZugferdUnitCodes::REC20_PIECE => 'pc',
        ZugferdUnitCodes::REC21_PIECE => 'pc',
        ZugferdUnitCodes::REC20_KILOGRAM => 'kg',
        ZugferdUnitCodes::REC20_LITRE => 'l',
        ZugferdUnitCodes::REC20_SQUARE_METRE => 'm<sup>2</sup>',
        ZugferdUnitCodes::REC20_ONE => '',
        ZugferdUnitCodes::REC20_DAY => 'day',
        ZugferdUnitCodes::REC20_MINUTE_UNIT_OF_TIME => 'min',
        ZugferdUnitCodes::REC20_KILOWATT_HOUR => 'kWh',
        ZugferdUnitCodes::REC20_LUMP_SUM => 'lump sum',
        ZugferdUnitCodes::REC20_SQUARE_MILLIMETRE => 'mm<sup>2</sup>',
        ZugferdUnitCodes::REC20_MILLIMETRE => 'mm',
        ZugferdUnitCodes::REC20_CUBIC_METRE => 'm<sup>3</sup>',
        ZugferdUnitCodes::REC20_METRE => 'm',
        ZugferdUnitCodes::REC20_NUMBER_OF_ARTICLES => 'times',
        ZugferdUnitCodes::REC20_PERCENT => '%',
        ZugferdUnitCodes::REC20_SET => 'Set(s)',
        ZugferdUnitCodes::REC20_TONNE_METRIC_TON => 't',
        ZugferdUnitCodes::REC20_RECIPROCAL_WEEK => 'week',
        ZugferdUnitCodes::REC20_HOUR => 'h',
        'KTM' => 'km',
    ],
    'documenttype' => [
        ZugferdInvoiceType::INVOICE => 'Invoice',
        ZugferdInvoiceType::CREDITNOTE => 'Credit Note',
        ZugferdInvoiceType::CORRECTION => 'Corrected Invoice',
        ZugferdInvoiceType::PREPAYMENTINVOICE => 'Prepayment Invoice',
    ],
    'generaltexts' => [
        'greeting' => 'Dear customer',
        'leadingtext1' => 'We take the liberty of invoicing you for the following items',
        'documentno' => 'Invoice No.',
        'documentdate' => 'Invoice date',
        'deliverydate' => 'Delivery date',
        'customerno' => 'Customer No.',
        'reference' => 'Reference',
        'postableheader' => [
            'posno' => 'Pos.',
            'description' => 'Desc.',
            'quantity' => 'Qty.',
            'price' => 'Price',
            'linemount' => 'Amount',
            'vatpercent' => 'VAT %',
        ],
        'chargeindicator' => [
            'allowance' => 'Allowance',
            'charge' => 'Charge',
        ],
        'totals' => [
            'heading' => 'Totals',
            'netamount' => 'Net Total',
            'chargetotalamount' => 'Charge Total',
            'allowancetotalamount' => 'Allowance Total',
            'taxtotalamount' => 'Tax',
            'grandtotalamount' => 'Gross Total',
            'alreadypaid' => 'Already paid',
            'amounttopay' => 'Amount to pay',
        ],
        'vattotals' => [
            'heading' => 'VAT Breakdown',
            'heading2' => 'Total',
        ],
        'formats' => [
            'date' => 'd.m.Y',
        ],
    ],
];
