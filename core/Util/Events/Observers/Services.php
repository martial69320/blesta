<?php
namespace Blesta\Core\Util\Events\Observers;

use Blesta\Core\Util\Events\Observer;
use Blesta\Core\Util\Events\Common\EventInterface;

/**
 * The Services event observer
 *
 * @package blesta
 * @subpackage blesta.core.Util.Events.Observers
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Services extends Observer
{
    /**
     * Handle Services.add events.
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Services.add events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function add(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Services.edit events.
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Services.edit events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function edit(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Services.cancel events.
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Services.cancel events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function cancel(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Services.suspend events.
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Services.suspend events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function suspend(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Services.unsuspend events.
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Services.unsuspend events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function unsuspend(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }
}
