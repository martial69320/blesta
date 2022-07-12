<?php
/**
 * Handle all default AppController events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\AppController
 */
class EventsAppControllerCallback extends EventCallback
{

    /**
     * Handle AppController.preAction events.
     *
     * @param EventObject $event An event object for AppController.preAction events
     * @return EventObject The processed event object
     */
    public static function preAction(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle AppController.structure events.
     *
     * @param EventObject $event An event object for AppController.structure events
     * @return EventObject The processed event object
     */
    public static function structure(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
