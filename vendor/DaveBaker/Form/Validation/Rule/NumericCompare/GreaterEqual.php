<?php

namespace DaveBaker\Form\Validation\Rule\NumericCompare;

class GreaterEqual
    extends Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    protected $mainError = "{{niceName}} needs to be greater or equal to {{compareNumber}}";
    protected $inputError = "This needs to be greater or equal to {{compareNumber}}";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if(($result = parent::validate()) !== true){
            return $result;
        }
        
        if((float) $this->getValue() < (float) $this->compareNumber){
            return $this->createError();
        }

        return true;
    }

}