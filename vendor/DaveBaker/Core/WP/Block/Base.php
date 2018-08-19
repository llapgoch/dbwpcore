<?php

namespace DaveBaker\Core\WP\Block;

abstract class Base extends \DaveBaker\Core\Object\Base
{
    protected $blockName;
    protected $orderType = '';
    protected $orderBlock;
    protected $as = '';

    const ORDER_TYPE_BEFORE = "before";
    const ORDER_TYPE_AFTER = "after";

    public function __construct($name = '')
    {
        if(!$name){
            throw new Exception("Block name not set");
        }

        $this->blockName = $name;
    }

    /**
     * @param $type
     * @param $blockName
     * @throws Exception
     */
    public function setOrder($type = self::ORDER_TYPES_BEFORE, $blockName)
    {
        if(!in_array($type, [self::ORDER_TYPE_AFTER, self::ORDER_TYPE_BEFORE])){
            throw new Exception("Invalid order set");
        }

        $this->orderBlock = (string) $blockName;
        $this->orderType = (string) $type;
    }

    /**
     * @return string
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * @return string
     */
    public function getOrderBlock()
    {
        return $this->orderBlock;
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