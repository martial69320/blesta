<?php

use Minphp\Bridge\Initializer;
use Minphp\Record\Record as MinphpRecord;

/**
 * Record Bridge
 *
 * Intended for legacy backwards compatibility ONLY.
 * Use Minphp\Record\Record instead.
 */
class Record extends MinphpRecord
{
    /**
     * Initialize
     */
    public function __construct(array $dbInfo = null)
    {
        $container = Initializer::get()->getContainer();

        if (null === $dbInfo) {
            $dbInfo = [];
        }

        parent::__construct($dbInfo);

        if (empty($dbInfo)) {
            $this->setConnection($container->get('pdo'));
        }
    }
}
