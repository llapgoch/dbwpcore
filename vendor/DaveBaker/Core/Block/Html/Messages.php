<?php

namespace DaveBaker\Core\Block\Html;
/**
 * Class Messages
 * @package DaveBaker\Core\Block\Html
 */
class Messages extends Base
{

    protected function _construct()
    {
        $this->setTemplate('html/messages.phtml');
        $this->addTagIdentifier('messages');
        parent::_construct();
    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getMessages()
    {
        return $this->getApp()->getGeneralSession()->getMessages();
    }
}