<?php

namespace DaveBaker\Form\Validation\Rule\Configurator;

class Base extends \DaveBaker\Core\Base
{
    const RULE_BASE = '\DaveBaker\Form\Validation\Rule\\';

    /**
     * @param $classSuffix
     * @return \DaveBaker\Form\Validation\Rule\RuleInterface
     * @throws Exception
     */
    public function createRule($classSuffix)
    {
        $rule = $this->createObject(self::RULE_BASE . $classSuffix, [$this->getApp()]);
        
        if(!($rule instanceof \DaveBaker\Form\Validation\Rule\RuleInterface)){
            throw new Exception("Rule {$classSuffix} is not compatible with RuleInterface");
        }

        return $rule;
    }
}