<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class FullDate
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class FullDate
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
        return $this->getDateHelper()->utcDbDateToLocalOutput($value);
    }
}