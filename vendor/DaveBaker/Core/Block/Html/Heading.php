<?php

namespace DaveBaker\Core\Block\Html;

class Heading extends \DaveBaker\Core\Block\Template
{
    public function init()
    {
        $this->setTemplate('html/heading.phtml');
        $this->setTag('h2');
    }
}