<?php

namespace DaveBaker\Core\Api;
use DaveBaker\Core\Block\BlockInterface;

/**
 * Class Controller
 * @package DaveBaker\Core\Api
 *
 * Methods are defined with an Action suffix, E.g. addAction
 */
abstract class Base
    extends \DaveBaker\Core\Controller\Base
    implements ControllerInterface
{
    /** @var \DaveBaker\Core\Block\BlockList */
    protected $replacerBlockList;

    /**
     * @return bool|\WP_Error
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function isAllowed()
    {
        if(!parent::isAllowed()){
            return new \WP_Error(
                ControllerInterface::AUTH_FAILED_CODE,
                __( ControllerInterface::AUTH_FAILED_STRING),
                ['status' => 403]
            );
        }

        return true;
    }

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
     * @param $blocks
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addReplacerBlock($blocks)
    {
        if(!is_array($blocks)){
            $blocks = [$blocks];
        }

        $this->getReplacerBlockList()->add($blocks);
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