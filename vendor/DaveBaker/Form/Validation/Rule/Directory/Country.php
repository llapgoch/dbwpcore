<?php

namespace DaveBaker\Form\Validation\Rule\Directory;

use DaveBaker\Form\Validation\Rule\Error;

/**
 * Class Country
 * @package DaveBaker\Form\Validation\Directory
 */
class Country
    extends \DaveBaker\Form\Validation\Rule\Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    protected $mainError = "Please select a valid country for '{{niceName}}'";
    protected $inputError = "This needs to be a valid country";

    /**
     * @return bool|Error
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
        if(!$this->getDirectoryHelper()->isValidCountryCode($this->getValue())){
            return $this->createError();
        }

        return true;
    }

    /**
     * @return \DaveBaker\Core\Helper\Directory
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getDirectoryHelper()
    {
        return $this->getApp()->getHelper('Directory');
    }
}