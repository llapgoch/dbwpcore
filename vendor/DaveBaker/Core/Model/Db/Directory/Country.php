<?php

namespace DaveBaker\Core\Model\Db\Directory;
/**
 * Class Country
 * @package DaveBaker\Core\Model\Db\Directory
 */
class Country
    extends \DaveBaker\Core\Model\Db\Base
{
    protected function init()
    {
        $this->idColumn = 'id';
        $this->tableName = 'directory_country';
    }
}