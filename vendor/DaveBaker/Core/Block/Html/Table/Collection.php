<?php

namespace DaveBaker\Core\Block\Html\Table;
use DaveBaker\Core\Block\Exception;

/**
 * Class Collection
 */
class Collection
    extends \DaveBaker\Core\Block\Html\Table
{
    /** @var \DaveBaker\Core\Model\Db\Collection\Base */
    protected $collection;

    /**
     * @param $records
     * @return $this|\DaveBaker\Core\Block\Html\Table
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function setRecords($records)
    {
        if(!$records instanceof \DaveBaker\Core\Model\Db\Collection\Base){
            throw new Exception('Collection Table must have records which are compatible with Collection\Base');
        }

        $this->collection = $records;

        return $this;
    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getRecords()
    {
        return $this->collection->getItems();
    }

    /**
     * @param $column
     * @param string $dir
     * @return $this|\DaveBaker\Core\Block\Html\Table
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function setColumnOrder($column, $dir = 'ASC')
    {
        parent::setColumnOrder($column, $dir);
        $this->collection->resetItems();

        if($this->collection && $this->orderColumn){
            $this->collection->getSelect()->reset(\Zend_Db_Select::ORDER);
            $this->collection->order($this->orderColumn . " " . $this->orderDir);
        }

        $this->setRecords($this->collection);

        return $this;
    }
}