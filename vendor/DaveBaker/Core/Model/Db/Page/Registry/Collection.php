<?php

namespace DaveBaker\Core\Model\Db\Page\Registry;

class Collection
    extends \DaveBaker\Core\Model\Db\Collection\Base
{
    protected function init()
    {
        $this->dbClass = '\DaveBaker\Core\Model\Db\Page\Registry';
    }
}