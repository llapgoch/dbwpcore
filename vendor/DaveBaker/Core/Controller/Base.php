<?php

namespace DaveBaker\Core\Controller;
/**
 * Class Base
 * @package DaveBaker\Core\Controller
 */
class Base extends \DaveBaker\Core\Base
{
    /** @var string */
    protected $namespaceCode = 'controller';
    protected $requiresLogin = false;

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function preDispatch()
    {
        $this->checkAllowed();
        $this->fireEvent('predispatch_before');
        $this->_preDispatch();
        $this->fireEvent('predispatch_after');
        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public final function postDispatch()
    {
        $this->fireEvent('postdispatch_before');
        $this->_postDispatch();
        $this->fireEvent('postdispatch_after');
        return $this;
    }

    /**
     * @param string $message
     * @param string|null $type
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addMessage(
        $message,
        $type = \DaveBaker\Core\Definitions\Messages::SUCCESS
    ) {
        $this->getApp()->getGeneralSession()->addMessage($message, $type);
        return $this;
    }

    /**
     * @param string|null $type
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function clearMessages($type = null)
    {
        $this->getApp()->getGeneralSession()->clearMessages($type);
        return $this;
    }

    /**
     * @return \DaveBaker\Core\App\Request|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getRequest()
    {
        return $this->getApp()->getRequest();
    }

    /**
     * @return \DaveBaker\Core\App\Response|object
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getResponse()
    {
        return $this->getApp()->getResponse();
    }

    /**
     * @param $url
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirect($url)
    {
        return $this->getResponse()->redirect($url);
    }

    /**
     * @param $pageIdentifier
     * @return \DaveBaker\Core\App\Response
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToPage($pageIdentifier)
    {
        return $this->getResponse()->redirectToPage($pageIdentifier);
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function checkAllowed()
    {
        $pageManager = $this->getApp()->getPageManager();

        if(!($pageManager->isOnRegisterPage() || $pageManager->isOnLoginPage())){
            if($this->requiresLogin && !($this->getApp()->getHelper('User')->isLoggedIn())){
                $this->getResponse()->authRedirect();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _preDispatch()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _postDispatch()
    {
        return $this;
    }
}