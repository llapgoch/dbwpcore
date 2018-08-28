<?php

namespace DaveBaker\Form\Validation\Rule\DateCompare;
/**
 * Class Future
 * @package DaveBaker\Form\Validation\Rule\DateCompare
 */
class Past
    extends \DaveBaker\Form\Validation\Rule\Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    /**
     * @return bool|\DaveBaker\Form\Validation\Rule\Error
     */
    public function validate()
    {
        $now = new \DateTime();
        $date = new \DateTime($this->getValue());

        if($date >= $now){
            $this->createError();
        }

        return true;
    }
}