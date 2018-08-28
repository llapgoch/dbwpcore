<?php

namespace DaveBaker\Form\Validation\Rule\DateCompare;
/**
 * Class Future
 * @package DaveBaker\Form\Validation\Rule\DateCompare
 */
class Future
    extends \DaveBaker\Form\Validation\Rule\Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    protected $mainError = "'{{niceName}}' needs to be a date in the future";
    protected $inputError = "Date needs to be in the future";

    /**
     * @return bool|\DaveBaker\Form\Validation\Rule\Error
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
        $now = new \DateTime();
        $date = new \DateTime($this->getValue());

        if($date < $now){
            $this->createError();
        }

        return true;
    }
}