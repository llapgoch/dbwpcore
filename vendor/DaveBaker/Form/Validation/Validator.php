<?php
namespace DaveBaker\Form\Util;

class Validator extends \DaveBaker\Core\Base
{
    /** @var array  */
    protected $rules = [];
    /** @var array  */
    protected $data = [];
    /** @var string  */
    protected $breakAtFirst = true;

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data = [])
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param \DaveBaker\Form\Validation\Rule\RuleInterface $rule
     * @return $this
     */
    public function addRule(
        \DaveBaker\Form\Validation\Rule\RuleInterface $rule
    ){
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param \DaveBaker\Form\Validation\Rule\ConfiguratorInterface $configurator
     * @return $this
     */
    public function configurate(
        \DaveBaker\Form\Validation\Rule\ConfiguratorInterface $configurator
    ) {
        /** @var \DaveBaker\Form\Validation\Rule\RuleInterface $rule */
        foreach($configurator->getRules() as $rule){
            $this->addRule($rule);
        }
        
        return $this;
    }

    /**
     * @param $breakAtFirst bool
     * @return $this
     */
    public function setBreakAtFirst($breakAtFirst)
    {
        $this->breakAtFirst = $breakAtFirst;
        return $this;
    }

    /**
     * @return bool
     */
    public function getBreakAtFirst()
    {
        return $this->breakAtFirst;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];

        /** @var \DaveBaker\Form\Validation\Rule\RuleInterface $rule */
        foreach($this->rules as $rule){
            if(($ruleResult = $rule->validate()) === true){
                continue;
            }

            if($this->breakAtFirst){
                return [$ruleResult];
            }

            $errors[] = $ruleResult;
        }

        if(count($errors)){
            return $errors;
        }

        return true;
    }
}