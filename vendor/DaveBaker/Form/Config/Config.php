<?php

namespace DaveBaker\Form\Config;
/**
 * Class Config
 * @package DaveBaker\Form\Config
 */
class Config
    extends \DaveBaker\Core\Config\Base
    implements \DaveBaker\Core\Config\ConfigInterface
{

    protected $config = [
        'classDefaults' => [
            'textarea' => 'form-element'
        ]
    ];
}