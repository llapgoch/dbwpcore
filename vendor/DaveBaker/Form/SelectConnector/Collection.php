<?php

namespace DaveBaker\Form\SelectConnector;
/**
 * Class Collection
 * @package DaveBaker\Form\SelectConnector
 *
 * Extend to get the values out of a collection for a select block
 */
class Collection extends \DaveBaker\Core\Base
{
    /** @var \DaveBaker\Core\Model\Db\Collection\Base */
    protected $collection;
    /** @var string  */
    protected $valueField = '';
    /** @var string  */
    protected $nameField = '';

    /**
     * @return array
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getData()
    {
        if(!$this->collection || !$this->valueField || !$this->nameField){
            throw new Exception('SelecConnector requires collectionClass, valueField, and nameField to be set via configure');
        }

        $data = [];

        $this->collection->load();

        if(!($items = $this->collection->getItems())){
            return [];
        }

        foreach($items as $item){
            $data[] = [
                'name' => $item->getData($this->nameField),
                'value' => $item->getData($this->valueField)
            ];
        }

        return $data;
    }

    /**
     * @param \DaveBaker\Core\Model\Db\Collection\Base $collection
     * @param $valueField
     * @param $nameField
     * @param \DaveBaker\Form\Block\Select|null $selectElement
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function configure(
        \DaveBaker\Core\Model\Db\Collection\Base $collection,
        $valueField,
        $nameField,
        \DaveBaker\Form\Block\Select $selectElement = null
    ) {
        $this->collection = $collection;
        $this->nameField = $nameField;
        $this->valueField = $valueField;

        if($selectElement){
            $selectElement->setSelectOptions($this->getData());
        }

        return $this;
    }

    /**
     * @return \DaveBaker\Core\Model\Db\Collection\Base
     */
    public function getCollection()
    {
        return $this->collection;
    }

}