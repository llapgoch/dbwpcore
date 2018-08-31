<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class FullDateTime
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class FullDateTime
    extends Base
    implements OutputProcessorInterface
{
    public function process($value)
    {
        return $this->getDateHelper()->utcDbDateTimeToLocalOutput($value);
    }
}