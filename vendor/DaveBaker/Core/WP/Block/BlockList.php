<?php

namespace DaveBaker\Core\WP\Block;

class BlockList implements \IteratorAggregate, \Countable
{
    protected $blocks = [];

    /**
     * @return \ArrayIterator
     */
    public function getIterator(){
        return new \ArrayIterator($this->blocks);
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
        $ordered = [];
        /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
        foreach($this->blocks as $block){
            if($block->getOrderType() == 'before'){
                if(!$block->getOrderBlock()){

                }
            }
        }


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
     * @param BlockInterface $block
     * @return $this
     */
    protected function addBlock(
        \DaveBaker\Core\WP\Block\BlockInterface $block
    ) {
        $this->blocks[$block->getName()] = $block;

        return $this;
    }
}