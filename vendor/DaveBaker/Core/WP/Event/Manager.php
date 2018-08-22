<?php

/**
 * An event manager should be created for each use so that prefixes are all different across different event types
 */

namespace DaveBaker\Core\WP\Event;

use DaveBaker\WP\Event\Exception;

class Manager extends \DaveBaker\Core\WP\Base
{
    /** @var string */
    protected $namespaceCode = "event";
    /** @var array */
    protected $events = [];
    protected $context;

    /**
     * @param array $eventIdentifiers
     * @param array $args
     * @return Context
     * @throws Exception
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    public function fire($eventIdentifiers = [], $args = [])
    {
        if(!$this->context) {
            $this->context = $this->getApp()->getObjectManager()->get('\DaveBaker\Core\WP\Event\Context', [$this->getApp()]);
        }

        $this->context->unsetData();

        foreach($args as $k => $arg){
            $this->context->setData($k, $arg);
        }

        if(!is_array($eventIdentifiers)){
            $eventIdentifiers = [$eventIdentifiers];
        }

        foreach($eventIdentifiers as $eventIdentifier) {
            if (!($events = $this->getEvents($eventIdentifier))) {
                return $this->context;
            }

            try {
                foreach ($events as $event) {
                    call_user_func($event['callback'], $this->context);
                }
            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        return $this->context;
    }

    /**
     * @param $eventIdentifier
     * @param array $callback
     * @param bool $allowMultiples
     * $callback should be an array of class, method, or an anonymous function
     */
    public function add($eventIdentifier, $callback = [], $allowMultiples = false)
    {
        if(!$this->getEvents($eventIdentifier)){
            $this->events[$eventIdentifier] = [];
        }

        if(!$allowMultiples) {
            foreach ($this->events[$eventIdentifier] as $event) {
                // Event has already been added
                if ($event['callback'] == $callback) {
                    return;
                }
            }
        }

        $this->events[$eventIdentifier][] = [
            "callback" => $callback
        ];

        // Also add to Wordpress' filter system
        add_filter($eventIdentifier, $callback);
    }

    /**
     * @param $eventIdentifier string
     * @param $callback array|bool
     * @return $this
     */
    public function remove($eventIdentifier, $callback = false)
    {
        if(!isset($this->events[$eventIdentifier])){
            return $this;
        }

        if(!$callback){
            unset($this->events[$eventIdentifier]);
            return $this;
        }

        foreach($this->events[$eventIdentifier] as $k => $event){
            if ($event['callback'] == $callback) {
                unset($this->events[$eventIdentifier][$k]);
            }
        }

        return $this;
    }

    // Override with stubs, these methods exist in the base class
    public function fireEvent($event, $params = []){}
    public function addEvent($event, $callback, $allowMultiples = false){}
    public function removeEvent($event, $callback = false){}

    /**
     * @param $eventIdentifier
     * @return array|mixed
     */
    protected function getEvents($eventIdentifier)
    {
        if(isset($this->events[$eventIdentifier])){
           return $this->events[$eventIdentifier];
        }

        return [];
    }

}