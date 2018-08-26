<?php

namespace DaveBaker\Core\Model\Db\Page;
/**
 * Class Registry
 * @package DaveBaker\Core\Model\Db\Page
 */
class Registry extends \DaveBaker\Core\Model\Db\Base
{
    protected function init()
    {
        $this->tableName = 'impresario_page_registry';
        $this->idColumn = 'id';
    }
}