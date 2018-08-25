<?php

namespace DaveBaker\Core\Config;
/**
 * Class Layout
 * @package DaveBaker\Core\Config
 *
 * class should be extended locally
 */
class Layout extends Base
{
    protected $config = [
        'templates' => [
            0 => 'dbwpcore' . DS . 'design' . DS . 'templates'
        ]
    ];
}