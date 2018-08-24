<?php

namespace DaveBaker\Form\Validation\Error;

class Error implements ErrorInterface
{
    protected $mainError = '';
    protected $inputError = '';

    /**
     * @param string $mainError
     * @param string $inputError
     * @return $this
     */
    public function setErrors($mainError, $inputError)
    {
        $this->mainError = (string) $mainError;
        $this->inputError = (string) $inputError;
        return $this;
    }

}