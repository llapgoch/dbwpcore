<?php
namespace DaveBaker\Form\Validation;

use DaveBaker\Form\Validation\Rule\Configurator\Exception;

class Validator extends \DaveBaker\Core\Base
{
    /** @var array  */
    protected $rules = [];
    /** @var array  */
    protected $values;
    /** @var string  */
    protected $breakAtFirst = true;

    /**
     * @param array $values
     * @return $this
     */
    public function setValues($values)
    {
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
     * @param Rule\Configurator\ConfiguratorInterface $configurator
     * @return $this
     * @throws Exception
     */
    public function configurate(
        \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator
    ) {
        $configurator
            ->setValues($this->getValues())
            ->collate();

        if(!is_array($configurator->getRules())) {
            throw new Exception('Rule Configurator getRules() must return an array of rules');
        }

        /** @var \DaveBaker\Form\Validation\Rule\RuleInterface $rule */
        foreach ($configurator->getRules() as $rule) {
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
     * @return array|bool
     * @throws Exception
     */
    public function validate()
    {
        $errors = [];

        if(!is_array($this->getValues())){
            throw new Exception('Values must be set before calling validate');
        }

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