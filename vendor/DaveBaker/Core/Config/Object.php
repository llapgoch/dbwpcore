<?php

namespace DaveBaker\Core\Config;

class Object extends Base
{
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
        '\DaveBaker\Core\Config\General' => [
            'singleton' => true
        ],
        
        /* Helpers */
        '\DaveBaker\Core\Helper\Util' => [
            'singleton' => true
        ],
        '\DaveBaker\Core\Helper\Date' => [
            'singleton' => true
        ]
    ];

}