<?php

namespace DaveBaker\Form\Validation\Rule;

class Date
    extends Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} needs to be a valid date";
    protected $inputError = "This needs to be a valid date";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if(!preg_match($this->getApp()->getHelper('Date')->getDatePattern(), $this->getValue())){
            return $this->createError();
        }

        return true;
    }

}