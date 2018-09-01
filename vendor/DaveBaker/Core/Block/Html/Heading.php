<?php

namespace DaveBaker\Core\Block\Html;
/**
 * Class Heading
 * @package DaveBaker\Core\Block\Html
 */
class Heading extends Tag
{
    protected function init()
    {
        $this->setTemplate('html/heading.phtml');
        $this->setTag('h2');
        $this->addTagIdentifier('heading');
    }

}