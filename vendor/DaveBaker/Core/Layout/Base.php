<?php

namespace DaveBaker\Core\Layout;

/**
 * Class Base
 * @package DaveBaker\Core\Layout
 */
abstract class Base extends \DaveBaker\Core\Base
{
    /** @var string */
    protected $namespaceCode = 'layout_item';
    /** @var array */
    protected $blocks = [];

    /**
     * @return \DaveBaker\Core\Block\Manager|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getBlockManager()
    {
        return $this->getApp()->getBlockManager();
    }

    /**
     * @return Manager|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getLayoutManager()
    {
        return $this->getApp()->getLayoutManager();
    }

    /**
     * @return \DaveBaker\Core\App\Request|object
     * @throws \DaveBaker\Core\Object\Exception
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
     * @param string $asName
     * @return \DaveBaker\Core\Block\BlockInterface
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createBlock($className, $name, $asName = '')
    {
        return $this->getBlockManager()->createBlock($className, $name, $asName);
    }

    /**
     * @param \DaveBaker\Core\Block\BlockInterface $block
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
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