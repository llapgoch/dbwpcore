<?php

namespace DaveBaker\Form\Block\Error;

/**
 * Class Main
 * @package DaveBaker\Form\Error
 *
 * An error renderer, typically for at the top of a form. Displays all errors in a list
 */
class Main extends \DaveBaker\Core\Block\Html\Base
{

    /** @var array  */
    protected $errors = [];

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier('form-error-message');
        parent::_construct();
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/errors/main.phtml');
    }

    /**
     * @param $errors
     * @return $this
     */
    public function addErrors($errors)
    {
        if(!is_array($errors)){
            $errors = [$errors];
        }

        array_map([$this, 'addError'], $errors);

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \DaveBaker\Form\Validation\Error\ErrorInterface $error
     */
    protected function addError(
        \DaveBaker\Form\Validation\Error\ErrorInterface $error
    ) {
        $this->errors[] = $error;
    }

}