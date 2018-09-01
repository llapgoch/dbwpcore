<?php

namespace DaveBaker\Form\Block;

/**
 * Class Textarea
 * @package DaveBaker\Form\Block
 */
class Textarea
    extends Base
    implements ValueSetterInterface
{
    /** @var string  */
    protected $mainTagName = 'textarea';

    protected function init()
    {
        parent::init();
        $this->setTemplate('form/textarea.phtml');
        $this->setElementType('textarea');
    }
}