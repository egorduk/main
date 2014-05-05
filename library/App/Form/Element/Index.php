<?php

/**
 * App_Form_Element_Index
 * 
 * Элементы формы главной страницы
 */
class App_Form_Element_Index extends Zend_Form_Element_Submit
{
    /**
     * Инициализация элемента
     * 
     * return void
     */  
    public function init()
    {
        $this->setLabel('Электронная почта:');
        $this->setAttrib('maxlength', 30);
        $this->addValidator('EmailAddress', true);
        //$this->addValidator('NoDbRecordExists', true, array('users', 'email'));
        $this->addFilter('StringTrim');
    }
}