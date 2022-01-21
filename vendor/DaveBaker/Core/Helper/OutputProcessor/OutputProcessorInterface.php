<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Interface OutputProcessorInterface
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
interface OutputProcessorInterface
    extends BaseInterface
{
    public function process($value);
}