<?php
/**
 * Wrapper for JSON encode/decode functions
 *
 * @package blesta
 * @subpackage blesta.components.json
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since v4.5.0 - use php's standard json_encode and json_decode functions instead
 */
class Json
{
    /**
     * Performs JSON encode. Attempts to use built in PHP json_encode if available.
     *
     * @param mixed $val The value to encode
     * @return string The encoded data, in JSON format
     */
    public function encode($val)
    {
        return json_encode($val);
    }

    /**
     * Performs JSON decode. Attempts to use built in PHP json_decode if available.
     *
     * @param string $val The value to decode
     * @param bool $assoc True to return value as an associative array, false otherwise.
     * @return mixed The decoded value in PHP format.
     */
    public function decode($val, $assoc = false)
    {
        return json_decode($val, $assoc);
    }
}
