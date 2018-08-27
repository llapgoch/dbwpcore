<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Directory
 * @package DaveBaker\Core\Helper
 */
class Directory extends Base
{
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
}