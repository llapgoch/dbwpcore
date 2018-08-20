<?php

namespace DaveBaker\Core\WP\Object;

class Manager
{
    const SINGLETON_KEY = 'singleton';
    const DEFINITION_KEY = 'definition';

    const BASE_HELPER_DEFINITION = '\DaveBaker\Core\Helper\{{helperName}}';
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var array
     */

    protected $config;

    protected $singletonCache = [];

    public function __construct(
        \DaveBaker\Core\WP\Config\ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getDefaults()
    {
        return $this->config->getConfig();
    }

    public function getHelper($helperName){
        $helperPath = str_replace('{{helperName}}', $helperName, self::BASE_HELPER_DEFINITION);
        return $this->get($helperPath);
    }
    
    /**
     * @param $identifier
     * @return mixed
     */
    public function getDefaultClassName($identifier)
    {
        if(isset($this->getDefaults()[$identifier])){
            return $this->getDefaults()[$identifier]['definition'];
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
            $isSingleton = $this->isSingleton($identifier);

            if($isSingleton){
                if(isset($this->singletonCache[$identifier])){
                    return $this->singletonCache[$identifier];
                }
            }
            
            $reflector = new \ReflectionClass($this->getDefaultClassName($identifier));
            $object = $reflector->newInstanceArgs($args);

            if($isSingleton){
                $this->singletonCache[$identifier] = $object;
            }

            return $object;
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
        if(isset($this->getDefaults()[$identifier])){
            return $this->getDefaults()[$identifier];
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