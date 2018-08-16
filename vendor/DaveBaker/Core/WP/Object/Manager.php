<?php

namespace DaveBaker\Core\WP\Object;

class Manager
{
    protected $defaults = [
        '\DaveBaker\Core\WP\Option\Manager' => '\DaveBaker\Core\WP\Option\Manager',
        '\DaveBaker\Core\WP\Page\Manager' => '\DaveBaker\Core\WP\Page\Manager'
    ];

    /**
     * @param $identifier
     * @return mixed
     */
    public function getDefaultClassName($identifier)
    {
        if(isset($this->defaults[$identifier])){
            return $this->defaults[$identifier];
        }
    }
}