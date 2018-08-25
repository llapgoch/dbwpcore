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
        'dateTimeDbFormat' => 'Y-m-d H:i:s',
        'dateLocalPattern' => '/^(\d{2})\/(\d{2})\/(\d{4})$/',
        'dateLocalOutputFormat' => 'l, F jS Y',
        'dateTimeLocalOutputFormat' => 'l, F jS Y, H:i A',
        'dateLocalShortOutputFormat' => 'j/n/Y',
        'dateTimeLocalShortOutputFormat' => 'j/n/Y H:i A'
    ];
}