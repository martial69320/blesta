<?php
namespace Blesta\Core\Util\Events\Observers;

use Blesta\Core\Util\Events\Observer;
use Blesta\Core\Util\Events\Common\EventInterface;

/**
 * The Clients event observer
 *
 * @package blesta
 * @subpackage blesta.core.Util.Events.Observers
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Clients extends Observer
{
    /**
     * Handle Clients.create events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.create events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function create(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Clients.add events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.add events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function add(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Clients.edit events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.edit events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function edit(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Clients.delete events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.delete events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function delete(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Clients.addNote events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.addNote events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function addNote(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Clients.editNote events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.editNote events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function editNote(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }

    /**
     * Handle Clients.deleteNote events
     *
     * @param Blesta\Core\Util\Events\Common\EventInterface $event An event object for Clients.deleteNote events
     * @return Blesta\Core\Util\Events\Common\EventInterface The processed event object
     */
    public static function deleteNote(EventInterface $event)
    {
        return parent::triggerEvent($event);
    }
}
