<?php

namespace DaveBaker\Form\Validation\Rule;

class Required
    extends Base
    implements RuleInterface
{
    protected $mainError = "Please enter '{{niceName}}'";
    protected $inputError = "This is a required field";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if(empty(trim($this->getValue()))){
            return $this->createError();
        }

        return true;
    }
    
}