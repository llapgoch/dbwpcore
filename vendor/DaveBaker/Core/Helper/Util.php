<?php

namespace DaveBaker\Core\Helper;
/**
 * Class Util
 * @package DaveBaker\Core\Helper
 */
class Util extends Base
{
    /**
     * @param $input
     * @return string
     */
    public function camelToUnderscore($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param $text
     * @return null|string
     */
    public function createUrlKeyFromText($text, $replaceCharacter = '-')
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', $replaceCharacter, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $replaceCharacter);

        // remove duplicate -
        $text = preg_replace('~-+~', $replaceCharacter, $text);

        return strtolower($text);
    }

    /**
     * @param $attr string
     * @return string
     */
    public function escAttr($attr)
    {
        return esc_attr($attr);
    }

    /**
     * @param $arr
     * @return string
     */
    public function createKeyFromArray($arr)
    {
        return md5(serialize($arr));
    }

    /**
     * @param $html string
     * @return string
     */
    public function escapeHtml($html)
    {
        return esc_html($html);
    }
    
    /**
     * @param $text
     * @return string
     */
    public function translate($text)
    {
        return _e($text);
    }

}