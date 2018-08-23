<?php

namespace DaveBaker\Form\Validation\Rule;

class GreaterEqual
    extends Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} needs to be greater or equal to {{compareNumber}}";
    protected $inputError = "This needs to be greater or equal to {{compareNumber}}";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if($this->getValue() < $this->compareNumber){
            return $this->createError();
        }

        return true;
    }

}