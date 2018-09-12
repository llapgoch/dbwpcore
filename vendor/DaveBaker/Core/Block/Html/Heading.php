<?php

namespace DaveBaker\Core\Block\Html;
/**
 * Class Heading
 * @package DaveBaker\Core\Block\Html
 */
class Heading extends Tag
{
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->setTag('h2');
        $this->addTagIdentifier('heading');
        parent::_construct();
    }

    /**
     * @return Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('html/heading.phtml');
    }

}