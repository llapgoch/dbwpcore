<?php

namespace DaveBaker\Core\WP\Controller;

interface BaseInterface
{
    public function preDispatch();
    public function postDispatch();
}