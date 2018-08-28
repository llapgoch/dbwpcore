<?php

namespace DaveBaker\Form\Validation\Rule;
/**
 * Class User
 * @package DaveBaker\Form\Validation\Rule
 */
class User extends Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} should be a valid user";
    protected $inputError = "This needs to be a valid user";

    /**
     * @return bool|\DaveBaker\Form\Validation\Error\ErrorInterface
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
        $user = $this->getApp()->getHelper('User')->getUser($this->getValue());

        if(!$user->getId()){
            return $this->createError();
        }

        return true;
    }

}