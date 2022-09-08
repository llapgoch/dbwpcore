<?php

namespace DaveBaker\Form\Validation\Rule\NumericCompare;

class LessEqual
    extends Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    protected $mainError = "{{niceName}} needs to be less than or equal to {{compareNumber}}";
    protected $inputError = "This needs to be less than or equal to {{compareNumber}}";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if(($result = parent::validate()) !== true){
            return $result;
        }

        if($this->getValue() > $this->compareNumber){
            return $this->createError();
        }

        return true;
    }

}