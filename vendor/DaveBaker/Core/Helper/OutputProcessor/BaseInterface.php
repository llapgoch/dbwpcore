<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Interface OutputProcessorInterface
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
interface BaseInterface
{
    public function setModel(\DaveBaker\Core\Model\Db\BaseInterface $model);
    public function getModel();
}