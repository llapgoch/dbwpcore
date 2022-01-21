<?php

namespace DaveBaker\Form\Validation\Rule;

class Date
    extends Base
    implements RuleInterface
{
    protected $mainError = "'{{niceName}}' needs to be a valid date";
    protected $inputError = "This needs to be a valid date";

    /**
     * @return bool|\DaveBaker\Form\Validation\Error\ErrorInterface|Error
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Note: this expects the date to have already been converted to a DB Format
     */
    public function validate()
    {
        if(!$this->getValue()){
            return $this->createError();
        }

        try {
            $date = new \DateTime($this->getValue());
        } catch(\Exception $e){
            return $this->createError();
        }

        return true;
    }

}