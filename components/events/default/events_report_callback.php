<?php
/**
 * Handle all default Report events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2018, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class EventsReportCallback extends EventCallback
{
    /**
     * Handle Report.clientData events
     *
     * @param EventObject $event An event object for Report.clientData events
     * @return EventObject The processed event object
     */
    public static function clientData(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
