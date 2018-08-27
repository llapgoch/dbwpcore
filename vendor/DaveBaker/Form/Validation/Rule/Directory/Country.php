<?php

namespace DaveBaker\Form\Validation\Dirctory;

use DaveBaker\Form\Validation\Rule\Error;

/**
 * Class Country
 * @package DaveBaker\Form\Validation\Dirctory
 */
class Country
    extends \DaveBaker\Form\Validation\Rule\Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{

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