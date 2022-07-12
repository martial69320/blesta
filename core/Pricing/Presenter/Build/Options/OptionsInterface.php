<?php
namespace Blesta\Core\Pricing\Presenter\Build\Options;

/**
 * Interface for options
 *
 * @package blesta
 * @subpackage blesta.core.Pricing.Presenter.Build.Options
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
interface OptionsInterface
{
    /**
     * Sets a list of settings
     *
     * @param array $settings An array containing all client and company settings as key/value pairs
     */
    public function settings(array $settings);

    /**
     * Sets any key/value custom options
     *
     * @param array $options An array of custom options:
     *
     *  - recur Boolean true/false. Whether the pricing items are recurring,
     *      or if they are being added for the first time (default false)
     */
    public function options(array $options);

    /**
     * Sets all tax rules
     *
     * @param array $taxes An array of stdClass objects representing each tax rule that applies, containing:
     *
     *  - id The tax ID
     *  - company_id The company ID
     *  - level The tax level
     *  - name The name of the tax
     *  - amount The tax amount
     *  - type The tax type (inclusive, exclusive)
     *  - status The tax status
     */
    public function taxes(array $taxes);

    /**
     * Sets all discounts
     *
     * @param array $discounts An array of stdClass objects representing each coupon that applies, containing:
     *
     *  - id The coupon ID
     *  - code The coupon code
     *  - used_qty The number of times the coupon has been used
     *  - max_qty The max number of coupon uses
     *  - start_date The date the coupon begins
     *  - end_date The date the coupon ends
     *  - status The coupon status
     *  - type The type of coupon as it applies to packages (exclusive, inclusive)
     *  - recurring 1 or 0, whether the coupon applies to recurring services
     *  - limit_recurring 1 or 0, whether the coupon limitations apply to recurring services
     *  - apply_package_options 1 or 0, whether the coupon applies to a service's package options
     *  - amounts An array of stdClass objects representing each coupon amount, containing:
     *      - coupon_id The coupon ID
     *      - currency The coupon amount currency
     *      - amount The coupon amount
     *      - type The coupon amount type (percent, amount)
     *  - packages An array of stdClass objects representing each assigned coupon package, containing:
     *      - coupon_id The coupon ID
     *      - package_id The assigned package ID
     */
    public function discounts(array $discounts);
}
