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
        if($this->paginator && $this->collection){
            if(!$this->paginator->getTotalRecords()){
                $this->paginator->setTotalRecords(count($this->getRecords()));
            }
            
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
     * @return $this|\DaveBaker\Core\Block\Html\Table
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
        // If there's an error getting items, reset the order
        // The most likely cause is a column which no longer exists
        try{
            $this->collection->getItems();
        }catch(\Exception $e){
            $this->collection->getSelect()->reset(\Zend_Db_Select::ORDER);
            $this->collection->getSelect()->reset(\Zend_Db_Select::WHERE);
            $this->setColumnOrder('', '');
        }

        return $this->collection->getItems();
    }

    /**
     * @return \DaveBaker\Core\Model\Db\Collection\Base
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param $column
     * @param string $dir
     * @return $this|\DaveBaker\Core\Block\Html\Table
     * @throws Exception
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