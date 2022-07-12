<?php
namespace Blesta\Core\Pricing\Presenter\Build\Invoice;

use stdClass;

interface InvoiceBuilderInterface
{
    /**
     * Builds an invoice
     *
     * @param stdClass $invoice
     */
    public function build(stdClass $invoice);
}
