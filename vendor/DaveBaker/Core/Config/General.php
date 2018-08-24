<?php

namespace DaveBaker\Core\Config;

/**
 * Class Installer
 * @package DaveBaker\Core\Config
 *
 * class should be extended locally
 */
class General extends Base {
    protected $config = [
        'datePattern' => '/^(\d{2})\/(\d{2})\/(\d{4})$/'
    ];
}