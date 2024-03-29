<?php

namespace DaveBaker\Core\Block;

interface BaseInterface
    extends \DaveBaker\Core\BaseInterface
{
    /* When all blocks have been created */
    public function preDispatch();

    /* Generate the block's HTML */
    public function render();

    /* After all blocks have been rendered */
    public function postDispatch();
    public function getName();
    public function getOrderType();
    public function getOrderBlock();
    public function addChildBlock($blocks);
    public function getChildBlocks();

    /**
     * @param string $shortcode
     * @return \DaveBaker\Core\Block\BlockInterface
     * 
     * Actions and shortcodes are only used by blocks registered with the layout manager
     */
    public function setShortcode($shortcode);
    public function getShortcode();
    public function getAction();
    public function setAction($action);
    // Any arguments which are passed from actions are set in actionArguments
    public function setActionArguments($args);
    public function isRendered();
}