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
     * @param $pageIdentifier
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToPage($pageIdentifier)
    {
        $this->redirect(
            $this->getApp()->getPageManager()->getUrl($pageIdentifier)
        );
        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectReferer()
    {
        $this->redirect($this->getApp()->getHelper('Url')->getRefererUrl());
        return $this;
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
            return $this->redirect($url);
        }

        return false;
    }

}