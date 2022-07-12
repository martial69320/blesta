<?php

// Uses the TcpdfWrapper for rendering the PDF
Loader::load(COMPONENTDIR . 'invoice_templates' . DS . 'tcpdf_wrapper.php');

/**
 * Perforated Invoice Template
 *
 * @package blesta
 * @subpackage blesta.components.invoice_templates.perforated_invoice
 * @copyright Copyright (c) 2015, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class PerforatedInvoicePdf extends TcpdfWrapper
{
    /**
     * @var string Holds the default font size for this document
     */
    private static $font_size = 9;

    /**
     * @var string Holds the alternate font size for this document
     */
    private static $font_size_alt = 7;

    /**
     * @var string Holds the second alternate font size for this document
     */
    private static $font_size_alt2 = 10;

    /**
     * @var string Holds the third alternate font size for this document
     */
    private static $font_size_alt3 = 20;

    /**
     * @var string The primary font family to use
     */
    private $font = 'dejavusanscondensed';

    /**
     * @var array An RGB representation of the primary color used throughout
     */
    private static $primary_color = [175, 176, 177];

    /**
     * @var array An RGB representation of the primary text color used throughout
     */
    private static $primary_text_color = [83, 84, 86];

    /**
     * @var array The standard number format options
     */
    private static $standard_num_options = [
        'prefix' => false,
        'suffix' => false,
        'code' => false,
        'with_separator' => false
    ];

    /**
     * @var array An array of meta data for this invoice
     */
    public $meta = [];

    /**
     * @var CurrencyFormat The CurrencyFormat object used to format currency values
     */
    public $CurrencyFormat;

    /**
     * @var Date The Date object used to format date values
     */
    public $Date;

    /**
     * @var array An array of invoice data for this invoice
     */
    public $invoice = [];

    /**
     * @var int The Y position where the header finishes its content
     */
    private $header_end_y = 0;

    /**
     * @var array An array of line item options
     */
    private $line_options = [];

    /**
     * @var array An array of transaction payment row options
     */
    private $payment_options = [];

    /**
     * @var int The y_pos to start the table headings at
     */
    private $table_heading_y_pos = 233;

    /**
     * @var bool Whether to include the to address or not
     */
    public $include_address = true;

    /**
     * Initializes the default invoice PDF
     *
     * @param string $orientation The paper orientation (optional, default 'P')
     * @param string $unit The measurement unit (optional, default 'mm')
     * @param string $format The paper format (optional, default 'A4')
     * @param bool $unicode True if the PDF should support unicode characters (optional, default true)
     * @param string $encoding The character encoding (optional, default 'UTF-8')
     * @param bool $diskcache True to cache results to disk (optional, default false)
     * @param string $font The font to use (optional, default null)
     */
    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $font = null
    ) {
        // Invoke the parent constructor
        $format = (is_string($format) ? strtoupper($format) : $format);
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);

        // Default font
        $this->SetDefaultMonospacedFont('courier');
        $this->setFontInfo($font);

        $this->line_options = [
            'x_pos' => 44,
            'y_pos' => $this->table_heading_y_pos,
            'border' => 'BR',
            'height' => 22,
            'line_style' => [
                'width' => 0.5,
                'cap' => 'butt',
                'join' => 'miter',
                'dash' => 2,
                'color' => self::$primary_color
            ],
            'font' => $this->font,
            'font_size' => self::$font_size_alt,
            'padding' => self::$font_size_alt,
            'col' => [
                'name' => [
                    'width' => 312
                ],
                'qty' => [
                    'width' => 70,
                    'align' => 'C'
                ],
                'unit_price' => [
                    'width' => 70,
                    'align' => 'R'
                ],
                'price' => [
                    'width' => 70,
                    'align' => 'R',
                    'border' => 'B'
                ],
            ],
            'cell' => [['name' => ['align' => 'L']]]
        ];

        $this->payment_options = [
            'x_pos' => 44,
            'y_pos' => $this->table_heading_y_pos,
            'border' => 'BR',
            'height' => 22,
            'line_style' => [
                'width' => 0.5,
                'cap' => 'butt',
                'join' => 'miter',
                'dash' => 2,
                'color' => self::$primary_color
            ],
            'font' => $this->font,
            'font_size' => self::$font_size_alt,
            'padding' => self::$font_size_alt,
            'col' => [
                'applied_date' => [
                    'width' => 130.5
                ],
                'type_name' => [
                    'width' => 130.5,
                    'align' => 'C'
                ],
                'transaction_id' => [
                    'width' => 130.5,
                    'align' => 'R'
                ],
                'applied_amount' => [
                    'width' => 130.5,
                    'align' => 'R',
                    'border' => 'B'
                ],
            ],
            'cell' => [['applied_date' => ['align' => 'L']]]
        ];

        // Set margins
        $this->SetMargins(25, 260, 25);
        $footer_margin = 240;
        $this->SetFooterMargin($footer_margin);

        // Set auto page breaks y-px from the bottom of the page
        $this->SetAutoPageBreak(true, $footer_margin + 30);
    }

    /**
     * Overwrite the default header that appears on each page of the PDF
     */
    public function Header()
    {
        $this->SetTextColorArray(self::$primary_text_color);

        // Draw the background
        $this->drawBackground();

        // Draw the paid text in the background
        $this->drawPaidWatermark();

        // Set logo
        $this->drawLogo();

        // Set the page mark so background images will display correctly
        $this->setPageMark();

        // Draw the return address
        $this->drawReturnAddress();

        // Place the invoice type on the document
        $this->drawInvoiceType();

        // Set Address
        if ($this->include_address) {
            $this->drawAddress();
        }

        // Draw Tax ID
        $this->drawTaxId();

        // Add Invoice Number, Customer Number, Invoice Date, Invoice Due Date
        $this->drawInvoiceInfo();

        // Draw the line items table heading on each page
        $this->drawLineHeader();

        // Set the position where the header finishes
        $this->header_end_y = $this->GetY();

        // Set the top margin again, incase any header methods expanded this area.
        $this->SetTopMargin($this->header_end_y);
    }

    /**
     * Overwrite the default footer that appears on each page of the PDF
     */
    public function Footer()
    {
        $this->SetTextColorArray(self::$primary_text_color);

        $this->drawDetachHeader();

        $y_pos = $this->getY() + 20;
        $this->drawDetachAccount($y_pos);
        $this->drawDetachInvoice($y_pos);

        $this->drawDetachtTotals($this->getY() + 20);
        $this->drawDetachCompany();

        // Set the page number of the document
        $this->drawPageNumber();
    }

    /**
     * Adds the detachment header to the PDF
     */
    private function drawDetachHeader()
    {
        $this->drawTable(
            [
                [Language::_('PerforatedInvoice.detach_heading', true)]
            ],
            [
                'col' => [['align' => 'C']],
                'row' => [['font' => $this->font, 'font_style' => 'B', 'font_size' => 14]]
            ]
        );
    }

    /**
     * Adds the totals to the detach section
     *
     * @param int $y_pos The Y position of the document to set the totals
     */
    private function drawDetachtTotals($y_pos)
    {
        $data = [
            [
                'name' => Language::_('PerforatedInvoice.detach_total_due', true),
                'space' => null,
                'value' => $this->CurrencyFormat->format($this->invoice->due, $this->invoice->currency)
            ],
            [
                'name' => Language::_('PerforatedInvoice.detach_total_payment', true),
                'space' => null,
                'value' => null
            ]
        ];

        $this->drawTable(
            $data,
            [
                'font' => $this->font,
                'font_size' => self::$font_size_alt,
                'line_style' => [
                    'width' => 1,
                    'cap' => 'butt',
                    'join' => 'miter',
                    'dash' => 0,
                    'color' => self::$primary_color
                ],
                'y_pos' => $y_pos,
                'x_pos' => 185,
                'col' => [
                    'name' => [
                        'width' => 285,
                        'align' => 'R'
                    ],
                    'space' => [
                        'width' => 5
                    ],
                    'value' => [
                        'width' => 85,
                        'align' => 'R'
                    ]
                ],
                'cell' => [1 => ['value' => ['border' => 'TRBL', 'padding' => 4]]]
            ]
        );
    }

    /**
     * Sets the company name/address to the detach section
     */
    private function drawDetachCompany()
    {
        $data = [
            [
                'label' => Language::_('PerforatedInvoice.detach_remit_to', true),
                'value' => $this->meta['company_name']
            ],
            [
                'label' => null,
                'value' => $this->meta['company_address']
            ]
        ];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size_alt,
            'y_pos' => -120,
            'x_pos' => 44,
            'col' => [
                'label' => [
                    'width' => 50,
                    'align' => 'L'
                ],
                'value' => [
                    'width' => 120,
                    'align' => 'L'
                ]
            ]
        ];
        $this->drawTable($data, $options);
    }

    /**
     * Sets the client information to the detach section
     *
     * @param int $y_pos The Y position of the document to set the client information
     */
    private function drawDetachAccount($y_pos)
    {
        $data = [
            [
                'name' => Language::_('PerforatedInvoice.client_id_code', true),
                'space' => null,
                'value' => $this->invoice->client->id_code
            ],
            [
                'name' => Language::_('PerforatedInvoice.client_name', true),
                'space' => null,
                'value' => $this->invoice->client->first_name . ' ' . $this->invoice->client->last_name
            ]
        ];

        $this->drawTable(
            $data,
            [
                'font' => $this->font,
                'font_size' => self::$font_size_alt,
                'y_pos' => $y_pos,
                'x_pos' => 44,
                'col' => [
                    'name' => [
                        'width' => 80,
                        'align' => 'L'
                    ],
                    'space' => [
                        'width' => 10
                    ],
                    'value' => [
                        'width' => 100,
                        'align' => 'L'
                    ]
                ]
            ]
        );
    }

    /**
     * Retrieves the invoice number in the appropriate language
     *
     * @return string The invoice number
     */
    private function getInvoiceNumberLanguage()
    {
        // Set the invoice number label language
        $inv_id_code_lang = 'PerforatedInvoice.invoice_id_code';
        if (in_array($this->invoice->status, ['proforma', 'draft'])) {
            $inv_id_code_lang = 'PerforatedInvoice.' . $this->invoice->status . '_id_code';
        }
        return $inv_id_code_lang;
    }

    /**
     * Sets the detachment section of the invoice
     *
     * @param int $y_pos The Y position of the document to set the invoice information
     */
    private function drawDetachInvoice($y_pos)
    {
        $data = [
            [
                'name' => Language::_($this->getInvoiceNumberLanguage(), true),
                'space' => null,
                'value' => $this->invoice->id_code
            ],
            [
                'name' => Language::_('PerforatedInvoice.date_billed', true),
                'space' => null,
                'value' => $this->Date->cast(
                    $this->invoice->date_billed,
                    $this->invoice->client->settings['date_format']
                )
            ]
        ];

        $this->drawTable(
            $data,
            [
                'font' => $this->font,
                'font_size' => self::$font_size_alt,
                'y_pos' => $y_pos,
                'x_pos' => 185,
                'col' => [
                    'name' => [
                        'width' => 285,
                        'align' => 'R'
                    ],
                    'space' => [
                        'width' => 10
                    ],
                    'value' => [
                        'width' => 85,
                        'align' => 'L'
                    ]
                ]
            ]
        );
    }

    /**
     * Draws a complete invoice
     */
    public function drawInvoice()
    {
        $options = $this->line_options;
        $options['y_pos'] = max($this->header_end_y, $this->GetY());

        // Build line items
        $lines = [];
        $inv_lines = (isset($this->invoice->line_items) && is_array($this->invoice->line_items)
            ? count($this->invoice->line_items)
            : 0
        );

        for ($i = 0; $i < $inv_lines; $i++) {
            $lines[] = [
                'name' => $this->invoice->line_items[$i]->description,
                'qty' => $this->CurrencyFormat->truncateDecimal($this->invoice->line_items[$i]->qty, 0),
                'unit_price' => $this->CurrencyFormat->format(
                    $this->invoice->line_items[$i]->amount,
                    $this->invoice->currency,
                    self::$standard_num_options
                ),
                'price' => $this->CurrencyFormat->format(
                    $this->invoice->line_items[$i]->total,
                    $this->invoice->currency,
                    self::$standard_num_options
                ),
            ];
        }

        // Draw invoice lines
        $this->drawTable($lines, $options);

        // Draw public notes and invoice tallies
        $this->drawTallies();

        // Draw transaction payments/credits
        $this->drawPayments();

        // Set the terms of the document
        if (!empty($this->meta['terms'])) {
            $this->drawTerms();
        }
    }

    /**
     * Set the fonts and font attributes to be used in the document
     *
     * @param string $font The font to set
     */
    private function setFontInfo($font)
    {
        $lang = [];
        $lang['a_meta_charset'] = 'UTF-8';
        $lang['a_meta_dir'] = Language::_('AppController.lang.dir', true)
            ? Language::_('AppController.lang.dir', true)
            : 'ltr';

        // Set language settings
        $this->setLanguageArray($lang);

        if ($font) {
            $this->font = $font;
        }

        // Set font
        $this->SetFont($this->font, '', self::$font_size);

        // Set default text color
        $this->SetTextColorArray(self::$primary_text_color);
    }

    /**
     * Draws the paid text in the background of the invoice
     */
    private function drawPaidWatermark()
    {
        // Show paid watermark
        if (!empty($this->meta['display_paid_watermark'])
            && $this->meta['display_paid_watermark'] == 'true'
            && ($this->invoice->date_closed != null)
        ) {
            $max_height = $this->getPageHeight();
            $max_width = $this->getPageWidth();

            $options = [
                'x_pos' => 25, // start within margin
                'y_pos' => ($max_height - 125) / 2, // vertical center
                'font' => $this->font,
                'font_size' => 100,
                'row' => [['font_style' => 'B', 'align' => 'C']]
            ];

            $data = [
                ['col' => Language::_('PerforatedInvoice.watermark_paid', true)]
            ];

            // Set paid background text color
            $this->SetTextColorArray([230, 230, 230]);

            // Rotate the text
            $this->StartTransform();
            // Rotate 45 degrees from midpoint
            $this->Rotate(45, ($max_width) / 2, ($max_height) / 2);

            $this->drawTable($data, $options);

            $this->StopTransform();

            // Set default text color
            $this->SetTextColorArray(self::$primary_text_color);
        }
    }

    /**
     * Renders public notes and invoice tallies onto the document
     */
    private function drawTallies()
    {
        $page = $this->getPage();

        $options = [
            'border' => 0,
            'x_pos' => 44,
            'y_pos' => max($this->header_end_y, $this->GetY()),
            'font' => $this->font,
            'font_size' => self::$font_size_alt,
            'col' => [
                [
                    'height' => 12,
                    'width' => 382
                ]
            ],
            'row' => [['font_style' => 'B']]
        ];


        // Draw notes
        $y_pos = 0;
        if (!empty($this->invoice->note_public)) {
            $note_options = $options;
            $note_options['y_pos'] += 4.5;

            $data = [
                [Language::_('PerforatedInvoice.notes_heading', true)],
                [$this->invoice->note_public]
            ];
            // Draw notes
            $this->drawTable($data, $note_options);
            $y_pos = $this->GetY();
        }

        $this->setPage($page);

        // Set subtitle
        $data = [
            [
                'notes' => null,
                'label' => Language::_('PerforatedInvoice.subtotal_heading', true),
                'price' => $this->CurrencyFormat->format(
                    $this->invoice->subtotal,
                    $this->invoice->currency,
                    self::$standard_num_options
                )
            ]
        ];
        // Set all taxes
        foreach ($this->invoice->taxes as $tax) {
            $data[] = [
                'notes' => null,
                'label' => Language::_('PerforatedInvoice.tax_heading', true, $tax->name, $tax->amount),
                'price' => $this->CurrencyFormat->format(
                    $tax->tax_total,
                    $this->invoice->currency,
                    self::$standard_num_options
                )
            ];
        }
        // Set total
        $data[] = [
            'notes' => null,
            'label' => Language::_('PerforatedInvoice.total_heading', true),
            'price' => $this->CurrencyFormat->format(
                $this->invoice->total,
                $this->invoice->currency
            )
        ];


        $options['row'] = ['label' => ['border' => 'R'], 'price' => ['border' => 0]];
        $options['padding'] = self::$font_size_alt;
        $options['col'] = [
            'notes' => [
                'width' => 382
            ],
            'label' => [
                'width' => 70,
                'align' => 'R',
                'border' => 'TR',
                'font_style' => 'B'
            ],
            'price' => [
                'width' => 70,
                'align' => 'R',
                'border' => 'T'
            ],
        ];

        // Draw tallies
        $this->drawTable($data, $options);

        // Set the Y position to the greater of the notes area, or the subtotal/total area
        $this->SetY(max($y_pos, $this->GetY()));
    }

    /**
     * Renders a heading, typically above a table
     *
     * @param string $heading The name of the heading for the table
     */
    private function drawTableHeading($heading)
    {
        $options = [
            'x_pos' => 44,
            'y_pos' => max($this->table_heading_y_pos, $this->GetY()),
            'col' => [
                'heading' => [
                    'align' => 'L',
                    'font_style' => 'B',
                    'border' => 0
                ]
            ],
            'font' => $this->font,
            'font_size' => self::$font_size_alt2,
            'padding' => 0,
        ];

        $data = [
            ['heading' => $heading],
        ];

        // Draw table heading
        $this->drawTable($data, $options);
    }

    /**
     * Renders the transaction payments/credits header onto the document
     */
    private function drawPaymentHeader()
    {
        // Add a heading above the table
        $this->drawTableHeading(Language::_('PerforatedInvoice.payments_heading', true));

        // Use the same options as line items
        $options = $this->payment_options;
        // Start header at top of page, or current page below the other table
        $options['y_pos'] = max($this->table_heading_y_pos, $this->GetY());

        // Draw the transaction payment header
        $options['row'] = [[
            'font' => $this->font,
            'font_size' => self::$font_size_alt2,
            'fill_color' => self::$primary_text_color,
            'border' => '0',
            'align' => 'C',
            'text_color' => [255, 255, 255]
        ]];

        $header = [[
            'applied_date' => Language::_('PerforatedInvoice.payments_applied_date', true),
            'type_name' => Language::_('PerforatedInvoice.payments_type_name', true),
            'transaction_id' => Language::_('PerforatedInvoice.payments_transaction_id', true),
            'applied_amount' => Language::_('PerforatedInvoice.payments_applied_amount', true)
        ]];

        $this->drawTable($header, $options);
    }

    /**
     * Renders the transaction payments/credits section onto the document
     */
    private function drawPayments()
    {
        if (!empty($this->meta['display_payments']) && $this->meta['display_payments'] == 'true') {
            // Set the payment rows
            $rows = [];
            $inv_trans = (isset($this->invoice->applied_transactions) && is_array($this->invoice->applied_transactions)
                ? count($this->invoice->applied_transactions)
                : 0
            );

            for ($i = 0; $i < $inv_trans; $i++) {
                // Only show approved transactions
                if ($this->invoice->applied_transactions[$i]->status != 'approved') {
                    continue;
                }

                // Use the type name, or the gateway name
                $type_name = $this->invoice->applied_transactions[$i]->type_real_name;
                if ($this->invoice->applied_transactions[$i]->type == 'other'
                    && $this->invoice->applied_transactions[$i]->gateway_type == 'nonmerchant'
                ) {
                    $type_name = $this->invoice->applied_transactions[$i]->gateway_name;
                }

                $rows[] = [
                    'applied_date' => $this->Date->cast(
                        $this->invoice->applied_transactions[$i]->applied_date,
                        $this->invoice->client->settings['date_format']
                    ),
                    'type_name' => $type_name,
                    'transaction_id' => $this->invoice->applied_transactions[$i]->transaction_number,
                    'applied_amount' => $this->CurrencyFormat->format(
                        $this->invoice->applied_transactions[$i]->applied_amount,
                        $this->invoice->applied_transactions[$i]->currency,
                        self::$standard_num_options
                    )
                ];
            }

            // Don't draw the table if there are no payments
            if (empty($rows)) {
                return;
            }

            // Draw the table heading
            $this->drawPaymentHeader();

            // Draw the table rows
            $options = $this->payment_options;
            $options['y_pos'] = max($this->table_heading_y_pos, $this->header_end_y, $this->GetY());
            $this->drawTable($rows, $options);

            // Set balance due at bottom of table
            $data = [[
                'blank' => '',
                'label' => Language::_('PerforatedInvoice.balance_heading', true),
                'price' => $this->CurrencyFormat->format($this->invoice->due, $this->invoice->currency)
            ]];

            $options['y_pos'] = max($this->table_heading_y_pos, $this->header_end_y, $this->GetY());
            $options['row'] = ['blank' => ['border' => 0]];
            $options['padding'] = self::$font_size_alt;
            $options['col'] = [
                'blank' => [
                    'width' => 261,
                    'border' => 0
                ],
                'label' => [
                    'width' => 130.5,
                    'align' => 'R',
                    'border' => 'R',
                    'font_style' => 'B'
                ],
                'price' => [
                    'width' => 130.5,
                    'align' => 'R',
                    'border' => 0
                ],
            ];

            // Draw balance
            $this->drawTable($data, $options);
        }
    }

    /**
     * Renders the background image onto the document
     */
    private function drawBackground()
    {
        // Set background image by fetching current margin break,
        // then disable page break, set the image, and re-enable page break with
        // the current margin
        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->getAutoPageBreak();
        $margins = $this->getMargins();

        $this->SetMargins(0, 0, 0, true);
        $this->SetAutoPageBreak(false, 0);


        if (file_exists($this->meta['background'])) {
            $this->Image(
                $this->meta['background'],
                0,
                0,
                0,
                0,
                '',
                '',
                '',
                true,
                300,
                '',
                false,
                false,
                0,
                false,
                false,
                true
            );
        }

        // Restore auto-page-break status and margins
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        $this->setPageMark();
        $this->SetMargins($margins['left'], $margins['top'], $margins['right'], true);
    }

    /**
     * Renders the logo onto the document
     */
    private function drawLogo()
    {
        // Really wish we could align right, but aligning right will not respect the
        // $x parameter in TCPDF::Image(), so we must manually set the off-set. That's ok
        // because we're setting the width anyway.
        if ($this->meta['display_logo'] == 'true' && file_exists($this->meta['logo'])) {
            $this->Image($this->meta['logo'], 432, 35, 140);
        }
    }

    /**
     * Renders the tax ID section to the document
     */
    private function drawTaxId()
    {
        $data = [];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size,
            'x_pos' => 185,
            'y_pos' => 130,
            'col' => [
                'name' => [
                    'width' => 285,
                    'align' => 'R'
                ],
                'space' => [
                    'width' => 10
                ],
                'value' => [
                    'width' => 85,
                    'align' => 'L'
                ]
            ]
        ];

        if (isset($this->meta['tax_id']) && $this->meta['tax_id'] != '') {
            $data[] = [
                'name' => Language::_('PerforatedInvoice.tax_id', true),
                'space' => null,
                'value' => $this->meta['tax_id']
            ];
        }
        if (isset($this->invoice->client->settings['tax_id']) && $this->invoice->client->settings['tax_id'] != '') {
            $data[] = [
                'name' => Language::_('PerforatedInvoice.client_tax_id', true),
                'space' => null,
                'value' => $this->invoice->client->settings['tax_id']
            ];
        }

        // Draw Tax/VAT ID
        $this->drawTable($data, $options);
    }

    /**
     * Fetch the date due to display
     *
     * @return string The date due to display
     */
    protected function getDateDue()
    {
        $date_due = null;
        switch ($this->invoice->status) {
            case 'proforma':
                $due_date_option = 'display_due_date_proforma';
                break;
            case 'draft':
                $due_date_option = 'display_due_date_draft';
                break;
            default:
                $due_date_option = 'display_due_date_inv';
                break;
        }
        if ($this->meta[$due_date_option] == 'true') {
            $date_due = $this->invoice->date_due;
        }
        return $date_due;
    }

    /**
     * Renders the Invoice info section to the document, containing the invoice ID, client ID, date billed, and date due
     */
    private function drawInvoiceInfo()
    {
        $data = [
            [
                'name' => Language::_($this->getInvoiceNumberLanguage(), true),
                'space' => null,
                'value' => $this->invoice->id_code
            ],
            [
                'name' => Language::_('PerforatedInvoice.client_id_code', true),
                'space' => null,
                'value' => $this->invoice->client->id_code
            ],
            [
                'name' => Language::_('PerforatedInvoice.date_billed', true),
                'space' => null,
                'value' => $this->Date->cast(
                    $this->invoice->date_billed,
                    $this->invoice->client->settings['date_format']
                )
            ]
        ];

        $date_due = $this->getDateDue();
        if ($date_due) {
            $data[] = [
                'name' => Language::_('PerforatedInvoice.date_due', true),
                'space' => null,
                'value' => $this->Date->cast(
                    $date_due,
                    $this->invoice->client->settings['date_format']
                )
            ];
        }

        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size,
            'x_pos' => 185,
            'y_pos' => max(158, $this->GetY() - $this->header_end_y),
            'col' => [
                'name' => [
                    'width' => 285,
                    'align' => 'R'
                ],
                'space' => [
                    'width' => 10
                ],
                'value' => [
                    'width' => 85,
                    'align' => 'L'
                ]
            ]
        ];
        $this->drawTable($data, $options);
    }

    /**
     * Renders the line items table heading
     */
    private function drawLineHeader()
    {
        $options = $this->line_options;
        $options['row'] = [[
            'font' => $this->font,
            'font_size' => self::$font_size_alt2,
            'fill_color' => self::$primary_text_color,
            'border' => '0',
            'align' => 'C',
            'text_color' => [255, 255, 255]
        ]];

        $header = [[
            'name' => Language::_('PerforatedInvoice.lines_description', true),
            'qty' => Language::_('PerforatedInvoice.lines_quantity', true),
            'unit_price' => Language::_('PerforatedInvoice.lines_unit_price', true),
            'price' => Language::_('PerforatedInvoice.lines_cost', true)
        ]];

        $this->drawTable($header, $options);
    }

    /**
     * Renders the to address information
     */
    private function drawAddress()
    {
        $address = $this->invoice->billing->first_name . ' ' . $this->invoice->billing->last_name . "\n";
        if (strlen($this->invoice->billing->company) > 0) {
            $address .= $this->invoice->billing->company . "\n";
        }
        $address .= $this->invoice->billing->address1 . "\n";
        if (strlen($this->invoice->billing->address2) > 0) {
            $address .= $this->invoice->billing->address2 . "\n";
        }
        $address .= $this->invoice->billing->city . ', ' . $this->invoice->billing->state
            . ' ' . $this->invoice->billing->zip . ' ' . $this->invoice->billing->country->alpha3;

        $data = [
            [$address]
        ];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size,
            'x_pos' => 44,
            'y_pos' => 157,
            'col' => [
                ['width' => 210]
            ]
        ];
        $this->drawTable($data, $options);
    }

    /**
     * Renders the return address information
     */
    private function drawReturnAddress()
    {
        if ($this->meta['display_companyinfo'] == 'false') {
            return;
        }
        $data = [
            [$this->meta['company_name']],
            [$this->meta['company_address']]
        ];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size,
            'y_pos' => 38,
            'x_pos' => 44,
            'align' => 'L'
        ];
        $this->drawTable($data, $options);
    }

    /**
     * Sets the invoice type on the document based upon the status of the invoice
     */
    private function drawInvoiceType()
    {
        $data = [
            [Language::_('PerforatedInvoice.type_' . $this->invoice->status, true)]
        ];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size_alt3,
            'font_style' => 'B',
            'y_pos' => 114,
            'x_pos' => 43,
            'align' => 'L'
        ];
        $this->drawTable($data, $options);
    }

    /**
     * Renders the page number to the document
     */
    private function drawPageNumber()
    {
        $font = $this->getFontBuffer($this->font);
        $group_alias = ($font && isset($font['type']) && in_array($font['type'], ['TrueTypeUnicode', 'cidfont0'])
            ? '{' . $this->getPageGroupAlias() . '}'
            : $this->getPageGroupAlias()
        );

        $data = [
            [Language::_('PerforatedInvoice.page_of', true, $this->getGroupPageNo(), $group_alias)]
        ];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size_alt,
            'font_style' => 'B',
            'y_pos' => -52,
            'align' => 'R'
        ];
        $this->drawTable($data, $options);
    }

    /**
     * Renders the terms of this document
     */
    private function drawTerms()
    {
        $data = [
            [Language::_('PerforatedInvoice.terms_heading', true)],
            [$this->meta['terms']]
        ];
        $options = [
            'font' => $this->font,
            'font_size' => self::$font_size_alt,
            'border' => 0,
            'x_pos' => 48,
            //'y_pos' => -260,
            'col' => [['height' => 12]],
            'row' => [['font_style' => 'B']]
        ];
        $this->drawTable($data, $options);
    }
}