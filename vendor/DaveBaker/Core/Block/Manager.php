<?php

namespace DaveBaker\Core\Block;

use DaveBaker\Core\App\Exception;

class Manager
{
    protected $app;
    /** @var \DaveBaker\Core\Block\BlockList */
    protected $blockList;
    /** @var array  */
    protected $removedBlocks = [];
    /** @var int  */
    protected $anonymousCounter = 1;
    /** @var string  */
    protected $messagesBlockName = 'core.message.list';

    public function __construct(
        \DaveBaker\Core\App $app
    ){
        $this->app = $app;
        $this->blockList = $this->createBlockList()->setUseAsName(false);
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $asName
     * @return \DaveBaker\Core\Block\BlockInterface
     * @throws Exception
     */
    public function createBlock($class, $name, $asName = '')
    {
        try{
            /** @var \DaveBaker\Core\Block\BlockInterface $block */
            $block = $this->app->getObjectManager()->get($class, [$name, $asName, $this->app]);

            if(!$block instanceof \DaveBaker\Core\Block\BlockInterface){
                throw new Exception("Block {$name} does not implement BlockInterface");
            }

            $this->blockList->add($block);

            return $block;
        } catch (\Exception $e) {
            throw new Exception("Could not create block {$class} - " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $blockName string
     * @return BlockInterface|null
     */
    public function getBlock($blockName)
    {
        return $this->getBlockList()->get($blockName);
    }

    /**
     * @return BlockList
     */
    public function getBlockList()
    {
        return $this->blockList;
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
     * @return \DaveBaker\Core\Block\BlockList
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createBlockList()
    {
        return $this->app->getObjectManager()->get('\DaveBaker\Core\Block\BlockList', [$this->app]);
    }

    /**
     * @return BlockList
     */
    public function getAllBlocks()
    {
        return $this->blockList;
    }

    /**
     * @return BlockInterface|null
     * @throws Exception
     */
    public function getMessagesBlock()
    {
        if(!$messagesBlock = $this->getBlock($this->messagesBlockName)) {
            return $this->createBlock(
                '\DaveBaker\Core\Block\Html\Messages',
                $this->messagesBlockName
            );
        }

        return $messagesBlock;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getMessagesBlockHtml()
    {
        if(!$this->getMessagesBlock()){
            return '';
        }

        return $this->getMessagesBlock()->render();
    }

    /**
     * @return BlockList
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getAllRenderedBlocks()
    {
        $list = $this->createBlockList();

        /** @var \DaveBaker\Core\Block\BlockInterface $block */
        foreach($this->getAllBlocks() as $block) {
            if($block->isRendered()) {
                $list->add($block);
            }
        }

        return $list;
    }
}