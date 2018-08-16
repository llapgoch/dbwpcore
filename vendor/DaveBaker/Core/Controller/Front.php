<?php

namespace DaveBaker\Core\Controller;

class Front
{

    public function __construct()
    {
        $this->initialise();
    }

    protected function initialise()
    {

    }

    public function isOnPage($pageCode){
        global $post;

        if(!$post){
            return false;
        }

        if(self::getOption($pageCode) == $post->ID){
            return true;
        }

        return false;
    }

}