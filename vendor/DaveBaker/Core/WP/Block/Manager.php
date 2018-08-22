<?php

namespace DaveBaker\Core\WP\Block;

use DaveBaker\Core\WP\App\Exception;

class Manager
{
    protected $app;
    /** @var \DaveBaker\Core\WP\Block\BlockList */
    protected $blockList;
    protected $removedBlocks = [];

    public function __construct(
        \DaveBaker\Core\App $app
    ){
        $this->app = $app;
        $this->blockList = $this->createBlockList();
    }

    /**
     * @param $class
     * @param $name
     * @return \DaveBaker\Core\WP\Block\BlockInterface
     * @throws Exception
     */
    public function createBlock($class, $name)
    {
        try{
            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
            $block = $this->app->getObjectManager()->get($class, [$name, $this->app]);
            $block->init();

            if(!$block instanceof \DaveBaker\Core\WP\Block\BlockInterface){
                throw new Exception("Block {$name} does not implement BlockInterface");
            }

            $this->blockList->add($block);

            return $block;
        } catch (\Exception $e) {
            throw new Exception("Could not create block {$class}", $e->getCode());
        }
    }

    /**
     * @param $blockName
     * @return $this|void
     */
    public function removeBlock($blockName)
    {
        $this->removedBlocks[] = $blockName;
        $this->blockList->update();

        return $this;
    }

    /**
     * @param $blockName
     * @return bool
     */
    public function isRemoved($blockName)
    {
        return in_array($blockName, $this->removedBlocks);
    }

    /**
     * @return \DaveBaker\Core\WP\Block\BlockList
     * @throws \DaveBaker\Core\WP\Object\Exception
     */
    public function createBlockList()
    {
        return $this->app->getObjectManager()->get('\DaveBaker\Core\WP\Block\BlockList', [$this->app]);
    }

    /**
     * @return BlockList
     */
    public function getAllBlocks()
    {
        return $this->blockList;
    }

    /**
     * @return BlockList
     */
    public function getAllRenderedBlocks()
    {
        $list = $this->createBlockList();

        /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
        foreach($this->getAllBlocks() as $block) {
            if($block->isRendered()) {
                $list->add($block);
            }
        }

        return $list;
    }
}