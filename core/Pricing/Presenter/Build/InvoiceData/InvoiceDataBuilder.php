<?php
namespace Blesta\Core\Pricing\Presenter\Build\InvoiceData;

class InvoiceDataBuilder extends AbstractInvoiceDataBuilder
{
    /**
     * Retrieve an InvoicePresenter
     *
     * @param array $invoice An array representing the invoice, including:
     *  - client_id The ID of the client the invoice is associated with
     *  - date_billed The date the invoice is billed for
     *  - date_due The date the invoice is due on
     *  - autodebit 1 or 0, whether the invoice can be autodebited
     *  - status The invoice status (e.g. 'active', 'proforma', etc.)
     *  - currency The ISO 4217 3-character currency code
     *  - lines A numerically-indexed array of arrays, each representing a line item
     *      - service_id The ID of the service this line item correlates to, if any
     *      - description The line item description
     *      - qty The line item quantity
     *      - amount The line item unit cost
     *      - tax "true" if the line item is taxable
     * @return InvoicePresenter An instance of the InvoicePresenter
     */
    public function build(array $invoice)
    {
        // Initialize the invoice items with settings, taxes, and discounts
        $settingsFormatter = $this->formatFactory->settings();
        $invoiceItems = $this->serviceFactory->invoiceData(
            $this->pricingFactory,
            $this->itemFactory,
            $this->formatSettings($settingsFormatter),
            $this->formatTaxes($this->formatFactory->tax(), $this->itemFactory->itemCollection()),
            $this->formatOptions($settingsFormatter)
        );

        // Format the invoice into consistent fields
        $data = $this->format($invoice);

        // Build the ItemPriceCollection from the invoice items
        $description = $this->pricingFactory->description();
        $collection = $description->describe($invoiceItems->build($data->invoice, $data->lines));

        return $this->presenterFactory->invoiceData($collection);
    }

    /**
     * Formats the invoice data into consistent items
     *
     * @param array $invoice The invoice
     * @return array An array of formatted items representing the invoice
     */
    private function format(array $invoice)
    {
        // Set the service formatters
        $invoiceFormatter = $this->formatFactory->invoice();

        // Format each line item as its own item
        $lines = $this->itemFactory->itemCollection();
        if (array_key_exists('lines', $invoice)) {
            $lineFormatter = $this->formatFactory->invoiceLine();

            foreach ($invoice['lines'] as $line) {
                $lines->append($lineFormatter->format((object)$line));
            }
        }

        return (object)[
            'invoice' => $invoiceFormatter->format((object)$invoice),
            'lines' => $lines
        ];
    }
}
