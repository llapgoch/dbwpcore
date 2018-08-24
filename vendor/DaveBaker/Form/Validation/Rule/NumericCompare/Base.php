<?php

namespace DaveBaker\Form\Validation\Rule\NumericCompare;

abstract class Base
    extends \DaveBaker\Form\Validation\Rule\Numeric
{
    protected $compareNumber = 0;

    /**
     * @param $number float
     * @return $this
     */
    public function setCompareNumber($number)
    {
        $this->compareNumber = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getCompareNumber()
    {
        return $this->compareNumber;
    }

    /**
     * @return array
     */
    protected function getErrorReplacers()
    {
        return array_merge(
            parent::getErrorReplacers(),
            [
                'compareNumber' => $this->compareNumber
            ]
        );
    }

}