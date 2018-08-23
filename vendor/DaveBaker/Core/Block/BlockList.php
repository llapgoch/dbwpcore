<?php

namespace DaveBaker\Core\Block;

class BlockList implements \IteratorAggregate, \Countable
{
    protected $blocks = [];
    protected $orderedBlocks = [];
    protected $isOrdered = false;
    /**  @var \DaveBaker\Core\Page\Manager */
    protected $pageManager;
    /** @var \DaveBaker\Core\App  */
    protected $app;

    public function __construct(
        \DaveBaker\Core\App $app
    ) {
        $this->app = $app;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->update();
        return new \ArrayIterator($this->blocks);
    }

    public function update()
    {
        $filtered = [];

        /** @var \DaveBaker\Core\Block\BlockInterface $block */
        foreach($this->blocks as $block){
            if(!$this->getBlockManager()->isRemoved($block->getName())){
                $filtered[$block->getName()] = $block;
            }
        }

        $this->blocks = $filtered;
    }

    /**
     * @param array $blocks
     * @return $this
     */
    public function add($blocks = [])
    {
        if(!is_array($blocks)){
            $blocks = [$blocks];
        }

        /** @var \DaveBaker\Core\Block\BlockInterface $block */
        foreach($blocks as $block){
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function order()
    {
        if(!$this->isOrdered) {
            for ($i = 0; $i < round(count($this->blocks)); $i++) {
                $this->orderAll();
            }
        }

        $this->isOrdered = true;

        return $this;
    }

    /**
     * @param $blockName
     * @return \DaveBaker\Core\Block\BlockInterface|null
     */
    public function get($blockName)
    {
        return $this->blocks[$blockName];
    }
    
    /**
     * @return int
     */
    public function count()
    {
        return count($this->blocks);
    }

    /**
     * @return Manager
     */
    public function getBlockManager()
    {
        return $this->app->getBlockManager();
    }

    /**
     * @param BlockInterface $block
     * @return $this
     */
    protected function addBlock(
        \DaveBaker\Core\Block\BlockInterface $block
    ) {
        if(!$this->getBlockManager()->isRemoved($block->getName())) {
            $this->blocks[$block->getName()] = $block;
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function orderAll()
    {

        $a = 10;
        $lowest = $a;

        foreach($this->blocks as $block){
            $block->setIndex($a);
            $a += 10;
        }

        $highest = $a;

        /** @var \DaveBaker\Core\Block\BaseInterface $block */
        foreach($this->blocks as $block){
            if(isset($this->blocks[$block->getOrderBlock()])){
                $orderBlock = $this->blocks[$block->getOrderBlock()];

                if($block->getOrderType() == 'before'){
                    $block->setData('index', $orderBlock->getIndex() - 1);

                    foreach($this->blocks as $blockReorder){
                        if($blockReorder->getIndex() <  $orderBlock->getIndex()){
                            $blockReorder->setData('index', $blockReorder->getIndex() - 1);
                        }
                    }
                }

                if($block->getOrderType() == 'after'){
                    $block->setData('index', $orderBlock->getIndex() + 1);

                    foreach($this->blocks as $blockReorder){
                        if($blockReorder->getData('index') >  $orderBlock->getIndex()){
                            $blockReorder->setData('index', $blockReorder->getIndex() + 1);
                        }
                    }
                }
            }

            if($block->getOrderBlock() === ''){
                if($block->getOrderType() == 'before'){
                    $block->setData('index', $lowest - 10);
                }

                if($block->getOrderType() == 'after'){
                    $block->setData('index', $highest + 10);
                }
            }

            $lowest = min($block->getData('index'), $lowest);
            $highest = max($block->getData('index'), $highest);
        }

        $ordered = [];
        foreach($this->blocks as $block){
            $ordered[$block->getData('index')] = $block;
        }

        ksort($ordered);

        $newBlocks = [];


        foreach($ordered as $block){
            $newBlocks[$block->getName()] = $block;
        }

        $this->blocks = $newBlocks;
    }


}