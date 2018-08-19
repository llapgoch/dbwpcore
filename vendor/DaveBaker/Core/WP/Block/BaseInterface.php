<?php

namespace DaveBaker\Core\WP\Block;

interface BaseInterface
{
    public function render();
    public function preDispatch();
    public function postDispatch();
    public function getName();
    public function getOrderType();
    public function getOrderBlock();
}