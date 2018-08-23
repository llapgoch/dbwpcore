<?php

namespace DaveBaker\Core\Controller;

interface BaseInterface
{
    public function preDispatch();
    public function postDispatch();
}