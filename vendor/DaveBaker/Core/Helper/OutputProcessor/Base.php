<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class Base
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class Base extends \DaveBaker\Core\Base
{
    /** @var \DaveBaker\Core\Model\Db\BaseInterface */
    protected $model;

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $model
     * @return $this
     */
    public function setModel(
        \DaveBaker\Core\Model\Db\BaseInterface $model
    ) {
        $this->model = $model;
        return $this;
    }

    /**
     * @return \DaveBaker\Core\Model\Db\BaseInterface
     */
    public function getModel()
    {
        return $this->model;
    }
}