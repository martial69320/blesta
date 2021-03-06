# GoCardless Gateway

[![Build Status](https://travis-ci.org/blesta/gateway-gocardless.svg?branch=master)](https://travis-ci.org/blesta/gateway-gocardless) [![Coverage Status](https://coveralls.io/repos/github/blesta/gateway-gocardless/badge.svg?branch=master)](https://coveralls.io/github/blesta/gateway-gocardless?branch=master)

This is a non-merchant gateway for Blesta that integrates with [GoCardless](https://gocardless.com/).

## Install the Gateway

1. You can install the gateway via composer:

    ```
    composer require blesta/gocardless
    ```

2. Upload the source code to a /components/gateways/nonmerchant/gocardless/ directory within
your Blesta installation path.

    For example:

    ```
    /var/www/html/blesta/components/nonmerchant/gocardless/
    ```

3. Log in to your admin Blesta account and navigate to
> Settings > Payment Gateways

4. Find the GoCardless gateway and click the "Install" button to install it

5. Configure by setting your "Access Token" and "Webhook Secret", both of which are found in the Developer section of your GoCardless account.
