<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class ShortDateTime
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class ShortDateTime
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
        return $this->getDateHelper()->utcDbDateTimeToShortLocalOutput($value);
    }
}