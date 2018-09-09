<?php

namespace DaveBaker\Core\Api;
/**
 * Class Manager
 * @package DaveBaker\Core\Page
 */
class Manager extends \DaveBaker\Core\Base
{
    const ROUTE_VERSION = 'v1';
    const NUM_PARAMETERS = 10;

    /** @var array  */
    protected $routes = [];
    /** @var string  */
    protected $paramsRegex = "?(?P<key{{key}}>[^/]+)?/?(?P<value{{value}}>[^/]+)?/";
    /** @var string  */
    protected $fullParamsRegex = '';
    protected $namespaceCode = 'api';

    /**
     * @return \DaveBaker\Core\Base|void
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
     * @return $this
     */
    protected function addEvents()
    {
        add_action( 'rest_api_init', function () {
            $this->registerRoutes();
        });

        return $this;
    }
    /**
     * @param string $route
     * @param string $controller
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addRoute(
        $route,
        $controllerClass
    ) {

       $this->routes[$route] = $controllerClass;
       return $this;
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function registerRoutes()
    {
        foreach ($this->routes as $route => $controllerClass) {
            $controller = $this->createAppObject($controllerClass);

            foreach(get_class_methods($controller) as $method) {
                if (preg_match("/Action/", $method)) {
                    $actionTag = $this->getUtilHelper()->camelToUnderscore($method);
                    $actionTag = preg_replace("/_action/", "", $actionTag);

                    register_rest_route(
                        $this->getNamespacedOption('') . "/" . self::ROUTE_VERSION,
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