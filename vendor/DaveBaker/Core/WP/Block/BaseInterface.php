<?php

namespace DaveBaker\Core\WP\Block;

interface BaseInterface
{
    /* When the block is first created */
    public function init();

    /* When all blocks have been created */
    public function preDispatch();

    /* Generate the block's HTML */
    public function render();

    /* After all blocks have been rendered */
    public function postDispatch();
    public function getName();
    public function getOrderType();
    public function getOrderBlock();
    public function addChildBlock(\DaveBaker\Core\WP\Block\BlockInterface $block);
    public function getChildBlocks();
}