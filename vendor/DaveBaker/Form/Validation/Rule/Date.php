<?php

namespace DaveBaker\Form\Validation\Rule;

class Date
    extends Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} needs to be a valid date";
    protected $inputError = "This needs to be a valid date";

    /**
     * @return bool|\DaveBaker\Form\Validation\Error\ErrorInterface|Error
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
        if(!preg_match($this->getApp()->getHelper('Date')->getLocalDatePattern(), $this->getValue())){
            return $this->createError();
        }

        return true;
    }

}