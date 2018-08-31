<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class ShortDate
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class ShortDate
    extends Base
    implements OutputProcessorInterface
{
    /**
     * @param $value
     * @return false|string
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function process($value)
    {
        return $this->getDateHelper()->utcDbDateToShortLocalOutput($value);
    }
}