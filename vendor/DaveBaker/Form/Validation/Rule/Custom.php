<?php

namespace DaveBaker\Form\Validation\Rule;
/**
 * Class Custom
 * @package DaveBaker\Form\Validation\Rule
 */
class Custom
    extends Base
    implements RuleInterface
{
    /** @var string  */
    protected $mainError = "";
    /** @var string  */
    protected $inputError = "";
    /** @var mixed */
    protected $validationMethod;

    /**
     * @return bool|Error
     */
    public function validate()
    {
        $validationMethod = $this->validationMethod;
        return $validationMethod($this->getValue(), $this);
    }

    /**
     * @param $method
     * @return $this
     */
    public function setValidationMethod($method)
    {
        $this->validationMethod = $method;
        return $this;
    }



}