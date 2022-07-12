<?php
namespace Blesta\Core\Util\Captcha;

use Blesta\Core\Util\Captcha\Captchas\ReCaptcha;

/**
 * Captcha Factory
 *
 * Creates new captcha instances
 *
 * @package blesta
 * @subpackage blesta.components.events
 * @copyright Copyright (c) 2018, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class CaptchaFactory
{
    /**
     * Creates an instance of Google reCaptcha
     *
     * @param array $options An array of options including:
     *
     *  - site_key The reCaptcha site key
     *  - shared_key The reCaptcha shared key
     *  - lang The user's language (e.g. "en" for English)
     *  - ip_address The user's IP address (optional)
     */
    public function reCaptcha(array $options)
    {
        $recaptcha = new ReCaptcha();
        $recaptcha->setOptions($options);

        return $recaptcha;
    }
}
