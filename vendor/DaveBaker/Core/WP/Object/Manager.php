<?php

namespace DaveBaker\Core\WP\Object;

class Manager
{
    const SINGLETON_KEY = 'singleton';
    const DEFINITION_KEY = 'definition';
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var array
     */
    protected $singletonCache = [];


    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    protected $defaults = [
        '\DaveBaker\Core\WP\Option\Manager' => [
            'definition' => '\DaveBaker\Core\WP\Option\Manager',
            'singleton' => false
        ],
        '\DaveBaker\Core\WP\Page\Manager' => [
            'definition' => '\DaveBaker\Core\WP\Page\Manager',
            'singleton' => false
        ]
    ];

    /**
     * @param $identifier
     * @return mixed
     */
    public function getDefaultClassName($identifier)
    {
        if(isset($this->defaults[$identifier])){
            return $this->defaults[$identifier]['definition'];
        }

        return $identifier;
    }

    /**
     * @param $identifier
     * @param array $args
     * @return object
     * @throws Exception
     */
    public function get($identifier, $args = [])
    {
        try {
            $reflector = new \ReflectionClass($this->getDefaultClassName($identifier));
            return $reflector->newInstanceArgs($args);
        } catch(\Exception $e){
            // Localised exception
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $identifier string
     * @return bool|mixed
     */
    protected function getDefinition($identifier)
    {
        if(isset($this->defaults[$identifier])){
            return $this->defaults[$identifier];
        }

        return false;
    }

    /**
     * @param $identifier string
     * @return bool
     */
    public function isSingleton($identifier)
    {
        if($definition = $this->getDefinition($identifier)){
            return $definition[self::SINGLETON_KEY];
        }

        return false;
    }
}