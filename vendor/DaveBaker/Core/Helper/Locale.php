<?php
namespace DaveBaker\Core\Helper;
/**
 * Class Date
 * @package DaveBaker\Core\Helper
 */
class Locale extends Base
{

    public function formatCurrency(
        $amount,
        $includeSymbol = true,
        $locale = null
    ) {
        if(!$locale){
            $locale = get_locale();
        }

        $fmt = new \NumberFormatter( 'en_GB', \NumberFormatter::CURRENCY );

        return $fmt->formatCurrency($amount, "GBP");
    }

    /**
     * @return \DaveBaker\Core\Helper\OutputProcessor\Currency
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getOutputProcessorCurrency()
    {
        return $this->createAppObject('\DaveBaker\Core\Helper\OutputProcessor\Currency');
    }
}