<?php

namespace DaveBaker\Core\App;

/**
 * Class Response
 */
class Response extends \DaveBaker\Core\Base
{
    /**
     * @param string $url
     */
    public function redirect($url)
    {
        wp_redirect($url);
        exit;
    }

    /**
     * @param string $pageIdentifier
     * @param null|string $returnUrl
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToPage($pageIdentifier, $params = [], $returnUrl = null)
    {
        $this->redirect(
            $this->getApp()->getPageManager()->getUrl($pageIdentifier, $params, $returnUrl)
        );
        return $this;
    }

    /**
     * @param string $alternateUrl
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectReferer($alternateUrl = '')
    {
        if($refererUrl = $this->getApp()->getHelper('Url')->getRefererUrl()) {
            $this->redirect($refererUrl);
            return true;
        }

        if($alternateUrl){
            $this->redirect($alternateUrl);
            return true;
        }

        return false;
    }

    /**
     * @return $this
     *
     * Redirects the user to the login page
     */
    public function authRedirect()
    {
        auth_redirect();
        return $this;
    }

    /**
     * @return null|bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToReturnUrl()
    {
        if($url = $this->getApp()->getRequest()->getReturnUrl()){
            $this->getApp()->getRequest()->unsetRemoveUrl();
            return $this->redirect($url);
        }

        return false;
    }

}