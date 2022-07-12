<?php
/**
 * CCAvenue Payment Gateway
 *
 * @package blesta
 * @subpackage blesta.components.gateways.ccavenue
 * @author Phillips Data, Inc.
 * @author Nirays Technologies
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @link http://nirays.com/ Nirays
 */
class Ccavenue extends NonmerchantGateway
{
    /**
     * @var string The version of this gateway
     */
    private static $version = '2.1.0';
    /**
     * @var string The authors of this gateway
     */
    private static $authors = [
        ['name' => 'Phillips Data, Inc.', 'url' => 'http://www.blesta.com'],
        ['name' => 'Nirays Technologies.', 'url' => 'http://nirays.com']
    ];

    /**
     * @var array An array of meta data for this gateway
     */
    private $meta;

    /**
     * @var string The URL to post payments to
     */
    private $ccavenue_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    /**
     * @var string The test URL to post payments to
     */
    private $test_url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

    /**
     * Construct a new merchant gateway
     */
    public function __construct()
    {

        // Load components required by this gateway
        Loader::loadComponents($this, ['Input']);

        // Load components required by this gateway
        Loader::loadModels($this, ['Clients', 'Contacts', 'Companies']);
        // Load the language required by this gateway
        Language::loadLang('ccavenue', null, dirname(__FILE__) . DS . 'language' . DS);
    }

    /**
     * Returns the name of this gateway
     *
     * @return string The common name of this gateway
     */
    public function getName()
    {
        return Language::_('Ccavenue.name', true);
    }

    /**
     * Returns the version of this gateway
     *
     * @return string The current version of this gateway
     */
    public function getVersion()
    {
        return self::$version;
    }

    /**
     * Returns the name and URL for the authors of this gateway
     *
     * @return array The name and URL of the authors of this gateway
     */
    public function getAuthors()
    {
        return self::$authors;
    }

    /**
     * Return all currencies supported by this gateway
     *
     * @return array A numerically indexed array containing all currency codes (ISO 4217 format) this gateway supports
     */
    public function getCurrencies()
    {
        return ['EUR', 'GBP', 'INR', 'SGD', 'USD'];
    }

    /**
     * Sets the currency code to be used for all subsequent payments
     *
     * @param string $currency The ISO 4217 currency code to be used for subsequent payments
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Create and return the view content required to modify the settings of this gateway
     *
     * @param array $meta An array of meta (settings) data belonging to this gateway
     * @return string HTML content containing the fields to update the meta data for this gateway
     */
    public function getSettings(array $meta = null)
    {
        $this->view = $this->makeView('settings', 'default', str_replace(ROOTWEBDIR, '', dirname(__FILE__) . DS));

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html']);

        $this->view->set('meta', $meta);

        return $this->view->fetch();
    }

