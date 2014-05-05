<?php

/**
 * Form_Index_author
 * 
 * Форма главной страницы автора
 */
class Form_Index_author extends App_Form
{
	public function init()
	{
 		// Вызываем родительский метод
        parent::init();
		
		// Указываем action формы
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'auth_index'));
        
        // Указываем метод формы
        $this->setMethod('post');
		
		//Задаем атрибут class для формы
        $this->setAttrib('class', 'formMainAuthor');

		// Добавление элемента в форму
		$this->addElement('submit', 'authorRegistry', array(
		'required' => false,
		'ignore' => true,
		'label' => 'Регистрация',
	 	));
		
		// Добавление элемента в форму
		$this->addElement('submit', 'authorLogin', array(
		'required' => false,
		'ignore' => true,
		'label' => 'Войти',
	 	));
		
		// Группируем элементы    
        $this->addDisplayGroup(
            array('authorRegistry','authorLogin'), 'authorGroup',
            array(
                'legend' => 'Для автора'
            )
        );
			
			
	}	
				
}