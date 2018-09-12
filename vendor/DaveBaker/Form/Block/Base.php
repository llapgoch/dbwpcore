<?php

namespace DaveBaker\Form\Block;

/**
 * Class Base
 * @package DaveBaker\Form\Block
 */
abstract class Base
    extends \DaveBaker\Core\Block\Html\Base
    implements BaseInterface
{
    const IGNORE_LOCK_DATA_KEY = 'ignore_lock';
    const LOCKED_DATA_KEY = 'locked';

    /** @var string  */
    protected $elementName = '';
    /** @var string  */
    protected $elementValue = '';
    /** @var string  */
    protected $elementType = '';

    protected function _construct()
    {
        $this->setIgnoreLock(false);
        $this->setLock(false);

        return parent::_construct();
    }

    /**
     * @param $elementName string
     * @return $this
     */
    public function setElementName($elementName)
    {
        $this->elementName = $elementName;
        return $this;
    }

    /**
     * @return $this
     */
    public function setLock($lock)
    {
        $this->setData(self::LOCKED_DATA_KEY, (bool) $lock);

        if($lock && !$this->getIgnoreLock()) {
            $this->addAttribute([
                'readonly' => 'readonly',
                'data-is-locked' => 1,
            ]);
        }else{
            $this->removeAttribute(['data-is-locked', 'readonly']);
        }

        $this->addClass('js-is-locked');

        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        if($this->getIgnoreLock()){
            return false;
        }

        return (bool) $this->getData(self::LOCKED_DATA_KEY);
    }

    /**
     * @param $val
     * @return $this
     */
    public function setIgnoreLock($val)
    {
        $this->setData(self::IGNORE_LOCK_DATA_KEY, (bool) $val);
        return $this;
    }

    /**
     * @return bool
     */
    public function getIgnoreLock()
    {
        return $this->getData(self::IGNORE_LOCK_DATA_KEY);
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     * @param $elementType string
     * @return $this
     */
    public function setElementType($elementType)
    {
        $this->removeTagIdentifier($this->elementType);
        $this->elementType = $elementType;
        $this->addTagIdentifier($elementType);
        return $this;
    }

    /**
     * @return string
     */
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * @param $elementValue string
     * @return $this
     */
    public function setElementValue($elementValue)
    {
        $this->elementValue = $elementValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementValue()
    {
        return $this->elementValue;
    }

}

