<?php

namespace DaveBaker\Core\Config;
/**
 * Class Base
 * @package DaveBaker\Core\Config
 */
class Base implements ConfigInterface
{
    /** @var array  */
    protected $config = [];

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getConfigValue($key)
    {
        if(isset($this->config[$key])){
            return $this->config[$key];
        }

        return null;
    }

    /**
     * @param array $config
     */
    protected function mergeConfig($config = [])
    {
        $this->config = array_replace_recursive($this->getConfig(), $config);
    }
}