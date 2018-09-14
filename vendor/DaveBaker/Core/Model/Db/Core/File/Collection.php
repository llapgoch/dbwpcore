<?php

namespace DaveBaker\Core\Model\Db\Core\File;
/**
 * Class File
 * @package DaveBaker\Core\Model\Db
 */
class Collection
    extends \DaveBaker\Core\Model\Db\Collection\Base
{
    protected function init()
    {
        $this->dbClass = '\DaveBaker\Core\Model\Db\Core\File';
    }
}