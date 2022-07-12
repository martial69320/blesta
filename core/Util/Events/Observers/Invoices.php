<?php
namespace Blesta\Core\Util\Events\Observers;

use Blesta\Core\Util\Events\Observer;
use Blesta\Core\Util\Events\Common\EventInterface;

/**
 * The Invoices event observer
 *
 * @package blesta
 * @subpackage blesta.core.Util.Events.Observers
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Invoices extends Observer
{
    /**
     * Handle Invoices.add events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Invoices.add events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function add(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Invoices.edit events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Invoices.edit events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function edit(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Invoices.setClosed events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Invoices.setClosed events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function setClosed(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }
}
