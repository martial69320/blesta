<?php
namespace Blesta\Core\Pricing\Presenter\Build\InvoiceData;

interface InvoiceDataBuilderInterface
{
    /**
     * Builds an invoice
     *
     * @param array $invoice
     */
    public function build(array $invoice);
}
