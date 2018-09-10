<?php

namespace DaveBaker\Core\Api;
/**
 * Class Manager
 * @package DaveBaker\Core\Page
 */
class Manager extends \DaveBaker\Core\Base
{
    const ROUTE_VERSION = 'v1';
    const NUM_PARAMETERS = 15;
    const ENDPOINT_NAMESPACE_SUFFIX = 'api';
    const WP_REST_NONCE_ID = 'wp_rest';

    /** @var array  */
    protected $routes = [];
    /** @var string  */
    protected $paramsRegex = "?(?P<key{{key}}>[^/]+)?/?(?P<value{{value}}>[^/]+)?/";
    /** @var string  */
    protected $fullParamsRegex = '';
    /** @var string  */
    protected $namespaceCode = 'rest';

    /**
     * @return \DaveBaker\Core\Base|void
     */
    protected function _construct()
    {
        parent::_construct();

        for($i = 1; $i <= self::NUM_PARAMETERS; $i++){
            $this->fullParamsRegex .= str_replace(['{{key}}', '{{value}}'], [$i, $i], $this->paramsRegex);
        }
    }

    /**
     * @param $route
     * @param $controllerClass
     * @return $this
     */
    public function addRoute(
        $route,
        $controllerClass
    ) {
       $this->routes[$route] = $controllerClass;
       return $this;
    }

    /**
     * @param $endpoint
     * @param array $params
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrl($endpoint, $params = [], $includeNonce = true)
    {
        $restParams = [];
        $paramString = '';
        $helper = $this->getUtilHelper();

        foreach($params as $k => $param){
            $paramString .= $helper->escAttr($k) . "/" . $helper->escAttr($param);
        }

        $url = get_rest_url(
            null,
            $this->getEndpointNamespace() . "/" . trim($endpoint, '/') . "/" . $paramString
        );

        if($includeNonce){
            $url = wp_nonce_url($url, self::WP_REST_NONCE_ID);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getEndpointNamespace()
    {
        return $this->getNamespacedOption(self::ENDPOINT_NAMESPACE_SUFFIX) . "/" . self::ROUTE_VERSION;
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerRoutes()
    {
        foreach ($this->routes as $route => $controllerClass) {
            $controller = $this->createAppObject($controllerClass);

            foreach(get_class_methods($controller) as $method) {
                if (preg_match("/Action/", $method)) {
                    $actionTag = $this->getUtilHelper()->camelToUnderscore($method);
                    $actionTag = preg_replace("/_action/", "", $actionTag);


                    register_rest_route(
                        $this->getEndpointNamespace(),
                        trailingslashit($route) . trailingslashit($actionTag)  . $this->fullParamsRegex , [
                            'callback' => function(\WP_REST_Request $request) use ($controller, $method){
                                $params = $request->get_params();
                                $assocParams = [];
                                $numParams = floor(count($params)/2);

                                for($i = 1; $i <= $numParams; $i++){
                                    if(isset($params["key{$i}"]) && isset($params["value{$i}"])) {
                                        $assocParams[$params["key{$i}"]] = $params["value{$i}"];
                                    }
                                }

                                return $controller->{$method}($assocParams, $request);
                            }
                        ]
                    );
                }
            }
        }
    }
}