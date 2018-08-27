<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Directory
 * @package DaveBaker\Core\Helper
 */
class Directory extends Base
{
    const COUNTRY_CODE_DEFAULT_CONFIG_KEY = 'countryCodeDefault';
    /**
     * @param $countryCode
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCountry($countryCode)
    {
        return $this->createAppObject(
            '\DaveBaker\Core\Model\Db\Directory\Country'
        )->load($countryCode, 'country_code');
    }

    /**
     * @param $countryCode
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function isValidCountryCode($countryCode)
    {
        return (bool) $this->getCountry($countryCode)->getId();
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDefaultCountryCode()
    {
        return $this->getGeneralConfigValue(self::COUNTRY_CODE_DEFAULT_CONFIG_KEY);
    }
}