<?php

namespace DaveBaker\Core\WP\Block;

abstract class Base
{
    public function preDispatch()
    {

    }

    public function postDispatch()
    {

    }

    public final function render()
    {
        return $this->toHtml();
    }

    public function toHtml(){
        return "";
    }
}