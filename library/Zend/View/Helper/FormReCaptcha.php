<?php

require_once 'Zend/View/Helper/FormElement.php';
require_once 'Recaptcha/recaptchalib.php';

/**
 * Helper to generate a "text" element
 */
class Zend_View_Helper_FormReCaptcha extends Zend_View_Helper_FormElement
{

    /**
     * Generates a 'captcha' element.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formReCaptcha($name, $value = null, $attribs = null)
    {
        //$info = $this->_getInfo($name, $value, $attribs);
        //extract($info); // name, value, attribs, options, listsep, disable

        /*if (empty($attribs['key'])) {
            throw new Exception('Undefined reCAPTHCA public key');
        }*/

        $xhtml = recaptcha_get_html(getCaptchaKey('public'));

        return $xhtml;
    }
}