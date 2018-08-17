<?php
namespace DaveBaker\Core\WP\Config;

class Base implements ConfigInterface
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getConfigValue($key)
    {
        if(isset($this->config[$key])){
            return $this->config[$key];
        }

        return null;
    }

    protected function mergeConfig($config = [])
    {
        $this->config = array_replace_recursive($this->getConfig(), $config);
    }
}