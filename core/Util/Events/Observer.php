<?php
namespace Blesta\Core\Util\Events;

use Blesta\Core\Util\Events\Common\EventInterface;
use Loader;
use stdClass;
use Configure;
use EventObject;

/**
 * Observer for invoking triggers for an event
 *
 * @package blesta
 * @subpackage blesta.core.Util.Events
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Observer
{
    /**
     * Handle event triggers
     *
     * @param \Blesta\Core\Util\Events\Common\EventInterface $event The event to trigger
     * @return \Blesta\Core\Util\Events\Common\EventInterface $event The event triggered
     */
    public static function triggerEvent(EventInterface $event)
    {
        return self::triggerPluginEvent(self::triggerSystemEvent($event));
    }

    /**
     * Triggers the event for any system listeners
     *
     * @param \Blesta\Core\Util\Events\Common\EventInterface $event The event to trigger
     * @return \Blesta\Core\Util\Events\Common\EventInterface $event The event triggered
     */
    private static function triggerSystemEvent(EventInterface $event)
    {
        $parent = new stdClass();
        Loader::loadModels($parent, ['SystemEvents']);

        $SystemEvents = new $parent->SystemEvents();

        $event = $SystemEvents->trigger($event);
        unset($parent, $SystemEvents);

        return $event;
    }

    /**
     * Triggers the event for any plugin listeners
     *
     * @param \Blesta\Core\Util\Events\Common\EventInterface $event The event to trigger
     * @return \Blesta\Core\Util\Events\Common\EventInterface $event The event triggered
     */
    private static function triggerPluginEvent(EventInterface $event)
    {
        $parent = new stdClass();
        Loader::loadModels($parent, ['PluginManager']);

        $PluginManager = new $parent->PluginManager();

        #
        # TODO: the PluginManager expects to pass an "EventObject" to the plugin events, but this is deprecated
        # and should be removed in favor of the given $event EventInterface
        #
        // Create an EventObject based on the current EventInterface to perform the same actions
        // This is for backward compatibility only
        $tempObject = new stdClass();
        Loader::loadComponents($tempObject, ['Events']);
        $eventObject = new EventObject($event->getName(), $event->getParams());
        $eventObject->setReturnVal($event->getReturnValue());
        $eventObject = $PluginManager->invokeEvents(Configure::get('Blesta.company_id'), $eventObject);

        // Set the EventObject back to the EventInterface
        $event->setParams($eventObject->getParams());
        $event->setReturnValue($eventObject->getReturnVal());
        #
        # TODO: the above EventObject code can be replaced by the following once the plugin event handlers
        # no longer support the deprecated EventObject
        #
        #$event = $PluginManager->triggerEvents($event);
        unset($parent, $PluginManager);

        return $event;
    }
}
