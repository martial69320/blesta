<?php
/**
 * Abstract class that all Plugin handlers extend
 *
 * Defines all methods plugin handlers must inherit and provides all methods common between all
 * plugin handlers
 *
 * @package blesta
 * @subpackage blesta.components.plugins
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
abstract class Plugin
{
    /**
     * @var stdClass A stdClass object representing the configuration for this plugin
     */
    protected $config;
    /**
     * @var array An array of messages keyed by type (e.g. ['success' => ['message' => ['Message 1', 'Message 2']]])
     */
    private $messages = [];

    /**
     * Returns the name of this plugin
     *
     * @return string The common name of this plugin
     */
    public function getName()
    {
        if (isset($this->config->name)) {
            return $this->translate($this->config->name);
        }
        return null;
    }

    /**
     * Returns the version of this plugin
     *
     * @return string The current version of this plugin
     */
    public function getVersion()
    {
        if (isset($this->config->version)) {
            return $this->config->version;
        }
        return null;
    }

    /**
     * Returns the name and URL for the authors of this plugin
     *
     * @return array The name and URL of the authors of this plugin
     */
    public function getAuthors()
    {
        if (isset($this->config->authors)) {
            foreach ($this->config->authors as &$author) {
                $author = (array)$author;
            }
            return $this->config->authors;
        }
        return null;
    }

    /**
     * Performs any necessary bootstraping actions
     *
     * @param int $plugin_id The ID of the plugin being installed
     */
    public function install($plugin_id)
    {
    }

    /**
     * Performs migration of data from $current_version (the current installed version)
     * to the given file set version
     *
     * @param string $current_version The current installed version of this plugin
     * @param int $plugin_id The ID of plugin being upgraded
     */
    public function upgrade($current_version, $plugin_id)
    {
    }

    /**
     * Performs any necessary cleanup actions
     *
     * @param int $plugin_id The ID of the plugin being uninstalled
     * @param boolean $last_instance True if $plugin_id is the last instance across all companies for
     *  this plugin, false otherwise
     */
    public function uninstall($plugin_id, $last_instance)
    {
    }

    /**
     * Returns all actions to be configured for this widget (invoked after install() or upgrade(),
     * overwrites all existing actions)
     *
     * @return array A numerically indexed array containing:
     *
     *  - action The action to register for
     *  - uri The URI to be invoked for the given action
     *  - name The name to represent the action (can be language definition)
     *  - options An array of key/value pair options for the given action
     */
    public function getActions()
    {
    }

    /**
     * Returns all events to be registered for this plugin (invoked after install() or upgrade(),
     * overwrites all existing events)
     *
     * @return array A numerically indexed array containing:
     *
     *  - event The event to register for
     *  - callback A string or array representing a callback function or class/method. If a user (e.g.
     *      non-native PHP) function or class/method, the plugin must automatically define it when the plugin is loaded.
     *      To invoke an instance methods pass "this" instead of the class name as the 1st callback element.
     */
    public function getEvents()
    {
    }

    /**
     * Returns all tabs to display to an admin when managing a service
     *
     * @param stdClass $service An stdClass object representing the selected service
     * @return array An array of tabs in the format of method => array where array contains:
     *
     *  - name (required) The name of the link
     *  - href (optional) use to link to a different URL
     *      Example:
     *      array('methodName' => "Title", 'methodName2' => "Title2")
     *      array('methodName' => array('name' => "Title", 'href' => "https://blesta.com"))
     */
    public function getAdminServiceTabs(stdClass $service)
    {
        return [];
    }

    /**
     * Returns all tabs to display to a client when managing a service
     *
     * @param stdClass $service A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => array where array contains:
     *
     *  - name (required) The name of the link
     *  - icon (optional) use to display a custom icon
     *  - href (optional) use to link to a different URL
     *      Example:
     *      array('methodName' => "Title", 'methodName2' => "Title2")
     *      array('methodName' => array('name' => "Title", 'icon' => "icon"))
     */
    public function getClientServiceTabs(stdClass $service)
    {
        return [];
    }

    /**
     * Returns whether this plugin provides support for setting admin or client service tabs
     * @see Plugin::getAdminServiceTabs
     * @see Plugin::getClientServiceTabs
     *
     * @return bool True if the plugin supports service tabs, or false otherwise
     */
    public function allowsServiceTabs()
    {
        return false;
    }

    /**
     * Returns the relative path from this plugin's directory to the logo for
     * this plugin. Defaults to views/default/images/logo.png
     *
     * @return string The relative path to the plugin's logo
     */
    public function getLogo()
    {
        if (isset($this->config->logo)) {
            return $this->config->logo;
        }
        return 'views' . DS . 'default' . DS . 'images' . DS . 'logo.png';
    }

    /**
     * Runs the cron task identified by the key used to create the cron task
     *
     * @param string $key The key used to create the cron task
     * @see CronTasks::add()
     */
    public function cron($key)
    {
    }

    /**
     * Return all validation errors encountered
     *
     * @return mixed Boolean false if no errors encountered, an array of errors otherwise
     */
    public function errors()
    {
        if (isset($this->Input) && is_object($this->Input) && $this->Input instanceof Input) {
            return $this->Input->errors();
        }
    }

    /**
     * Sets a message
     *
     * @param string $type The type of message ('success', 'error", or 'notice')
     * @param string $message The message text to display
     */
    final protected function setMessage($type, $message)
    {
        if (!array_key_exists($type, $this->messages)) {
            $this->messages[$type] = ['message' => []];
        }

        $this->messages[$type]['message'][] = $message;
    }

    /**
     * Retrieves a set of messages set by the module
     *
     * @return array An array of messages
     */
    final public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Loads a given config file
     *
     * @param string $file The full path to the config file to load
     */
    protected function loadConfig($file)
    {
        if (!isset($this->Json)) {
            Loader::loadComponents($this, ['Json']);
        }

        if (file_exists($file)) {
            $this->config = $this->Json->decode(file_get_contents($file));
        }
    }

    /**
     * Translate the given str, or passthrough if no translation et
     *
     * @param string $str The string to translate
     * @return string The translated string
     */
    private function translate($str)
    {
        $pass_through = Configure::get('Language.allow_pass_through');
        Configure::set('Language.allow_pass_through', true);
        $str = Language::_($str, true);
        Configure::set('Language.allow_pass_through', $pass_through);

        return $str;
    }
}
