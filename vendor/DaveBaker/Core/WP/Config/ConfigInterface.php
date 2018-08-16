<?php

namespace DaveBaker\Core\WP\Config;

interface ConfigInterface
{
    public function getConfig();
    public function getConfigValue($key);
}