<?php
/**
 * Handle all default GatewayManager events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2017, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\GatewayManager
 */
class EventsGatewayManagerCallback extends EventCallback
{
    /**
     * Handle GatewayManager.add events
     *
     * @param EventObject $event An event object for GatewayManager.add events
     * @return EventObject The processed event object
     */
    public static function add(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle GatewayManager.edit events
     *
     * @param EventObject $event An event object for GatewayManager.edit events
     * @return EventObject The processed event object
     */
    public static function edit(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle GatewayManager.delete events
     *
     * @param EventObject $event An event object for GatewayManager.delete events
     * @return EventObject The processed event object
     */
    public static function delete(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
