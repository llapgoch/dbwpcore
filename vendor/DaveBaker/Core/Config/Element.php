<?php

namespace DaveBaker\Core\Config;

class Element
    extends Base
    implements ConfigInterface
{
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