<?php
// Errors
$lang['Stripe_gateway.!error.auth'] = 'The gateway could not authenticate.';
$lang['Stripe_gateway.!error.api_key.empty'] = 'Please enter an API Key.';
$lang['Stripe_gateway.!error.json_required'] = 'The JSON extension is required for this gateway.';

$lang['Stripe_gateway.name'] = 'Stripe';

// Settings
$lang['Stripe_gateway.api_key'] = 'API Secret Key';
$lang['Stripe_gateway.tooltip_api'] = 'Your API Secret Key is specific to either live or test mode. Be sure you are using the correct key.';
$lang['Stripe_gateway.stored'] = 'Store Card Information Offsite';
$lang['Stripe_gateway.tooltip_stored'] = 'Check this box to store payment account card information with Stripe rather than within Blesta.';

// Charge description
$lang['Stripe_gateway.charge_description'] = 'Charge for %1$s'; // Where %1$s is a comma seperated list of invoice ID display codes
