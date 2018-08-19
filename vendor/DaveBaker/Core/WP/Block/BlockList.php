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

    public function order()
    {
        for($i = 0; $i < round(count($this->blocks) * 10); $i++){
            $this->orderAll();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function orderAll()
    {
        //TODO: Caching!


        $ordered = array_values($this->blocks);
        $orderedOrig = $ordered;

        var_dump(count($this->blocks));

        /** @var \DaveBaker\Core\WP\Block\BlockInterface $block */
        foreach ($orderedOrig as $blockKey => $block) {
            $found = false;

            if (!$block->getOrderBlock()) {
                if ($block->getOrderType() == 'before') {
                    array_unshift($ordered, [$block]);
//                    $found = true;
                }

                if ($block->getOrderType() == 'after') {
                    $ordered[] = $block;
//                    $found = true;
                }

            }

            /** @var \DaveBaker\Core\WP\Block\BlockInterface $orderBlock */
            if(!$found) {
                foreach ($ordered as $k => $orderBlock) {

                    if ($block->getOrderBlock() == $orderBlock->getName()) {
                        if ($block->getOrderType() == 'before') {
                            array_splice($ordered, $k, 0, [$block]);
                            $found = true;
                            break;
                        }


                        if ($block->getOrderType() == 'after') {
                            array_splice($ordered, $k + 1, 0, [$block]);
                            $found = true;
                            break;
                        }
                    }
                }
            }

            if(!$found){
//                $ordered[] = $block;
            }

            if ($found) {
                array_splice($ordered, $blockKey, 1);
            }

        }

        $assoc = [];
        foreach ($ordered as $item) {
            $assoc[$item->getName()] = $item;
        }

        $this->blocks = $assoc;

        var_dump(count($assoc));
exit;
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