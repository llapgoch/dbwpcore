<?php

namespace DaveBaker\Core\Block\Html;

class Tag extends \DaveBaker\Core\Block\Template
{
    protected function init()
    {
        $this->setTemplate('html/tag.phtml');
        $this->setTag('div');
    }
}