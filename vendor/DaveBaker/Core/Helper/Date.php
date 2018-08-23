<?php

namespace DaveBaker\Core\Helper;

class Date extends Base
{
    const DB_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param $timestamp
     * @return bool|string
     */
    public function getDbTime($timestamp = null)
    {
        if(!$timestamp){
            return date(self::DB_FORMAT);
        }
        return date(self::DB_FORMAT, $timestamp);
    }
}
