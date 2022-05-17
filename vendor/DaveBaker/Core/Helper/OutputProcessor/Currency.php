<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class Currency
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class Currency
    extends Base
    implements OutputProcessorInterface
{
    /**
     * @param $value
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function process($value)
    {
        return $this->getLocaleHelper()->formatCurrency($value);
    }
}