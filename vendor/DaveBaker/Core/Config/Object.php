<?php

namespace DaveBaker\Core\Config;
/**
 * Class Object
 * @package DaveBaker\Core\Config
 *
 * class should be extended locally
 */
class Object extends Base
{
    /** @var array  */
    protected $config = [
        /* Config objects */
        '\DaveBaker\Core\Config\General' => [
            'singleton' => true
        ],
        '\DaveBaker\Core\Config\Installer' => [
            'singleton' => true
        ],
        '\DaveBaker\Core\Config\Layout' => [
            'singleton' => true
        ],
        '\DaveBaker\Core\Config\Object' => [
            'singleton' => true
        ],
        '\DaveBaker\Core\Config\Page' => [
            'singleton' => true
        ],

        /* Session */
        '\DaveBaker\Core\Session\General' => [
            'singleton' => true
        ]

    ];

}