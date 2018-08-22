<?php

namespace DaveBaker\Core\WP\Object;

class Manager extends \DaveBaker\Core\WP\Base
{
    const SINGLETON_KEY = 'singleton';
    const DEFINITION_KEY = 'definition';

    const BASE_HELPER_DEFINITION = '\DaveBaker\Core\WP\Helper\{{helperName}}';
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
        \DaveBaker\Core\App $app,
        \DaveBaker\Core\WP\Config\ConfigInterface $config
    ) {
        parent::__construct($app);
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
        $helper = $this->get($helperPath, [$this->getApp()]);

        return $helper;
    }

    /**
     * @param $className string
     * @return object
     * @throws Exception
     */
    public function getModel($className)
    {
        $model = $this->get($className, [$this->getApp()]);

        if(!$model instanceof \DaveBaker\Core\WP\Model\Db\Base){
            throw new Exception('Created DB Model is not compatible with Base Model');
        }

        return $model;
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