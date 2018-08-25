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
     * @param $pageIdentidier string
     * @return false|string
     */
    public function getPageUrl($pageIdentidier)
    {
        return $this->getApp()->getPageManager()->getUrl($pageIdentidier);
    }
}