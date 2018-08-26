<?php

namespace DaveBaker\Core\Block\Html;

class Heading extends Base
{
    protected function init()
    {
        $this->setTemplate('html/heading.phtml');
        $this->setTag('h2');
    }
}