<?php
namespace Blesta\Core\Util\Events\Observers;

use Blesta\Core\Util\Events\Observer;
use Blesta\Core\Util\Events\Common\EventInterface;

/**
 * The Packages event observer
 *
 * @package blesta
 * @subpackage blesta.core.Util.Events.Observers
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Packages extends Observer
{
    /**
     * Handle Packages.add events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Packages.add events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function add(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Packages.edit events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Packages.edit events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function edit(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Packages.delete events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Packages.delete events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function delete(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }
}
