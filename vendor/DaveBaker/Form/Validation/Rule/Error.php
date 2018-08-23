<?php

namespace DaveBaker\Form\Validation\Rule;

class Error
{
    protected $mainError = '';
    protected $inputError = '';

    /**
     * @param string $mainError
     * @param string $inputError
     * @return $this
     */
    public function setErrors($mainError = '', $inputError = '')
    {
        $this->mainError = $mainError;
        $this->inputError = $inputError;
        return $this;
    }

}