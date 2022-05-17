<?php

namespace DaveBaker\Form\Block;

/**
 * Class Row
 * @package DaveBaker\Form\Block
 */
class Row extends Base
{

    /**
     * @return \DaveBaker\Core\Block\Template
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier('form-row');
        return parent::_construct();
    }

    /**
     * @return Base
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('form/row.phtml');
    }

}