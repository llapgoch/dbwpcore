<?php

namespace DaveBaker\Core\WP;

interface BaseInterface
{
    public function getData();
    public function setData($key, $value);
}