<?php

/**
 * Форма главной страницы клиента
 */
class Form_Index_client extends App_Form
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
		
		// Задаем атрибут class для формы
        $this->setAttrib('class', 'formMainClient');

		// Добавление элемента в форму
		$this->addElement('submit', 'clientRegistry', array(
		'required' => false,
		'ignore' => true,
		'label' => 'Старт',
	 	));
		
		// Добавление элемента в форму
		$this->addElement('submit', 'clientLogin', array(
		'required' => false,
		'ignore' => true,
		'label' => 'Вход',
	 	));
		
		// Группируем элементы    
        $this->addDisplayGroup(
            array('clientRegistry','clientLogin'), 'clientGroup',
            array(
                'legend' => 'Для клиента'
            )
        );
			
			
	}	
				
}