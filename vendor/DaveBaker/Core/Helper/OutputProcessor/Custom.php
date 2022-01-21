<?php

namespace DaveBaker\Core\Helper\OutputProcessor;
/**
 * Class Custom
 * @package DaveBaker\Core\Helper\OutputProcessor
 */
class Custom
    extends Base
    implements OutputProcessorInterface
{
    /** @var mixed */
    protected $callback;

    /**
     * @param $value
     * @return mixed
     * @throws \DaveBaker\Core\Helper\Exception
     */
    public function process($value)
    {
        if(!$this->callback){
            throw new \DaveBaker\Core\Helper\Exception('Callback must be set in OutputProcessor');
        }

        return call_user_func_array($this->callback, [$value, $this->getModel()]);
    }

    /**
     * @param mixed $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }


}