<?php

namespace DaveBaker\Core\Config;

class Object extends Base
{
    protected $config = [
        '\DaveBaker\Core\Option\Manager' => [
            'definition' => '\DaveBaker\Core\Option\Manager',
            'singleton' => false
        ],
        '\DaveBaker\Core\Page\Manager' => [
            'definition' => '\DaveBaker\Core\Page\Manager',
            'singleton' => true
        ],
        '\DaveBaker\Core\Config\Installer' => [
            'definition' => '\DaveBaker\Core\Config\Installer',
            'singleton' => true
        ],
         '\DaveBaker\Core\Event\Manager' => [
            'definition' => '\DaveBaker\Core\Event\Manager',
            'singleton' => false
        ],
        '\DaveBaker\Core\Config\Page' => [
            'definition' => '\DaveBaker\Core\Config\Page',
            'singleton' => true
        ],
        '\DaveBaker\Core\Helper\Util' => [
            'definition' => '\DaveBaker\Core\Helper\Util',
            'singleton' => true
        ],
        '\DaveBaker\Core\Helper\Date' => [
            'definition' => '\DaveBaker\Core\Helper\Date',
            'singleton' => true
        ]
    ];

}