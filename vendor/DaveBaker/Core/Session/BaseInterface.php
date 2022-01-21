<?php
namespace DaveBaker\Core\Session;
/**
 * Interface BaseInterface
 * @package DaveBaker\Core\Session
 */
interface BaseInterface
{
    public function addMessage($message, $type = \DaveBaker\Core\Definitions\Messages::ERROR);
    public function getMessages($type = null, $clear = true);
    public function clearMessages($type = null);
    public function get($key);
    public function set($key, $data);
    public function clear($key = null);
}