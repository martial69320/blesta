<?php
namespace Blesta\Core\Pricing\Presenter\Build\Invoice;

use Blesta\Core\Pricing\Presenter\Build\Options\AbstractOptions;
use Blesta\Core\Pricing\Presenter\Format\FormatFactory;
use Blesta\Core\Pricing\Presenter\Items\ServiceFactory;
use Blesta\Core\Pricing\Presenter\PresenterFactory;
use Blesta\Core\Pricing\PricingFactory;
use Blesta\Items\ItemFactory;

abstract class AbstractInvoiceBuilder extends AbstractOptions implements InvoiceBuilderInterface
{
    /**
     * @var Instance of FormatFactory
     */
    protected $formatFactory;
    /**
     * @var Instance of PresenterFactory
     */
    protected $presenterFactory;
    /**
     * @var Instance of PricingFactory
     */
    protected $pricingFactory;
    /**
     * @var Instance of ServiceFactory
     */
    protected $serviceFactory;
    /**
     * @var Instance of ItemFactory
     */
    protected $itemFactory;

    /**
     * Init
     *
     * @param ServiceFactory $serviceFactory An instance of the ServiceFactory
     * @param FormatFactory $formatFactory An instance of the FormatFactory
     * @param PricingFactory $pricingFactory An instance of the PricingFactory
     * @param PresenterFactory $presenterFactory An instance of the PresenterFactory
     * @param ItemFactory $itemFactory An instance of the ItemFactory
     */
    public function __construct(
        ServiceFactory $serviceFactory,
        FormatFactory $formatFactory,
        PricingFactory $pricingFactory,
        PresenterFactory $presenterFactory,
        ItemFactory $itemFactory
    ) {
        $this->serviceFactory = $serviceFactory;
        $this->formatFactory = $formatFactory;
        $this->pricingFactory = $pricingFactory;
        $this->presenterFactory = $presenterFactory;
        $this->itemFactory = $itemFactory;
    }
}
