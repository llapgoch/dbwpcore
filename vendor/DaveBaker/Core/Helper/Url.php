<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Url
 * @package DaveBaker\Core\Helper
 */
class Url extends Base
{
    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    public function getUrl($url, $params = [])
    {
        return add_query_arg($params, get_site_url() . $url);
    }

    /**
     * @param string $pageIdentidier
     * @param array $params
     * @return false|string
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPageUrl($pageIdentidier, $params = [])
    {
        return $this->getApp()->getPageManager()->getUrl($pageIdentidier, $params);
    }

    /**
     * @return false|string
     */
    public function getRefererUrl()
    {
        return wp_get_referer();
    }
}