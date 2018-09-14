<?php

namespace DaveBaker\Core\Config;
/**
 * Class General
 * @package DaveBaker\Core\Config
 *
 * class should be extended locally
 */
class General
    extends Base
    implements ConfigInterface
{
    protected $config = [
        'dateTimeDbFormat' => 'Y-m-d H:i:s',
        'dateLocalPattern' => '/^(\d{2})\/(\d{2})\/(\d{4})$/',
        'dateLocalOutputFormat' => 'l, F jS Y',
        'dateTimeLocalOutputFormat' => 'l, F jS Y, H:i A',
        'dateLocalShortOutputFormat' => 'd/m/Y',
        'dateTimeLocalShortOutputFormat' => 'd/m/Y H:i A',
        'countryCodeDefault' => 'GB',
        'formElementErrorClass' => 'form-input-error',
        'uploadAllowedMimeTypes' => 'image/jpeg, image/png, image/gif',
    ];
}