<?php
Configure::set('DirectAdmin.password_requirements', [
    ["A-Z"],
    ["a-z"],
    ["0-9"],
    ["!", "\"", "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "]", "^", "_", "`", "{", "|", "}"]
]);
Configure::set('DirectAdmin.password_length', 12);
