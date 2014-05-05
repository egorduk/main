<?php

require_once 'Zend/Validate/Abstract.php';
require_once('Recaptcha/recaptchalib.php');
require_once '../../main/functions/recaptcha.php';

class Recaptcha_Validate_ReCaptcha extends Zend_Validate_Abstract
{
    const IS_ERROR = 'isError';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::IS_ERROR => "Введены неверные символы"
    );

    protected $_challengeField, $_privateKey; 
    
    public function __construct($challengeField) 
    {
        $this->_challengeField = (string)$challengeField;
        $this->_privateKey = getCaptchaKey('private');
    }
    
    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        if (!recaptcha_check_answer($this->_privateKey, $_SERVER["REMOTE_ADDR"], $this->_challengeField, $valueString)->is_valid) {
            $this->_error();
            return false;
        }

        return true;
    }

}