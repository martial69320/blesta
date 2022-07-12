<?php
// Gateway name
$lang['_2Checkout.name'] = '2Checkout';

// Settings
$lang['_2Checkout.api_version'] = 'API Version';
$lang['_2Checkout.vendor_id'] = 'Vendor Account Number';
$lang['_2Checkout.merchant_code'] = 'Merchant Code';
$lang['_2Checkout.secret_key'] = 'Secret Key';
$lang['_2Checkout.secret_word'] = 'Secret Word';
$lang['_2Checkout.buy_link_secret_word'] = 'Buy Link Secret Word';
$lang['_2Checkout.api_username'] = 'API Username';
$lang['_2Checkout.api_username_note'] = 'This, as well as the API Password, are required in order to process refunds through 2Checkout.';
$lang['_2Checkout.api_password'] = 'API Password';
$lang['_2Checkout.sandbox'] = 'Sandbox';
$lang['_2Checkout.test_mode'] = 'Test Mode';

// Refund
$lang['2Checkout.refund.comment'] = 'Initiating a refund for %1$s.'; // %1$s is the refund amount

// Process form
$lang['_2Checkout.buildprocess.submit'] = 'Pay with 2Checkout';

// Get API Versions
$lang['_2Checkout.getapiversions.v1'] = 'Version 1 (Legacy)';
$lang['_2Checkout.getapiversions.v5'] = 'Version 5';

// Errors
$lang['_2Checkout.!error.api_version.valid'] = 'Please enter a valid API version.';
$lang['_2Checkout.!error.vendor_id.empty'] = 'Please enter your Vendor Account Number.';
$lang['_2Checkout.!error.merchant_code.empty'] = 'Please enter your Merchant Code.';
$lang['_2Checkout.!error.secret_word.empty'] = 'Please enter your Secret Word.';
$lang['_2Checkout.!error.buy_link_secret_word.empty'] = 'Please enter your Buy Link Secret Word.';
$lang['_2Checkout.!error.secret_key.empty'] = 'Please enter your Secret Key.';
$lang['_2Checkout.!error.test_mode.valid'] = 'Test mode must be set to either \'true\' or \'false\'.';
$lang['_2Checkout.!error.sandbox.valid'] = 'Sandbox must be set to either \'true\' or \'false\'.';
$lang['_2Checkout.!error.key.valid'] = 'The key used to verify this sale originated from 2Checkout is invalid.';
$lang['_2Checkout.!error.hash.valid'] = 'The hash used to verify this sale originated from 2Checkout is invalid.';
$lang['_2Checkout.!error.credit_card_processed.completed'] = 'The transaction was not processed successfully.';
$lang['_2Checkout.!error.sid.valid'] = 'The Vendor Account Number does not match the account number provided by the transaction.';
