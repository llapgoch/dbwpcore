<?php

namespace DaveBaker\Core\Config;

/**
 * Class General
 * @package DaveBaker\Core\Config
 *
 * class should be extended locally
 */
class General extends Base {
    protected $config = [
        'datePattern' => '/^(\d{2})\/(\d{2})\/(\d{4})$/',
        'dbDateFormat' => 'Y-m-d H:i:s'
    ];
}