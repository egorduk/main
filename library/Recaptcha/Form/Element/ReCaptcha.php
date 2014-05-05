<?php

require_once 'Zend/Form/Element/Xhtml.php';
require_once 'Recaptcha/recaptchalib.php' ;

class Recaptcha_Form_Element_ReCaptcha extends Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formReCaptcha';
}
