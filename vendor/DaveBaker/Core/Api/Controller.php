<?php

namespace DaveBaker\Core\Api;
use DaveBaker\Core\Block\BlockInterface;

/**
 * Class Controller
 * @package DaveBaker\Core\Api
 *
 * Methods are defined with an Action suffix, E.g. addAction
 */
class Controller
    extends \DaveBaker\Core\Controller\Base
    implements ControllerInterface
{
    const BLOCK_REPLACER_KEY = '__block__replacers__';

    /** @var \DaveBaker\Core\Block\BlockList */
    protected $replacerBlockList;

    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getBlockReplacerData()
    {
        $list = $this->getReplacerBlockList();

        if(!count($list)){
            return [];
        }

        $replacers = [];

        /** @var BlockInterface $block */
        foreach($list as $block){
            $replacers[$block->getName()] = $block->render();
        }

        return [self::BLOCK_REPLACER_KEY => $replacers];
    }

    /**
     * @param BlockInterface $block
     * @throws \DaveBaker\Core\Object\Exception
     * @return $this
     */
    protected function registerBlockReplacer(
        BlockInterface $block
    ){
        $this->getReplacerBlockList()->add($block);
        return $this;
    }


    /**
     * @return \DaveBaker\Core\Block\BlockList
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getReplacerBlockList()
    {
        if(!$this->replacerBlockList) {
            $this->replacerBlockList = $this->getApp()->getBlockManager()->createBlockList();
        }

        return $this->replacerBlockList;
    }


    // Methods in API controllers follow the form <actionName>Action, execute is not required

    /**
     *
     */
    public function execute(){}
}