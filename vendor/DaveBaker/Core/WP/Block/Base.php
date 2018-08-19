<?php

namespace DaveBaker\Core\WP\Block;

abstract class Base extends \DaveBaker\Core\Object\Base
{
    /**
     * @return string
     */
    public final function render()
    {
        return $this->toHtml();
    }

    abstract public function toHtml();

    public function preDispatch(){}
    public function postDispatch(){}
}