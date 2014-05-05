<?php

//функции для работы с reCAPTHCA

/*
 * getCaptchaKey() - получение нужного ключа
 * @param string $keyType
 * @return string $key
*/
function getCaptchaKey($keyType) {
    static $serverName;
    if (empty($serverName)) {
        $serverName = preg_replace('/^(www.){0,1}/', '', $_SERVER['SERVER_NAME']);
    }
    $config = new Zend_Config_Ini('../../main/application/settings/recaptcha.ini', $serverName);
    return $config->$keyType;
}