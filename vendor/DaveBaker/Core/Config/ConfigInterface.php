<?php

namespace DaveBaker\Core\Config;
/**
 * Interface ConfigInterface
 * @package DaveBaker\Core\Config
 */
interface ConfigInterface
{
    public function getConfig();
    public function getConfigValue($key);
}