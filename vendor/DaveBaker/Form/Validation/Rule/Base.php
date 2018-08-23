<?php

namespace DaveBaker\Form\Validation\Rule;

abstract class Base
    extends \DaveBaker\Core\Base
    implements BaseInterface
{
    protected $name;
    protected $niceName;
    protected $value;
    protected $inputError = '';
    protected $mainError = '';

    /**
     * @param $name
     * @param $niceName
     * @param $value
     * @return $this
     */
    public function configure(
        $name,
        $niceName,
        $value
    ) {
        $this->name = $name;
        $this->niceName = $niceName;
        $this->value = $value;

        return $this;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNiceName()
    {
        return $this->niceName;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getInputError()
    {
        return $this->inputError;
    }

    /**
     * @param $inputError
     * @return $this
     */
    public function setInputError($inputError)
    {
        $this->inputError = $inputError;
        return $this;
    }

    /**
     * @return string
     */
    public function getMainError()
    {
        return $this->mainError;
    }

    /**
     * @param $mainError
     * @return $this
     */
    public function setMainError($mainError)
    {
        $this->mainError = $mainError;
        return $this;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function doErrorReplacers($string = '')
    {
        foreach($this->getErrorReplacers() as $k => $errorReplacer) {
            $string = str_replace("{{" . $k . "}}", $errorReplacer, $string);
        }

        return $string;
    }

    /**
     * @return array
     */
    protected function getErrorReplacers()
    {
        return [
            'niceName' => $this->getNiceName()
        ];
    }

    /**
     * @return string
     */
    protected function getMainErrorOutput()
    {
        return $this->doErrorReplacers($this->mainError);
    }

    /**
     * @return string
     */
    protected function getInputErrorOutput()
    {
        return $this->doErrorReplacers($this->inputError);
    }

    /**
     * @return \DaveBaker\Form\Validation\Rule\Error
     */
    protected function createError()
    {
        /** @var \DaveBaker\Form\Validation\Rule\Error $error */
        $error = $this->createObject('\DaveBaker\Form\Validation\Rule\Error');
        return $error->setErrors($this->getMainErrorOutput(), $this->getInputErrorOutput());
    }
}