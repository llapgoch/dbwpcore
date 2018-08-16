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

    protected function mergeConfig($config = [])
    {
        $this->config = array_merge($this->getConfig(), $config);
    }
}