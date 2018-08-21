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

    /**
     * @param $eventIdentifier
     * @throws Exception
     */
    public function fire($eventIdentifier){
        if(!($events = $this->getEvents($eventIdentifier))){
            return;
        }

        try {
            foreach ($events as $event) {
                call_user_func($event['method'], $event['args']);
            }
        }catch (\Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $eventIdentifier
     * @param $method
     */
    public function register($eventIdentifier, $method = [], $args = [])
    {
        if(!$this->getEvents($eventIdentifier)){
            $this->events[$eventIdentifier] = [];
        }

        $this->events[$eventIdentifier][] = [
            "method" => $method,
            "args" => $args
        ];

    }

    /**
     * @param $eventIdentifier
     * @param $method
     */
    public function unregister($eventIdentifier, $method)
    {
        if(!($events = &$this->getEvents($eventIdentifier))){
            return;
        }

        foreach($events as $k => $event){
            if($event === $method){
                unset($events[$k]);
            }
        }
    }

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