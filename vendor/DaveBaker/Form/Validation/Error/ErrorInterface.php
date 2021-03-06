<?php

namespace DaveBaker\Form\Validation\Error;

interface ErrorInterface
{
    public function setErrors($mainError, $inputError);
    public function getMainError();
    public function getInputError();
}