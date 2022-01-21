<?php

namespace DaveBaker\Form\SelectConnector;
/**
 * Class Array
 * @package DaveBaker\Form\SelectConnector
 *
 * Extend to get the values out of a collection for a select block
 */

class AssociativeArray extends \DaveBaker\Core\Base
{
    protected $values;

    /**
     * @param array $values
     * @param \DaveBaker\Form\Block\Select|null $selectElement
     * @return $this
     * @throws Exception
     */
    public function configure(
        $values,
        \DaveBaker\Form\Block\Select $selectElement = null
    ) {
        $this->values = $values;

        if($selectElement){
            $selectElement->setSelectOptions($this->getElementData());
        }

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getElementData()
    {
        $data = [];
        if(!is_array($this->values)){
            throw new Exception('values must be set via configure before getting data');
        }

        foreach($this->values as $value => $name){
            $data[] = [
                'name' => $name,
                'value' => $value
            ];
        }

        return $data;
    }
}