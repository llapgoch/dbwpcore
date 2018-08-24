<?php

namespace DaveBaker\Form\Validation\Rule;

class Date
    extends Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} needs to be a valid date";
    protected $inputError = "This needs to be a valid date";
    protected $datePattern = '/^(\d{2})\/(\d{2})\/(\d{4})$/';

    /**
     * @return bool|Error
     */
    public function validate()
    {
        if(!preg_match($this->datePattern, $this->getValue())){
            return $this->createError();
        }

        return true;
    }

}