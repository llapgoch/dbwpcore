<?php

namespace DaveBaker\Form\Validation\Rule\Configurator;

abstract class Base
    extends \DaveBaker\Core\Base
    implements BaseInterface
{
    const RULE_BASE = '\DaveBaker\Form\Validation\Rule\\';

    private $rules = [];
    protected $values;

    /**
     * @param $classSuffix
     * @param $name
     * @param null $niceName
     * @param null $value
     * @return object
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createRule($className, $name, $niceName = null, $value = null)
    {
        // If the class name is fully qualified, just create it, otherwise add it to the base path
        if(substr($className, 0, 1) !== '\\') {
            $className = self::RULE_BASE . $className;
        }

        $rule = $this->createAppObject($className);
        
        if(!($rule instanceof \DaveBaker\Form\Validation\Rule\RuleInterface)){
            throw new Exception("Rule {$className} is not compatible with RuleInterface");
        }

        $rule->configure(
            $name,
            $niceName ? $niceName : $name,
            $value ? $value : $this->getValue($name)
        );

        return $rule;
    }

    /**
     * @param \DaveBaker\Form\Validation\Rule\RuleInterface $rule
     * @return $this
     */
    public final function addRule(
        \DaveBaker\Form\Validation\Rule\RuleInterface $rule
    ) {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     * @throws Exception
     */
    public function setValues($values = [])
    {
        if(!is_array($values)){
            throw new Exception("setValues must be provided with an array");
        }
        $this->values = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $key string
     * @return mixed|null
     */
    public function getValue($key)
    {
        if(isset($this->values[$key])){
            return $this->values[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public final function getRules()
    {
        return $this->rules;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public final function collate(){
        if(!is_array($this->values)){
            throw new Exception('Configurator values not set, call setValues before collate');
        }

        $this->reset();
        $this->_collate();

        return $this;
    }

    protected abstract function _collate();

    /**
     * @return $this
     */
    protected function reset()
    {
        $this->rules = [];
        return $this;
    }
}