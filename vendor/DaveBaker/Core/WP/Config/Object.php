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
        ]
    ];

}