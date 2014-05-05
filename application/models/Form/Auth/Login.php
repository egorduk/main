<?php

class Form_Auth_Login extends App_Form
{
	public function init()
	{
 		// Вызываем родительский метод
        parent::init();
		
		// Указываем action формы
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'auth_login'));
        
        // Указываем метод формы
        $this->setMethod('post');
		
		// Set form's name
	   	$this->setName('formLogin');
		
		// Задаем атрибут class для формы
        $this->setAttrib('class', 'formLogin');
		
		// Используемый собственный элемент App_Form_Element_Email
        $email = new App_Form_Element_Email('email', array(
            'required' => true,
        ));
        $this->addElement($email);
		
		// Добавление элемента в форму
		$this->addElement('password', 'password', array(
		//'filters' => array('StringTrim'),
		'validators' => array(
	 		array('StringLength', false, array(6, 30)),
		),
		'required' => true,
		'label' => 'Пароль:',
		));

		// Добавление элемента в форму
		$this->addElement('submit', 'login', array(
			'required' => false,
			'ignore' => true,
			'label' => 'Войти',
	 	));
		
		// Создаём атрибут id = submitbutton
	    //$submit->setAttrib('id', 'submitbutton');
		
		// Группируем элементы    
        // Группа полей связанных с авторизационными данными
        $this->addDisplayGroup(
            array('email','password','login'), 'authDataGroup',
            array(
                'legend' => 'Авторизационные данные'
            )
        );
			
			
	}	
				
}