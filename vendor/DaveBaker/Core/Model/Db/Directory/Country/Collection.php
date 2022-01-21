<?php

namespace DaveBaker\Core\Model\Db\Directory\Country;
/**
 * Class Collection
 * @package DaveBaker\Core\Model\Db\Directory\Country
 */
class Collection
    extends \DaveBaker\Core\Model\Db\Collection\Base
{
    protected function init()
    {
        $this->dbClass = '\DaveBaker\Core\Model\Db\Directory\Country';
    }
}