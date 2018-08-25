<?php

namespace DaveBaker\Core\Controller;
/**
 * Interface BaseInterface
 * @package DaveBaker\Core\Controller
 */
interface BaseInterface
{
    public function preDispatch();
    public function postDispatch();
}