<?php

namespace DaveBaker\Core\Block\Html;
/**
 * Class ButtonAnchor
 * @package DaveBaker\Core\Block\Html
 */
class ButtonAnchor extends Tag
{
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->setTag('a');
        $this->addTagIdentifier('button-anchor');
        parent::_construct();
    }

    /**
     * @return Tag|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('html/tag.phtml');
    }


}