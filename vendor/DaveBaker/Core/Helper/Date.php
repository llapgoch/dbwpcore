<?php

namespace DaveBaker\Core\Helper;

/**
 * Class Date
 * @package DaveBaker\Core\Helper
 */
class Date extends Base
{
    const CONFIG_DATE_DB_FORMAT = 'dbDateFormat';
    const CONFIG_DATE_PATTERN = 'datePattern';

    /**
     * @param $timestamp
     * @return bool|string
     */
    public function getDbTime($timestamp = null)
    {
        /** @var string $format */
        $format = $this->getDbDateFormat();

        if(!$timestamp){
            return date($format);
        }
        return date($format, $timestamp);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getDatePattern()
    {
        return $this->getGeneralConfigValue(self::CONFIG_DATE_PATTERN);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getDbDateFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_DATE_DB_FORMAT);
    }
}
