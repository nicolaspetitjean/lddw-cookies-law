<?php

class LddwHelper
{
    static function getValueMultilang($key)
    {
        $languages = Language::getLanguages();
        $results_array = array();
        foreach($languages as $language) {
            $results_array[$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang'], Configuration::get($key, $language['id_lang']));
        }

        return $results_array;
    }

    static function getConfigMultilang($key)
    {
        $languages = Language::getLanguages();
        $results_array = array();
        foreach($languages as $language) {
            $results_array[$language['id_lang']] = Configuration::get($key, $language['id_lang']);
        }

        return $results_array;
    }

    static function validateNotEmpty($values)
    {
        if(!is_array($values)) {
            $values = array($values);
        }

        $result = true;
        foreach($values as $value) {
            if(empty($value)) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}