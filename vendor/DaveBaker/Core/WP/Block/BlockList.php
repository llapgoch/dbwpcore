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
        uasort($this->blocks, function($block1, $block2){
            $orderOutput = 0;

            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block1 */
            /** @var \DaveBaker\Core\WP\Block\BlockInterface $block2 */

            if($block1->getOrderBlock() == $block2->getName()){
                if(($block1->getOrderType() == 'before') ||
                    ($block1->getOrderBlock() === "" && $block1->getOrderType() == 'before')
                ){
                    $orderOutput--;
                }

                if(($block1->getOrderType() == 'after') ||
                    ($block1->getOrderBlock() === "" && $block1->getOrderType() == 'after')
                ){
                    $orderOutput++;
                }
            }

            if($block2->getOrderBlock() == $block1->getName()){
                if(($block2->getOrderType() == 'before') ||
                    ($block2->getOrderBlock() === "" && $block2->getOrderType() == 'before'))
                {
                    $orderOutput++;
                }

                if(($block2->getOrderType() == 'after') ||
                    ($block2->getOrderBlock() === "" && $block2->getOrderType() == 'after'))
                {
                    $orderOutput--;
                }
            }
            
            return $orderOutput;
        });

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