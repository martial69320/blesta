<?php
namespace Blesta\Core\Util\Captcha\Captchas;

use Blesta\Core\Util\Captcha\Common\AbstractCaptcha;
use ReCaptcha\ReCaptcha as GoogleRecaptcha;
use RuntimeException;

/**
 * Google reCAPTCHA integration
 *
 * @package blesta
 * @subpackage blesta.core.Util.Captcha.Captchas
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ReCaptcha extends AbstractCaptcha
{
    /**
     * @var array An array of options
     */
    private $options = [];
    /**
     * @var string The Google reCaptcha JavaScript API URL
     */
    private $apiUrl = 'https://www.google.com/recaptcha/api.js';

    /**
     * Builds the HTML content to render the reCaptcha
     *
     * @return string The HTML
     */
    public function buildHtml()
    {
        $key = $this->Html->safe($this->Html->ifSet($this->options['site_key']));
        $lang = $this->Html->safe($this->Html->ifSet($this->options['lang']));
        $apiUrl = $this->Html->safe($this->apiUrl . (!empty($lang) ? '?hl=' . $lang : ''));

        $html = <<< HTML
<div class="g-recaptcha" data-sitekey="$key"></div>
<script type="text/javascript" src="$apiUrl"></script>
HTML;

        return $html;
    }

    /**
     * Sets reCaptcha options
     *
     * @param array $options An array of options including:
     *
     *  - site_key The reCaptcha site key
     *  - shared_key The reCaptcha shared key
     *  - lang The user's language (e.g. "en" for English)
     *  - ip_address The user's IP address (optional)
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Verifies whether or not the captcha is valid
     *
     * @param array $data An array of data to validate against, including:
     *
     *  - response The value of 'g-recaptcha-response' in the submitted form
     * @return bool Whether or not the captcha is valid
     */
    public function verify(array $data)
    {
        $success = false;

        // Attempt to verify the captcha was accepted
        try {
            $recaptcha = new GoogleRecaptcha($this->Html->ifSet($this->options['shared_key']));

            $response = $recaptcha->verify(
                $this->Html->ifSet($data['response']),
                $this->Html->ifSet($data['ip_address'], null)
            );

            $success = $response->isSuccess();
        } catch (RuntimeException $e) {
            // ReCaptcha could not process the request due to missing data
            $this->setErrors(['recaptcha' => ['error' => $e->getMessage()]]);
        }

        return $success;
    }
}
