<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class Base
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class FullDateTime
    extends Base
    implements OutputProcessorInterface
{
    public function process()
    {
        return $this->getApp()->getHelper('Date')
    }
}