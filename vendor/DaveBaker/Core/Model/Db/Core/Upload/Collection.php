<?php

namespace DaveBaker\Core\Model\Db\Core\Upload;
/**
 * Class Collection
 * @package DaveBaker\Core\Model\Db
 */
class Collection
    extends \DaveBaker\Core\Model\Db\Collection\Base
{
    protected function init()
    {
        $this->dbClass = '\DaveBaker\Core\Model\Db\Core\Upload';
    }
}