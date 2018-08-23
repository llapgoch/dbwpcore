<?php

namespace DaveBaker\Form\Validation\Rule;

class Email
    extends Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} needs to be a valid email address";
    protected $inputError = "This needs to be a valid email address";

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if (!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)){
            return $this->createError();
        }

        return true;
    }

}