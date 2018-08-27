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
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectToPage($pageIdentifier)
    {
        $this->redirect(
            $this->getApp()->getPageManager()->getUrl($pageIdentifier)
        );
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function redirectReferer()
    {
        $this->redirect($this->getApp()->getHelper('Url')->getRefererUrl());
    }
}