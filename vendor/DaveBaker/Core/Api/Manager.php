<?php

namespace DaveBaker\Core\Api;
use DaveBaker\Core\Event\Context;

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
    /** @var \WP_REST_Request */
    protected $restRequest;
    /** @var array  */
    protected $fullRoutes = [];

    /**
     * @return \DaveBaker\Core\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        parent::_construct();

        for($i = 1; $i <= self::NUM_PARAMETERS; $i++){
            $this->fullParamsRegex .= str_replace(['{{key}}', '{{value}}'], [$i, $i], $this->paramsRegex);
        }

        $this->addEvents();
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addEvents()
    {

        $this->addEvent('rest_request_before_callbacks', function(
            $response,
            $handler,
            \WP_REST_Request $request){
                $this->restRequest = $request;
                $this->addHandles();
        });
    }

    /**
     * @param $route
     * @return null|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getRouteHandle($route)
    {
        return $this->getUtilHelper()->createUrlKeyFromText(
            $this->getNamespacedEvent(untrailingslashit($this->getEndpointNamespace()) . '/' . trim($route, '/')),
            '_'
        );
    }

    /**
     * @param $route
     * @return null|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getRouteEvent($route)
    {
        return $this->getRouteHandle($route);
    }

    /**
     * @param $fullRoute
     * @return mixed|string
     */
    public function getBaseRoute($fullRoute)
    {
        $fullRoute = trim($fullRoute, '/');

        foreach($this->fullRoutes as $route){
            $route = trim($route, '/');

            if(strpos($fullRoute, $route) === 0){
                return $route;
            }
        }

        return '';
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addHandles()
    {
        $handles = [];
        if($this->restRequest) {
            if ($baseRoute = $this->getBaseRoute($this->restRequest->get_route())) {
                $key =  $this->getUtilHelper()->createUrlKeyFromText(
                        $baseRoute,
                        '_'
                );

                $handles[] = $this->getNamespacedEvent($key);
                $this->fireEvent($key);
            }

            $key = $this->getUtilHelper()->createUrlKeyFromText(
                $this->restRequest->get_route(),
                '_'
            );

            $handles[] = $this->getNamespacedEvent($key);
            $this->fireEvent($key);
        }

        $this->addEvent('handle_register_handles', function(Context $context) use($handles){
            $currentHandles = $context->getHandles();
            $context->setHandles(array_merge($currentHandles, $handles));

            return $context;
        });

        return $this;
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

                    $routePath = trailingslashit($route) . untrailingslashit($actionTag);
                    $this->fullRoutes[] = trailingslashit($this->getEndpointNamespace()) . $routePath;

                    register_rest_route(
                        $this->getEndpointNamespace(),
                        $routePath . $this->fullParamsRegex ,
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