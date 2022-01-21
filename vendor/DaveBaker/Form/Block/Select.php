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
    /** @var array  */
    protected $firstOption = [
        'name' => '--- Please Select ---',
        'value' => ''
    ];

    protected $hiddenInput;

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->setElementType('select');
        parent::_construct();
    }

    /**
     * @return Base|void
     */
    protected function init()
    {
        $this->setTemplate('form/select.phtml');
        $this->setData('select_options', []);
        $this->setData('show_first_option', true);
    }

    public function setLock($lock)
    {
        parent::setLock($lock);

    }

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _preDispatch()
    {
        parent::_preDispatch();
    }

    /**
     * @return Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _preRender()
    {

        // Set the template to input and create a hidden input for the element value
        if($this->isLocked()){
            $this->setTemplate('form/input.phtml')
                ->addAttribute(['readonly' => 'readonly']);

            $this->addChildBlock(
                $this->hiddenInput = $this->createBlock('\DaveBaker\Form\Block\Input\Hidden')
                    ->setElementName($this->getElementName())
                    ->setElementValue($this->getElementValue())
            );

            $this->hiddenInput
                ->setElementValue($this->getElementValue())
                ->setElementName($this->getElementName());

            $this->setElementValue($this->getSelectedOptionLabel());
            $this->setElementName($this->getElementName() . "__locked");
        }

        parent::_preRender();
    }

    /**
     * @param $selectOptions
     * @return $this
     */
    public function setSelectOptions($selectOptions)
    {
        if(is_array($selectOptions)){
            $this->setData('select_options', $selectOptions);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedOptionLabel()
    {
        foreach($this->getSelectOptions() as $option) {
            if($this->getElementValue() == $option['value']){
                return $option['name'];
            }
        }

        return '';
    }


    /**
     * @param $showFirstOption
     * @return $this
     */
    public function setShowFirstOption($showFirstOption)
    {
        $this->setData('show_first_option', (bool) $showFirstOption);
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowFirstOption()
    {
        return $this->getData('show_first_option');
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
        $selectOptions = $this->getData('select_options');

        if($this->getShowFirstOption()){
            array_unshift($selectOptions, $this->getFirstOption());
        }

        return $selectOptions;
    }
}
