<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Url
 * @package DaveBaker\Core\Helper
 */
class Url extends Base
{
    /**
     * @param $url
     * @return string
     */
    public function getUrl($url)
    {
        return get_site_url() . $url;
    }

    /**
     * @param string $pageIdentidier
     * @return false|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPageUrl($pageIdentidier)
    {
        return $this->getApp()->getPageManager()->getUrl($pageIdentidier);
    }
}