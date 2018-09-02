<?php

namespace DaveBaker\Form\Block;

/**
 * Class Row
 * @package DaveBaker\Form\Block
 */
class Row extends Base
{

    public function init()
    {
        $this->setTemplate('form/row.phtml');
        $this->addTagIdentifier('form-row');
        return parent::init();
    }

}