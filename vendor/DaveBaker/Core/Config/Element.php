<?php

namespace DaveBaker\Core\Config;
/**
 * Class Element
 * @package DaveBaker\Core\Config
 */
class Element
    extends Base
    implements ConfigInterface
{
    /**
     * @var array
     *
     * Override and provide theme defaults. Add tag identifiers as necessary
     * These do not have to relate to tag names, identifiers are arbitrary
     */
    protected $config = [
        'elementClasses' => [
            'heading' => '',
            'messages' => '',
            'table' => '',
            'th' => '',
            'tr' => '',
            'td' => '',
            'a' => '',
            'p' => ''
        ]
    ];
}