    /**
     * Validates the given meta (settings) data to be updated for this gateway
     *
     * @param array $meta An array of meta (settings) data to be updated for this gateway
     * @return array The meta data to be updated in the database for this gateway, or reset into the form on failure
     */
    public function editSettings(array $meta)
    {
        // Verify meta data is valid
        $rules = [
            'merchant_id' => [
                'valid' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Ccavenue.!error.merchant_id.valid', true)
                ]
            ],
            'access_code' => [
                'empty' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Ccavenue.!error.access_code.empty', true)
                ]
            ],
            'encryption_key' => [
                'valid' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Ccavenue.!error.encryption_key.valid', true)
                ]
            ]
        ];

        // Set test mode if not given
        if (empty($meta['test_mode'])) {
            $meta['test_mode'] = 'false';
        }

        $this->Input->setRules($rules);

        // Validate the given meta data to ensure it meets the requirements
        $this->Input->validates($meta);
        // Return the meta data, no changes required regardless of success or failure for this gateway
        return $meta;
    }

    /**
     * Returns an array of all fields to encrypt when storing in the database
     *
     * @return array An array of the field names to encrypt when storing in the database
     */
    public function encryptableFields()
    {
        return ['encryption_key', 'access_code'];
    }

    /**
     * Sets the meta data for this particular gateway
     *
     * @param array $meta An array of meta data to set for this gateway
     */
    public function setMeta(array $meta = null)
    {
        $this->meta = $meta;
    }

    /**
     * Returns all HTML markup required to render an authorization and capture payment form
     *
     * @param array $contact_info An array of contact info including:
     *  - id The contact ID
     *  - client_id The ID of the client this contact belongs to
     *  - user_id The user ID this contact belongs to (if any)
     *  - contact_type The type of contact
     *  - contact_type_id The ID of the contact type
     *  - first_name The first name on the contact
     *  - last_name The last name on the contact
     *  - title The title of the contact
     *  - company The company name of the contact
     *  - address1 The address 1 line of the contact
     *  - address2 The address 2 line of the contact
     *  - city The city of the contact
     *  - state An array of state info including:
     *      - code The 2 or 3-character state code
     *      - name The local name of the country
     *  - country An array of country info including:
     *      - alpha2 The 2-character country code
     *      - alpha3 The 3-cahracter country code
     *      - name The english name of the country
     *      - alt_name The local name of the country
     *  - zip The zip/postal code of the contact
     * @param float $amount The amount to charge this contact
     * @param array $invoice_amounts An array of invoices, each containing:
     *  - id The ID of the invoice being processed
     *  - amount The amount being processed for this invoice (which is included in $amount)
     * @param array $options An array of options including:
     *  - description The Description of the charge
     *  - return_url The URL to redirect users to after a successful payment
     *  - recur An array of recurring info including:
     *      - start_date The date/time in UTC that the recurring payment begins
     *      - amount The amount to recur
     *      - term The term to recur
     *      - period The recurring period (day, week, month, year, onetime) used in
     *          conjunction with term in order to determine the next recurring payment
     * @return mixed A string of HTML markup required to render an authorization and
     *  capture payment form, or an array of HTML markup
     */
    public function buildProcess(array $contact_info, $amount, array $invoice_amounts = null, array $options = null)
    {
        $client = $this->Clients->get($contact_info['client_id']);

        Loader::load(dirname(__FILE__) . DS . 'lib' . DS . 'lib_functions.php');
        $util = new LibFunctions();
        $company = $this->Companies->get($client->company_id);

        // Get the company hostname
        $hostname = isset($company->hostname) ? $company->hostname : '';
        // Force 2-decimal places only
        $amount = round($amount, 2);

        //redirection URL
        $redirect_url = Configure::get('Blesta.gw_callback_url')
            . Configure::get('Blesta.company_id') . '/ccavenue/'
            . $this->ifSet($contact_info['client_id']);

        $order_id = $this->ifSet($contact_info['client_id']) . '-' . time();
        $Merchant_Id =  $this->ifSet($this->meta['merchant_id']);
        $encryption_key = $this->ifSet($this->meta['encryption_key']);

        // Filling the response parameters
        $fields = [
            'merchant_id' => $Merchant_Id,
            'amount' => $amount,
            'currency' => $this->currency,
            'order_id' => $order_id,
            'redirect_url' => $redirect_url,
            'billing_name' => $this->clean(
                $this->ifSet($contact_info['first_name']) .' '.  $this->ifSet($contact_info['last_name'])
            ),
            'billing_address' => $this->clean(
                $this->ifSet($contact_info['address1']) . ' ' . $this->ifSet($contact_info['address2'])
            ),
            'billing_city' =>  $this->ifSet($contact_info['city']),
            'billing_state' => $this->ifSet($contact_info['state']['name']),
            'billing_zip' => $this->ifSet($contact_info['zip']),
            'billing_country' => trim($this->ifSet($contact_info['country']['name'])),
            'billing_tel' => $this->clean($this->getContact($client)),
            'billing_email' => $this->ifSet($client->email),
            'delivery_name' => $this->clean(
                $this->ifSet($contact_info['first_name']) .' '.  $this->ifSet($contact_info['last_name'])
            ),
            'delivery_address' => $this->clean(
                $this->ifSet($contact_info['address1']) . ' ' . $this->ifSet($contact_info['address2'])
            ),
            'delivery_city' => $this->ifSet($contact_info['city']),
            'delivery_state' => $this->ifSet($contact_info['state']['name']),
            'delivery_zip' => $this->ifSet($contact_info['zip']),
            'delivery_country' => trim($this->ifSet($contact_info['country']['name'])),
            'delivery_tel' => $this->clean($this->getContact($client)),
            'merchant_param1' =>  $this->serializeInvoices($invoice_amounts),
            'merchant_param2' => $this->ifSet($client->id)
        ];

        $this->view = $this->makeView('process', 'default', str_replace(ROOTWEBDIR, '', dirname(__FILE__) . DS));

        $merchant_data='';
        foreach ($fields as $Key => $Value) {
            $merchant_data .= $Key . '=' . $Value .'&';
        }
        rtrim($merchant_data, '&');

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html']);

        $this->view->set(
            'post_to',
            ($this->ifSet($this->meta['test_mode']) == 'true' ? $this->test_url : $this->ccavenue_url)
        );

        // Method for encrypting the data.
        $encRequest = $util->encrypt($merchant_data, $encryption_key);
        $this->view->set('encRequest', $encRequest);
        $this->view->set('access_code', $this->ifSet($this->meta['access_code']));
        $this->view->set('merchant_id', $this->ifSet($Merchant_Id));

        // Log request received
        return $this->view->fetch();
    }

    /**
     * Validates the incoming POST/GET response from the gateway to ensure it is
     * legitimate and can be trusted.
     *
     * @param array $get The GET data for this request
     * @param array $post The POST data for this request
     * @return array An array of transaction data, sets any errors using Input if the data fails to validate
     *  - client_id The ID of the client that attempted the payment
     *  - amount The amount of the payment
     *  - currency The currency of the payment
     *  - invoices An array of invoices and the amount the payment should be applied to (if any) including:
     *      - id The ID of the invoice to apply to
     *      - amount The amount to apply to the invoice
     *  - status The status of the transaction (approved, declined, void, pending, reconciled, refunded, returned)
     *  - reference_id The reference ID for gateway-only use with this transaction (optional)
     *  - transaction_id The ID returned by the gateway to identify this transaction
     *  - parent_transaction_id The ID returned by the gateway to identify this
     *      transaction's original transaction (in the case of refunds)
     */
    public function validate(array $get, array $post)
    {
        // put in the 32 bit working key in the quotes provided here
        $encryption_key = $this->ifSet($this->meta['encryption_key']);
        // This is the response sent by the CCAvenue Server
        $enc_response = $this->ifSet($post['encResp']);
        Loader::load(dirname(__FILE__) . DS . 'lib' . DS . 'lib_functions.php');
        $util = new LibFunctions();
        if ($enc_response) {
            // AES Decryption used as per the specified working key.
            $rec_string = $util->decrypt($enc_response, $encryption_key);
            $all_params = [];
            parse_str($rec_string, $all_params);
        } else {
            $all_params=$post;
        }

        $url = ($this->ifSet($this->meta['test_mode']) == 'true' ? $this->test_url : $this->ccavenue_url);

        $response_status = strtolower($this->ifSet($all_params['order_status']));
        if ($response_status == 'success') {
            $status = 'approved';
            $this->log($url, serialize($all_params), 'output', true);
        } elseif ($response_status == 'failure' || $response_status == 'aborted') {
            $this->log($url, serialize($all_params), 'output', false);
            $this->Input->setErrors(
                [
                    'authentication' => [
                        'response' => Language::_('Ccavenue.!error.authentication.log', true)
                    ]
                ]
            );
            $status = 'declined';
        } else {
            $this->Input->setErrors(
                ['security' => ['response' => Language::_('Ccavenue.!error.security.response', true)]]
            );
            $this->log($url, serialize($all_params), 'output', false);
            $status = 'declined';
        }

        return  [
            'client_id' => $this->ifSet($all_params['merchant_param2'], $this->ifSet($get[2])),
            'amount' => $this->ifSet($all_params['amount']),
            'currency' => $this->ifSet($all_params['currency']),
            'invoices' => $this->unserializeInvoices($this->ifSet($all_params['merchant_param1'])),
            'status' => $status,
            'reference_id' => $this->ifSet($all_params['tracking_id']),
            'transaction_id' => $this->ifSet($all_params['order_id']),
            'parent_transaction_id' => null
        ];
    }

    /**
     * Returns data regarding a success transaction. This method is invoked when
     * a client returns from the non-merchant gateway's web site back to Blesta.
     *
     * @param array $get The GET data for this request
     * @param array $post The POST data for this request
     * @return array An array of transaction data, may set errors using Input if the data appears invalid
     *  - client_id The ID of the client that attempted the payment
     *  - amount The amount of the payment
     *  - currency The currency of the payment
     *  - invoices An array of invoices and the amount the payment should be applied to (if any) including:
     *      - id The ID of the invoice to apply to
     *      - amount The amount to apply to the invoice
     *  - status The status of the transaction (approved, declined, void, pending, reconciled, refunded, returned)
     *  - transaction_id The ID returned by the gateway to identify this transaction
     *  - parent_transaction_id The ID returned by the gateway to identify this transaction's original transaction
     */
    public function success(array $get, array $post)
    {
        return [
            'client_id' => $this->ifSet($post['merchant_param2'], $this->ifSet($get[2])),
            'amount' => $this->ifSet($post['amount']),
            'currency' => $this->ifSet($post['currency']),
            'invoices' => $this->unserializeInvoices($this->ifSet($post['merchant_param1'])),
            'status' => 'approved',
            'transaction_id' => $this->ifSet($post['order_id']),
            'parent_transaction_id' => null
        ];
    }

    /**
     * Serializes an array of invoice info into a string
     *
     * @param array A numerically indexed array invoices info including:
     *  - id The ID of the invoice
     *  - amount The amount relating to the invoice
     * @return string A serialized string of invoice info in the format of key1=value1|key2=value2
     */
    private function serializeInvoices(array $invoices)
    {
        $str = '';
        foreach ($invoices as $i => $invoice) {
            $str .= ($i > 0 ? ',' : '') . $invoice['id'] . ' ' . $invoice['amount'];
        }
        return $str;
    }

    /**
     * Unserializes a string of invoice info into an array
     *
     * @param string A serialized string of invoice info in the format of key1=value1|key2=value2
     * @return array A numerically indexed array invoices info including:
     *  - id The ID of the invoice
     *  - amount The amount relating to the invoice
     */
    private function unserializeInvoices($str)
    {
        $invoices = [];
        $temp = explode(',', $str);
        foreach ($temp as $pair) {
            $pairs = explode(' ', $pair, 2);
            if (count($pairs) != 2) {
                continue;
            }
            $invoices[] = ['id' => $pairs[0], 'amount' => $pairs[1]];
        }
        return $invoices;
    }

    /**
     * Function to remove all special characters
     * @param $string
     * @return mixed
     */
    private function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        return str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
    }

    /**
     * This function return contact number for the given client.
     * @param $client
     * @return string
     */
    private function getContact($client)
    {
        // Get any phone/fax numbers
        $contact_numbers = $this->Contacts->getNumbers($client->contact_id);

        // Set any contact numbers (only the first of a specific type found)
        $data = '';
        foreach ($contact_numbers as $contact_number) {
            switch ($contact_number->location) {
                case 'home':
                    // Set home phone number
                    if ($contact_number->type == 'phone') {
                        $data = $contact_number->number;
                    }
                    break;
                case 'work':
                    // Set work phone/fax number
                    if ($contact_number->type == 'phone') {
                        $data['office_tel'] = $contact_number->number;
                    }
                    // No break?
                case 'mobile':
                    // Set mobile phone number
                    if ($contact_number->type == 'phone') {
                        $data = $contact_number->number;
                    }
                    break;
            }
        }
        if (trim($data)=='') {
            return '9391919191'; // dummy number
        }
        return preg_replace('/[^0-9]/', '', $data);
    }
}
