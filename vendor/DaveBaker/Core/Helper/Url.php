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
     * @param array $params
     * @param string $returnUrl
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrl($url, $params = [], $returnUrl = null)
    {
        if($returnUrl === true){
            $returnUrl = $this->getCurrentUrl();
        }

        if($returnUrl) {
            $params[\DaveBaker\Core\App\Request::RETURN_URL_PARAM] =
                $this->getApp()->getRequest()->createReturnUrlParam($returnUrl);
        }

        return add_query_arg($params, home_url($url));
    }

    /**
     * @param string $pageIdentidier
     * @param array $params
     * @return false|string
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPageUrl($pageIdentidier, $params = [], $returnUrl = null)
    {
        return $this->getApp()->getPageManager()->getUrl($pageIdentidier, $params, $returnUrl);
    }

    /**
     * @return string
     */
    public function getRefererUrl()
    {
        if(isset($_SERVER['HTTP_REFERER'])){
            return $_SERVER['HTTP_REFERER'];
        }

        return '';
    }

    /**
     * @param array $params
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getLoginUrl($params = [], $returnUrl = null)
    {
        return $this->getUrl(wp_login_url(), $params, $returnUrl);
    }

    /**
     * @param array $params
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getRegistrationUrl($params = [], $returnUrl = null)
    {
        return $this->getUrl(wp_registration_url(), $params, $returnUrl);
    }

    /**
     * @param array $params
     * @param string $returnUrl
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getForgotPasswordUrl($params = [], $returnUrl = null)
    {
        return $this->getUrl(wp_lostpassword_url(), $params, $returnUrl);
    }

    /**
     * @param array $params
     * @param bool $withQueryString
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCurrentUrl($params = [], $withQueryString = true)
    {
        global $wp;
        $url = $wp->request;

        if($withQueryString && isset($_SERVER['QUERY_STRING'])){
            $url = add_query_arg($_SERVER['QUERY_STRING'], '', $url);
        }

        return $this->getUrl($url, $params);
    }
}