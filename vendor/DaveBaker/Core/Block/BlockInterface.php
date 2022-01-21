<?php

namespace DaveBaker\Core\Block;

interface BlockInterface
    extends BaseInterface
{
    public function render();
    public function getChildHtml($blockName);
}