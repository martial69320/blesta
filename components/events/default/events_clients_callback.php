<?php
/**
 * Handle all default Clients events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2017, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\Clients
 */
class EventsClientsCallback extends EventCallback
{
    /**
     * Handle Clients.create events
     *
     * @param EventObject $event An event object for Clients.create events
     * @return EventObject The processed event object
     */
    public static function create(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Clients.add events
     *
     * @param EventObject $event An event object for Clients.add events
     * @return EventObject The processed event object
     */
    public static function add(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Clients.edit events
     *
     * @param EventObject $event An event object for Clients.edit events
     * @return EventObject The processed event object
     */
    public static function edit(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Clients.delete events
     *
     * @param EventObject $event An event object for Clients.delete events
     * @return EventObject The processed event object
     */
    public static function delete(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Clients.addNote events
     *
     * @param EventObject $event An event object for Clients.addNote events
     * @return EventObject The processed event object
     */
    public static function addNote(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Clients.editNote events
     *
     * @param EventObject $event An event object for Clients.editNote events
     * @return EventObject The processed event object
     */
    public static function editNote(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Clients.deleteNote events
     *
     * @param EventObject $event An event object for Clients.deleteNote events
     * @return EventObject The processed event object
     */
    public static function deleteNote(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
