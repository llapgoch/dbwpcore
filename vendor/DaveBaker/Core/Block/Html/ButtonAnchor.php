<?php

namespace DaveBaker\Core\Block\Html;
/**
 * Class ButtonAnchor
 * @package DaveBaker\Core\Block\Html
 */
class ButtonAnchor extends Tag
{
    protected function _construct()
    {
        $this->setTemplate('html/tag.phtml');
        $this->setTag('a');
        $this->addTagIdentifier('button-anchor');
    }

}