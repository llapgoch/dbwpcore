<?php

namespace DaveBaker\Form;

use \DaveBaker\Core\Definitions\General as GeneralDefinition;
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
    /** @var \DaveBaker\Form\Validation\Validator */
    protected $validator;

    /** @var string
     * This can be overriden in config
     */
    protected $elementErrorClass = 'error';

    /**
     * @param Block\Form $form
     * @param $values
     * @param Validation\Validator|null $validator
     * @return $this
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function configure(
        Block\Form $form,
        $values,
        \DaveBaker\Form\Validation\Validator $validator = null
    ) {
        $this->form = $form;
        $this->validator = $validator;
        $this->setValues($values);

        if($class = $this->getApp()->getGeneralConfig()->getConfigValue(
            GeneralDefinition::CONFIG_ELEMENT_ERROR_CLASS_KEY)
        ){
            $this->elementErrorClass = $class;
        }

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

        return null;
    }

    /**
     * @return array
     */
    public function getChildFormElements()
    {
        return $this->getForm()->getValueFormElements();
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

        $formErrors = $this->getValidatorInputErrors();
        $formErrorKeys = array_keys($formErrors);

        /** @var Block\BaseInterface $element */
        foreach($this->getChildFormElements() as $element){
            if($this->getValue($element->getElementName()) !== null) {
                $element->setElementValue(
                    $this->getValue($element->getElementName())
                );



                if(in_array($element->getElementName(), $formErrorKeys)){
                    $element->addClass($this->elementErrorClass);
                }
            }
        }
        
        return $this;
    }

    /**
     * @return array
     */
    protected function getValidatorInputErrors()
    {
        if($this->validator){
            return $this->validator->getErrorFields();
        }

        return [];
    }
}