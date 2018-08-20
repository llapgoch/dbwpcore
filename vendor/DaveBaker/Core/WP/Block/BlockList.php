<?php

namespace DaveBaker\Core\WP\Block;

class BlockList implements \IteratorAggregate, \Countable
{
    protected $blocks = [];
    protected $orderedBlocks = [];
    protected $isOrdered = false;
    /**  @var \DaveBaker\Core\WP\Page\Manager */
    protected $pageManager;

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

        /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
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

        /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
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
        \DaveBaker\Core\WP\Block\BlockInterface $block
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

        /** @var \DaveBaker\Core\WP\Block\BaseInterface $block */
        foreach($this->blocks as $block){
            if(isset($this->blocks[$block->getOrderBlock()])){
                $orderBlock = $this->blocks[$block->getOrderBlock()];

                if($block->getOrderType() == 'before'){
                    $block->setIndex($orderBlock->getIndex() - 1);

                    foreach($this->blocks as $blockReorder){
                        if($blockReorder->getIndex() <  $orderBlock->getIndex()){
                            $blockReorder->setIndex($blockReorder->getIndex() - 1);
                        }
                    }
                }

                if($block->getOrderType() == 'after'){
                    $block->setIndex($orderBlock->getIndex() + 1);

                    foreach($this->blocks as $blockReorder){
                        if($blockReorder->getIndex() >  $orderBlock->getIndex()){
                            $blockReorder->setIndex($blockReorder->getIndex() + 1);
                        }
                    }
                }
            }

            if($block->getOrderBlock() === ''){
                if($block->getOrderType() == 'before'){
                    $block->setIndex($lowest - 10);
                }

                if($block->getOrderType() == 'after'){
                    $block->setIndex($highest + 10);
                }
            }

            $lowest = min($block->getIndex(), $lowest);
            $highest = max($block->getIndex(), $highest);
        }

        $ordered = [];
        foreach($this->blocks as $block){
            $ordered[$block->getIndex()] = $block;
        }

        ksort($ordered);

        $newBlocks = [];


        foreach($ordered as $block){
            $newBlocks[$block->getName()] = $block;
        }

        $this->blocks = $newBlocks;
    }


}