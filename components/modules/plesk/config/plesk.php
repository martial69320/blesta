<?php
Configure::set('Plesk.password_requirements', [
    ["A-Z"],
    ["a-z"],
    ["0-9"],
    ["!", "@", "#", "$", "%", "^", "&", "*", "?", "_", "~"]
]);
Configure::set('Plesk.password_length', 16);
Configure::set('Plesk.password_minimum_characters_per_pool', 3);
