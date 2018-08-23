<?php
namespace DaveBaker\Form\Validation\Rule\Configurator;

interface BaseInterface
{
    public function getRules();
    public function setValues($values = []);
}