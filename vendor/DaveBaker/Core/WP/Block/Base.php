<?php

namespace DaveBaker\Core\WP\Block;

abstract class Base extends \DaveBaker\Core\Object\Base
{
    protected $blockName;

    public function __construct($name = '')
    {
        if(!$name){
            throw new Exception("Block name not set");
        }

        $this->blockName = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->blockName;
    }

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