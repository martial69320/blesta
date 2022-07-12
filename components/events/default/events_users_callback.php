<?php
/**
 * Handle all default Users events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\Users
 */
class EventsUsersCallback extends EventCallback
{

    /**
     * Handle Users.delete events
     *
     * @param EventObject $event An event object for Users.delete events
     * @return EventObject The processed event object
     */
    public static function delete(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Users.login events
     *
     * @param EventObject $event An event object for Users.login events
     * @return EventObject The processed event object
     */
    public static function login(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Users.logout events
     *
     * @param EventObject $event An event object for Users.logout events
     * @return EventObject The processed event object
     */
    public static function logout(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
