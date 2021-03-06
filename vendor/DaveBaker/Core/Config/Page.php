<?php

namespace DaveBaker\Core\Config;
/**
 * Class Page
 * @package DaveBaker\Core\Config
 *
 * class should be extended locally
 */
class Page extends Base
{
    const DEFAULT_VALUES_KEY = 'defaultValues';

    /* Override this locally */
    protected $config = [
        "defaultValues" => [
            "post_title" => "",
            "comment_status" => "closed",
            "post_content" => "",
            "post_status" => "publish",
            "post_type" => "page",
            "post_author" => "admin"
        ]
    ];

}