<?php

namespace DaveBaker\Form\Validation\Rule;

interface RuleInterface
    extends BaseInterface
{
    /**
     * @return bool|Error
     */
    public function validate();
}