<?php

namespace DaveBaker\Core\Block\Html;

class Tag extends Base
{
    protected function init()
    {
        $this->setTemplate('html/tag.phtml');
        $this->setTag('div');
    }
}