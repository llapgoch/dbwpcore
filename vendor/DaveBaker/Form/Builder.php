<?php

namespace DaveBaker\Form;
/**
 * Class Builder
 * @package DaveBaker\Form
 */
class Builder extends \DaveBaker\Core\Base
{
    const BASE_ELEMENT_NAMESPACE = '\DaveBaker\Form\Block\\';
    const DEFAULT_LABEL_DEFINITION = 'Label';
    /** @var string  */
    protected $formName = '';

    /**
     * @param array $schema
     * @return array
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     *
     * schema - an array of elements;
     *  - name
     *  - type (Fully qualified class name or Core Rule E.g. Input\Text)
     *  - labelName
     *  - value
     *  - attributes
     *  - class
     */
    public function build($schema = [])
    {
        $elements = [];
        foreach($schema as $scheme){
            $elements = array_merge($elements, $this->createElements($scheme));
        }

        return $elements;
    }

    /**
     * @param $formName
     * @return $this
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;
        return $this;
    }

    public function getFormName()
    {
        return $this->formName;
    }

    /**
     * @param array $scheme
     * @return array
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createElements($scheme = [])
    {
        $blocks = [];

        if(!isset($this->formName)){
            throw new Exception('formName must be set before creating form elements');
        }

        if(!isset($scheme['name'])){
            throw new Exception('name not set');
        }

        if(!isset($scheme['type'])){
            throw new Exception('type not set');
        }

        // If the class name is fully qualified, just create it, otherwise add it to the base path
        if(substr($scheme['type'], 0, 1) !== '\\') {
            $scheme['type'] = self::BASE_ELEMENT_NAMESPACE . $scheme['type'];
        }

        $inputBlock = $this->getApp()->getBlockManager()->createBlock(
            $scheme['type'],
            $this->formName . "." . str_replace("_", ".", $scheme['name']) . 'element'
        )->setElementName($scheme['name']);


        if(isset($scheme['labelName'])){
            $blockId = $this->formName . "_" . $scheme['name'];

            $labelBlock = $this->getApp()->getBlockManager()->createBlock(
                self::BASE_ELEMENT_NAMESPACE . self::DEFAULT_LABEL_DEFINITION,
                $this->formName . "." . str_replace("_", ".", $scheme['name']) . 'label'
            )->setLabelName($scheme['labelName'])->setForId($blockId);

            $inputBlock->addAttribute(['id' => $blockId]);

            $blocks[$scheme['name'] . "_label"] = $labelBlock;
        }

        $blocks[$scheme['name'] . '_element'] = $inputBlock;

        if(isset($scheme['class'])){
            $inputBlock->addClass($scheme['class']);
        }

        if(isset($scheme['value'])){
            $inputBlock->setElementValue($scheme['value']);
        }

        if(isset($scheme['attributes'])){
            $inputBlock->setAttributes($scheme['attributes']);
        }

        return $blocks;
    }
}