<?php
/**
 * Handle all default Transactions events callbacks
 *
 * @package blesta
 * @subpackage blesta.components.events.default
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 * @deprecated since 4.3.0 - use the namespaced Events under \Blesta\Core\Util\Events\Observers\Transactions
 */
class EventsTransactionsCallback extends EventCallback
{

    /**
     * Handle Transactions.add events
     *
     * @param EventObject $event An event object for Transactions.add events
     * @return EventObject The processed event object
     */
    public static function add(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }

    /**
     * Handle Transactions.edit events
     *
     * @param EventObject $event An event object for Transactions.edit events
     * @return EventObject The processed event object
     */
    public static function edit(EventObject $event)
    {
        return parent::triggerPluginEvent($event);
    }
}
