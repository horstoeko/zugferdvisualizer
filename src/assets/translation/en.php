<?php

return [
    'unitcodes' => [
    ],
    'documenttype' => [
        '380' => 'Invoice',
        '381' => 'Credit Note',
        '384' => 'Corrected Invoice',
        '386' => 'Prepayment Invoice',
    ],
    'generaltexts' => [
        'greeting' => 'Dear customer',
        'leadingtext1' => 'We take the liberty of invoicing you for the following items',
        'documentdate' => 'Invoice date',
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
        ]
    ],
];
