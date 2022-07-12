<?php
/**
 * Handle all default Contacts events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2017, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\Contacts
 */
class EventsContactsCallback extends EventCallback
{
    /**
     * Handle Contacts.add events
     *
     * @param EventObject $event An event object for Contacts.add events
     * @return EventObject The processed event object
     */
    public static function add(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Contacts.edit events
     *
     * @param EventObject $event An event object for Contacts.edit events
     * @return EventObject The processed event object
     */
    public static function edit(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Contacts.delete events
     *
     * @param EventObject $event An event object for Contacts.delete events
     * @return EventObject The processed event object
     */
    public static function delete(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
