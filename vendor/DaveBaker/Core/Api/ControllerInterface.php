<?php

namespace DaveBaker\Core\Api;
/**
 * Interface ControllerInterface
 * @package DaveBaker\Core\Api
 */
interface ControllerInterface
    extends \DaveBaker\Core\Controller\ControllerInterface
{
    const BLOCK_REPLACER_KEY = '__block__replacers__';
    const AUTH_FAILED_STRING = 'You are not authorised to perform that action';
    const AUTH_FAILED_CODE = 'authentication_fail';

    public function getBlockReplacerData();
}