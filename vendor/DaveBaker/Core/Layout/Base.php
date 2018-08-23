<?php

namespace DaveBaker\Core\Layout;

abstract class Base extends \DaveBaker\Core\Base
{
    /** @var string */
    protected $namespaceCode = 'layout_item';
    
    /** @var array */
    protected $blocks = [];
    
    /**
     * @return \DaveBaker\Core\Block\Manager
     */
    public function getBlockManager()
    {
        return $this->getApp()->getBlockManager();
    }

    /**
     * @return Manager
     */
    public function getLayoutManager()
    {
        return $this->getApp()->getLayoutManager();
    }

    /**
     * @return \DaveBaker\Core\App\Request
     */
    public function getRequest()
    {
        return $this->getApp()->getRequest();
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param $className
     * @param $name
     * @return \DaveBaker\Core\Block\BlockInterface
     * @throws \DaveBaker\Core\App\Exception
     */
    public function createBlock($className, $name)
    {
        return $this->getBlockManager()->createBlock($className, $name);
    }

    /**
     * @param \DaveBaker\Core\Block\BlockInterface $block
     * @return $this
     */
    public function addBlock(
        \DaveBaker\Core\Block\BlockInterface $block
    ) {
        $this->blocks[] = $block;

        $context = $this->fireEvent('add_block',
            ['block' => $block, 'blocks' => $this->blocks]
        );

        $this->blocks = $context->getBlocks();
        return $this;
    }

}