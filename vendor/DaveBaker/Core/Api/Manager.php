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
        $controllerClass,
        $args = []
    ) {

        if(!isset($args['method'])){
            $args['methods'] = "GET,POST";
        }

        $this->routes[$route] = [
           'controller' => $controllerClass,
           'args' => $args
       ];

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
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     *
     * TODO: Add controller method caching at some point
     */
    public function registerRoutes()
    {
        foreach ($this->routes as $route => $routeData) {
            /** @var ControllerInterface $controller */
            $controller = $this->createAppObject($routeData['controller']);

            if(!$controller instanceof ControllerInterface){
                throw new Exception(
                    'API Controller must implement Api\ControllerInterface'
                );
            }

            foreach(get_class_methods($controller) as $method) {
                if (preg_match("/Action/", $method)) {
                    $actionTag = $this->getUtilHelper()->camelToUnderscore($method);
                    $actionTag = preg_replace("/_action/", "", $actionTag);


                    register_rest_route(
                        $this->getEndpointNamespace(),
                        trailingslashit($route) . trailingslashit($actionTag)  . $this->fullParamsRegex ,
                        array_merge_recursive([
                            'callback' => function(\WP_REST_Request $request) use ($controller, $method){
                                $params = $request->get_params();
                                $assocParams = [];
                                $numParams = floor(count($params)/2);

                                // Use our regex to separate the key value pairs in the URL to an assoc array
                                for($i = 1; $i <= $numParams; $i++){
                                    if(isset($params["key{$i}"]) && isset($params["value{$i}"])) {
                                        $assocParams[$params["key{$i}"]] = $params["value{$i}"];
                                        unset($params["key{$i}"]);
                                        unset($params["value{$i}"]);
                                    }
                                }

                                // Merge in any params which may have come from post
                                $assocParams = array_merge($assocParams, $params);

                                if(($isAllowed = $controller->isAllowed()) !== true){
                                    return $isAllowed;
                                }

                                try {
                                    $controller->preDispatch();
                                    $res = $controller->{$method}($assocParams, $request);
                                    $controller->postDispatch();
                                } catch (\Exception $e){
                                    return new \WP_Error(
                                        400,
                                        $e->getMessage(),
                                        ['status' => 400]
                                    );
                                }

                                return array_merge(
                                    ['data' => $res],
                                    $controller->getBlockReplacerData()
                                );
                            }
                        ], $routeData['args'])
                    );
                }
            }
        }
    }
}