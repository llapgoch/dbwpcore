<?php

namespace DaveBaker\Core\WP\Config;

class Object extends Base
{
    protected $config = [
        '\DaveBaker\Core\WP\Option\Manager' => [
            'definition' => '\DaveBaker\Core\WP\Option\Manager',
            'singleton' => false
        ],
        '\DaveBaker\Core\WP\Page\Manager' => [
            'definition' => '\DaveBaker\Core\WP\Page\Manager',
            'singleton' => false
        ],
        '\DaveBaker\Core\WP\Config\Installer' => [
            'definition' => '\DaveBaker\Core\WP\Config\Installer',
            'singleton' => true
        ],
         '\DaveBaker\Core\WP\Event\Manager' => [
            'definition' => '\DaveBaker\Core\WP\Event\Manager',
            'singleton' => false
        ],
        '\DaveBaker\Core\WP\Config\Page' => [
            'definition' => '\DaveBaker\Core\WP\Config\Page',
            'singleton' => true
        ],
        '\DaveBaker\Core\Helper\Util' => [
            'definition' => '\DaveBaker\Core\Helper\Util',
            'singleton' => true
        ]
    ];

}