<?php

namespace DaveBaker\Form\Validation\Rule;

class Numeric
    extends Base
    implements RuleInterface
{
    protected $mainError = "Please make sure '{{niceName}}' is a number";
    protected $inputError = "This needs to be a number";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if(!is_numeric($this->getValue())){
            return $this->createError();
        }

        return true;
    }

}