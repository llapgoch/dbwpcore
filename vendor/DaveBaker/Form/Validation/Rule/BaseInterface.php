<?php

namespace DaveBaker\Form\Validation\Rule;

interface BaseInterface
{
    /**
     * @return bool|Error
     */
    public function configure($name, $niceName, $value);
    public function getName();
}