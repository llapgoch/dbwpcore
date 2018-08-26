<?php

namespace DaveBaker\Form;
/**
 * Class BlockApplicator
 * @package DaveBaker\Form
 *
 * Applies values (most likely from a post) to a form's child ValueSetterInterface elements
 */

class BlockApplicator extends \DaveBaker\Core\Base
{
    /** @var  Block\Form */
    protected $form;
    /** @var array */
    protected $values = [];

    /**
     * @param Block\Form $form
     * @param $values
     * @return $this
     * @throws Exception
     */
    public function configure(
        Block\Form $form,
        $values
    ) {
        $this->form = $form;
        $this->setValues($values);
        $this->apply();

        return $this;
    }

    /**
     * @return Block\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $values
     * @return $this
     * @throws Exception
     */
    public function setValues($values)
    {
        if(!is_array($values)){
            throw new Exception("Form values must be an array");
        }

        $this->values = $values;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getValue($key)
    {
        if(isset($this->values[$key])){
            return $this->values[$key];
        }

        return $key;
    }

    /**
     * @return array
     */
    public function getChildFormElements()
    {
        $elements = [];

        foreach($this->getForm()->getChildBlocks() as $childBlock){
            if($childBlock instanceof Block\ValueSetterInterface){
                $elements[] = $childBlock;
            }
        }

        return $elements;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function apply()
    {
        if(!$this->getForm()){
            throw new Exception('Form and values must be set through configure before calling apply');
        }

        /** @var Block\BaseInterface $element */
        foreach($this->getChildFormElements() as $element){
            $element->setElementValue(
                $this->getValue($element->getElementName())
            );
        }
        
        return $this;
    }
}