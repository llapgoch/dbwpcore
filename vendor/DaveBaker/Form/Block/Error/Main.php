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
    /**
     * @var array
     */
    protected $errors = [];

    protected function init()
    {
        $this->setTemplate('form/errors/main.phtml');
        $this->addTagIdentifier('form-error-message');
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