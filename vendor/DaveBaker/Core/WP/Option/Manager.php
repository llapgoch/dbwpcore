<?php

namespace DaveBaker\Core\WP\Option;

class Manager extends \DaveBaker\Core\WP\Base
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
     */
    public function set($optionId, $value){
        $context = $this->fireEvent('set_option', ['option'=>$optionId, 'option_value' => $value]);
        update_option($optionId, $context->getOptionValue());
    }

    /**
     * @param $option
     */
    public function remove($option){
        $this->fireEvent('remove_option', ['option' => 'option']);
        delete_option($option);
    }
}