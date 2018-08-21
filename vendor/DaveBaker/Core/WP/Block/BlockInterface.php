<?php

namespace DaveBaker\Core\WP\Block;

interface BlockInterface extends BaseInterface
{
    public function render();
    public function getChildHtml($blockName);
}