<?php
namespace Blesta\Core\Util\Events\Observers;

use Blesta\Core\Util\Events\Observer;
use Blesta\Core\Util\Events\Common\EventInterface;

/**
 * The ClientGroups event observer
 *
 * @package blesta
 * @subpackage blesta.core.Util.Events.Observers
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ClientGroups extends Observer
{
    /**
     * Handle ClientGroups.add events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for ClientGroups.add events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function add(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle ClientGroups.edit events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for ClientGroups.edit events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function edit(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle ClientGroups.delete events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for ClientGroups.delete events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function delete(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }
}
