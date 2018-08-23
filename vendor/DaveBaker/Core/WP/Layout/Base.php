<?php

namespace DaveBaker\Core\WP\Layout;

abstract class Base extends \DaveBaker\Core\WP\Base
{
    /** @var string */
    protected $namespaceCode = 'layout_item';
    
    /** @var array */
    protected $blocks = [];
    
    /**
     * @return \DaveBaker\Core\WP\Block\Manager
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
     * @return \DaveBaker\Core\WP\App\Request
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
     * @return \DaveBaker\Core\WP\Block\BlockInterface
     * @throws \DaveBaker\Core\WP\App\Exception
     */
    public function createBlock($className, $name)
    {
        return $this->getBlockManager()->createBlock($className, $name);
    }

    /**
     * @param \DaveBaker\Core\WP\Block\BlockInterface $block
     * @return $this
     */
    public function addBlock(
        \DaveBaker\Core\WP\Block\BlockInterface $block
    ) {
        $this->blocks[] = $block;

        $context = $this->fireEvent('add_block',
            ['block' => $block, 'blocks' => $this->blocks]
        );

        $this->blocks = $context->getBlocks();
        return $this;
    }

}