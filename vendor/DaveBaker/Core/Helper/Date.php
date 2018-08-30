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
     * @param null|int $timestamp
     * @return false|string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
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
     * @param string $utcDateTime
     * @return false|string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function utcDbDateToLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @param string $utcDateTime
     * @return false|string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function utcDbDateTimeToLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateTimeLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @param string $utcDateTime
     * @return false|string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function utcDbDateToShortLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateShortLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @param string $utcDateTime
     * @return false|string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function utcDbDateTimeToShortLocalOutput($utcDateTime)
    {
        return date(
            $this->getDateTimeShortLocalOutputFormat(),
            strtotime($this->utcDbToLocal($utcDateTime))
        );
    }

    /**
     * @return false|string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function currentDateShortLocalOutput()
    {
        return date($this->getDateShortLocalOutputFormat());
    }

    /**
     * @param string $utcTimestamp
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function utcTimestampToLocal($utcTimestamp)
    {
        return $this->utcDateStringToLocal(
            date($this->getDbDateTimeFormat(), $utcTimestamp)
        );
    }

    /**
     * @param $dateString
     * @return string
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function localDateToDb($dateString){
        preg_match($this->getLocalDatePattern(), $dateString, $matches);

        if(count($matches) < 4){
            return null;
        }

        return $matches[3] . "-" . $matches[2] . "-" . $matches[1];
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getLocalDatePattern()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_PATTERN);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDbDateTimeFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_DATE_TIME_DB_FORMAT);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDateLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_OUPUT_FORMAT);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDateTimeLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_TIME_OUTPUT_FORMAT);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDateShortLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_SHORT_OUTPUT_FORMAT);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDateTimeShortLocalOutputFormat()
    {
        return $this->getGeneralConfigValue(self::CONFIG_LOCAL_DATE_TIME_SHORT_OUTPUT_FORMAT);
    }
}
