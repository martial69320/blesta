<?php
/**
 * Handle all default Navigation events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2017, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\Navigation
 */
class EventsNavigationCallback extends EventCallback
{
    /**
     * Handle Navigation.getSearchOptions events
     *
     * @param EventObject $event An event object for Navigation.getSearchOptions events
     * @return EventObject The processed event object
     */
    public static function getSearchOptions(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
