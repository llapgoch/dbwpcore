<?php

namespace DaveBaker\Form\Block;
/**
 * Interface BaseInterface
 * @package DaveBaker\Form\Block
 */
interface BaseInterface extends \DaveBaker\Core\Block\TemplateInterface
{
    public function setElementName($elementName);
    public function getElementName();
    public function getElementType();
    public function setElementType($elementType);
    public function setElementValue($elementValue);
    public function getElementValue();
    public function setIgnoreLock($val);
    public function getIgnoreLock();
    public function setLock($val);
    public function isLocked();
}
