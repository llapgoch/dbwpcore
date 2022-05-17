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
    /** @var bool  */
    protected $requiresLogin = false;
    /** @var array  */
    protected $capabilities = [];
    /** @var string  */
    protected $capabilityFailUrl = '';

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function preDispatch()
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
    public function postDispatch()
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
        $type = \DaveBaker\Core\Definitions\Messages::ERROR
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
     * @param array $params
     * @param null $returnUrl
     * @return \DaveBaker\Core\App\Response
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToPage($pageIdentifier, $params = [], $returnUrl = null)
    {
        return $this->getResponse()->redirectToPage($pageIdentifier, $params, $returnUrl);
    }

    /**
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function isAuthAllowed()
    {
        $pageManager = $this->getApp()->getPageManager();

        if (!($pageManager->isOnRegisterPage() || $pageManager->isOnLoginPage())) {
            if ($this->requiresLogin && !($this->getUserHelper()->isLoggedIn())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function isAllowed()
    {
        return $this->isAuthAllowed() && $this->isCapabilityAllowed();
    }

    /**
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function isCapabilityAllowed()
    {
        if ($this->capabilities && !$this->getUserHelper()->hasCapability($this->capabilities)) {
            return false;
        }

        return true;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function checkAllowed()
    {
        if (!$this->isAuthAllowed()) {
            return auth_redirect();
        }

        if (!$this->isCapabilityAllowed()) {
            return $this->redirect($this->getUrlHelper()->getUrl($this->capabilityFailUrl));
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
