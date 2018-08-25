<?php

namespace DaveBaker\Core\Helper;

/**
 * Class Date
 * @package DaveBaker\Core\Helper
 */
class Date extends Base
{
    const CONFIG_DATE_TIME_DB_FORMAT = 'dateTimeDbFormat';
    const CONFIG_LOCAL_DATE_PATTERN = 'dateLocalPattern';
    const CONFIG_LOCAL_DATE_OUPUT_FORMAT = 'dateLocalOutputFormat';
    const CONFIG_LOCAL_DATE_TIME_OUTPUT_FORMAT = 'dateTimeLocalOutputFormat';
    const CONFIG_LOCAL_DATE_SHORT_OUTPUT_FORMAT = 'dateLocalShortOutputFormat';
    const CONFIG_LOCAL_DATE_TIME_SHORT_OUTPUT_FORMAT = 'dateTimeLocalShortOutputFormat';

    /**
     * @param $timestamp
     * @return bool|string
     *
     */
    public function utcTimestampToDb($timestamp = null)
    {
        /** @var string $format */
        $format = $this->getDbDateTimeFormat();

        if(!$timestamp){
            return date($format);
        }
        return date($format, $timestamp);
    }

    /**
     * @param $utcDateTime string
     * @return string
     */
    public function utcDbToLocal($utcDateTime)
    {
        return get_date_from_gmt($utcDateTime);
    }

    /**
     * @param $utcDateTime string
     * @return bool|string
     */
    public function utcDbDateToLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @param $utcDateTime string
     * @return bool|string
     */
    public function utcDbDateTimeToLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateTimeLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @param $utcDateTime string
     * @return bool|string
     */
    public function utcDbDateToShortLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateShortLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @param $utcDateTime string
     * @return bool|string
     */
    public function utcDbDateTimeToShortLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateTimeShortLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }


    /**
     * @param $utcTimestamp int
     * @return string
     */
    function utcTimestampToLocal($utcTimestamp)
    {
        return $this->utcDateStringToLocal(
            date($this->getDbDateTimeFormat(), $utcTimestamp)
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getLocalDatePattern()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_PATTERN);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDbDateTimeFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_DATE_TIME_DB_FORMAT);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDateLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_OUPUT_FORMAT);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDateTimeLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_TIME_OUTPUT_FORMAT);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDateShortLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_SHORT_OUTPUT_FORMAT);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDateTimeShortLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_TIME_SHORT_OUTPUT_FORMAT);
    }
}
