<?php

/**
 * App_Form_Element_Email
 * 
 * Элемент формы - электронная почта
 * 
 */
class App_Form_Element_Email extends Zend_Form_Element_Text 
{
    /**
     * Инициализация элемента
     * 
     * return void
     */  
    public function init()
    {
        $this->setLabel('E-mail:');
        $this->setAttrib('maxlength', 30);
        $this->addValidator('EmailAddress', true);
		$this->addFilter('StripTags');
        $this->addFilter('StringTrim');
    }
}