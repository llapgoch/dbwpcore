<?php

namespace DaveBaker\Form\Block;
/**
 * Class Select
 * @package DaveBaker\Form\Block
 */
class Select
    extends Base
    implements \DaveBaker\Form\Block\ValueSetterInterface
{
    /** @var string  */
    protected $mainTagName = 'select';
    /** @var array  */
    protected $selectOptions = [];
    /** @var array  */
    protected $firstOption = [
        'name' => '--- Please Select ---',
        'value' => ''
    ];

    protected $showFirstOption = true;

    /**
     * @return Base|void
     */
    protected function init()
    {
        $this->setTemplate('form/select.phtml');
        $this->setElementType('select');
    }

    /**
     * @param $selectOptions
     * @return $this
     */
    public function setSelectOptions($selectOptions)
    {
        if(is_array($selectOptions)){
            $this->selectOptions = $selectOptions;
        }

        return $this;
    }

    /**
     * @param $showFirstOption
     * @return $this
     */
    public function setShowFirstOption($showFirstOption)
    {
        $this->showFirstOption = (bool) $showFirstOption;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowFirstOption()
    {
        return $this->showFirstOption;
    }

    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function setFirstOption($name, $value = '')
    {
        $this->firstOption = [
            'name' => $name,
            'value' => $value
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getFirstOption()
    {
        return $this->firstOption;
    }

    /**
     * @param $optionValue
     * @return string
     */
    public function getSelectedAttribute($optionValue)
    {
        if($this->getElementValue() == $optionValue){
            return "selected='selected'";
        }

        return '';
    }

    /**
     * @return array
     */
    public function getSelectOptions()
    {
        $selectOptions = $this->selectOptions;

        if($this->getShowFirstOption()){
            array_unshift($selectOptions, $this->getFirstOption());
        }

        return $selectOptions;
    }
}
