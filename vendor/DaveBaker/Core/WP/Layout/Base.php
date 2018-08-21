<?php

namespace DaveBaker\Core\WP\Layout;

abstract class Base extends \DaveBaker\Core\WP\Base
{
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
     * @param \DaveBaker\Core\WP\Block\BlockInterface $block
     * @return $this
     */
    public function addBlock(
        \DaveBaker\Core\WP\Block\BlockInterface $block
    ) {
        $this->blocks[] = $block;
        return $this;
    }

}