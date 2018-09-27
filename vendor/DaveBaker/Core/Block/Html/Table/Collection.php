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

    protected function _preRender()
    {

        if($this->collection && $this->orderColumn){
            $this->collection->getSelect()->reset(\Zend_Db_Select::ORDER);
            $this->collection->order($this->orderColumn . " " . $this->orderDir);
        }

        $this->setRecords($this->collection);
        if($this->paginator && $this->collection){
            $this->paginator->setTotalRecords(count($this->collection->getItems()));
            $this->collection->resetItems();
            $this->collection->getSelect()->limit(
                $this->paginator->getRecordsPerPage(),
                $this->paginator->getOffset()
            );

            $paginatorClass = $this->getUtilHelper()->createUrlKeyFromText($this->getName() . "__paginator");

            $this->paginator->addClass($paginatorClass);
            $this->addJsDataItems([
                "paginatorSelector" => ".{$paginatorClass}",
                "pageNumber" => $this->paginator->getPage(),
                "order" => [
                    "column" => $this->orderColumn,
                    "dir" => $this->orderDir
                ]
            ]);
        }

        parent::_preRender();
    }

    /**
     * @param $records
     * @return $this
     * @throws Exception
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
     * @return \DaveBaker\Core\Model\Db\Collection\Base
     */
    public function getCollection()
    {
        return $this->collection;
    }

}