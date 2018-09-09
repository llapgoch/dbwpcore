<?php

namespace DaveBaker\Core\Api;

/**
 * Class Controller
 * @package DaveBaker\Core\Api
 *
 * Methods are defined with an Action suffix, E.g. addAction
 */
class Test extends Controller{
    public function getAction($params)
    {
        var_dump($params);
        return ["WOO" => "FOO"];
    }

}