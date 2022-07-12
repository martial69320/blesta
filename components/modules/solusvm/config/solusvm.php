<?php
// Sets whether or not to generate passwords with special characters (non-alpha-numeric)
// since a bug exists in SolusVM <= v1.14 that may cause the server's IP address and user password to not be updated
Configure::set('Solusvm.password.allow_special_characters', false);

// Polling interval for how often to refresh page content after an action is performed on pages that support it
// Note: Set to number of milliseconds (1000 = 1 second)
Configure::set('Solusvm.page_refresh_rate_fast', '5000');

// Polling interval for how often to refresh page content on pages that support it
// Note: Set to number of milliseconds (1000 = 1 second)
Configure::set('Solusvm.page_refresh_rate', '30000');
