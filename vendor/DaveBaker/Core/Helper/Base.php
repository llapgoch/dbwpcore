<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Base
 * @package DaveBaker\Core\Helper
 */
class Base extends \DaveBaker\Core\Base
{
    /**
     * @return \DaveBaker\Core\Config\ConfigInterface
     */
    public function getGeneralConfig()
    {
        return $this->getApp()->getGeneralConfig();
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function getGeneralConfigValue($key)
    {
        if(!($configValue = $this->getGeneralConfig()->getConfigValue($key))){
            throw new Exception("Config value not set: {$key}");
        }

        return $configValue;
    }

}