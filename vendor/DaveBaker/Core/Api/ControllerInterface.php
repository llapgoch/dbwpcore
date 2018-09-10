<?php

namespace DaveBaker\Core\Api;
/**
 * Interface ControllerInterface
 * @package DaveBaker\Core\Api
 */
interface ControllerInterface
    extends \DaveBaker\Core\Controller\ControllerInterface
{
    public function getBlockReplacerData();
}