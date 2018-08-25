<?php

namespace DaveBaker\Core\Option;
/**
 * Class Manager
 * @package DaveBaker\Core\Option
 */
class Manager extends \DaveBaker\Core\Base
{
    /**
     * @param $optionId
     * @param null $default
     * @return mixed
     */
    public function get($optionId, $default = null){
        $option = get_option($optionId, $default);
        $context = $this->fireEvent('get_option', ['option' => $optionId, 'option_value' => $option]);
        return $context->getOptionValue();
    }

    /**
     * @param $optionId
     * @param $value
     * @return $this
     */
    public function set($optionId, $value){
        $context = $this->fireEvent('set_option', ['option'=>$optionId, 'option_value' => $value]);
        update_option($optionId, $context->getOptionValue());
        return $this;
    }

    /**
     * @param $option
     * @return $this
     */
    public function remove($option){
        $this->fireEvent('remove_option', ['option' => 'option']);
        delete_option($option);
        return $this;
    }
}