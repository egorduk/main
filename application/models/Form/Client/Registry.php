<?php


class Form_Client_Registry extends App_Form
{
    /**
     * Создание формы
     */
    public function init()
    {
        parent::init();
        
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'auth_client_reg'));

        $this->setMethod('post');
        
       	$this->setAttrib('class', 'formClientRegistry');
       	    	
        
        $name = new Zend_Form_Element_Text('nickname', array(
            'required'    => true,
            'label'       => 'Ник в системе:',
            'maxlength'   => '30',
            'validators'  => array(
                array('StringLength', true, array(5, 30)),
				array('Regex', true, array('pattern' => '/^[А-Яа-яЁёa-zA-Z]+$/u'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),	
        ));

        $this->addElement($name); 

		// Используется элемент App_Form_Element_Email
        $email = new App_Form_Element_Email('email', array(
            'required'    => true,
			/*'validators' => array(
	 			array('DbRecordExists',true, array('users', 'email')),
			),*/
        ));

        // Добавление элемента в форму
        $this->addElement($email);
        
        // Password элемент "Пароль". Значение проверяется валидатором App_Validate_Password
        $password = new Zend_Form_Element_Password('password', array(
            'required'    => true,
            'label'       => 'Пароль:',
            'validators'  => array('Password'),		
        ));
        
        $this->addElement($password);
        
        // Элемент "Подтверждение пароля". 
        // Проверяется на совпадение с полем "Пароль" валидатором App_Validate_EqualInputs
        $passwordApprove = new Zend_Form_Element_Password('password_approve', array(
            'required'    => true,
            'label'       => 'Подтвердите пароль:',
            'validators'  => array(
				array('EqualInputs', true, array('password')),
			),	
        )); 
        
        $this->addElement($passwordApprove);  
          
		/*$captcha = new Zend_Form_Element_Captcha('captcha', array(
            'label' => "Введите символы:",
            'captcha' => array(
                'captcha'   => 'Image', // Тип CAPTCHA
                'wordLen'   => 4,       // Количество генерируемых символов
                'width'     => 260,     // Ширина изображения
                'timeout'   => 120,     // Время жизни сессии хранящей символы
                'expiration'=> 300,     // Время жизни изображения в файловой системе
                'font'      => Zend_Registry::get('config')->path->rootPublic . 'fonts/arial.ttf', // Путь к шрифту
                'imgDir'    => Zend_Registry::get('config')->path->rootPublic . 'images/captcha/', // Путь к изобр.
                'imgUrl'    => Zend_Registry::get('config')->url->base. '/images/captcha/', // Адрес папки с изображениями
                'gcFreq'    => 5        // Частота вызова сборщика мусора
            ),
        ));             
        $this->addElement($captcha);*/
        
        $element = new Recaptcha_Form_Element_ReCaptcha('recaptcha_response_field');
        $element->addValidator(new Recaptcha_Validate_ReCaptcha(@$_POST['recaptcha_challenge_field']))
        ->setRequired(true);
        $this->addElement($element);
        
        
        // Переопределяем сообщение об ошибке для валидатора NotEmpty
        $validatorNotEmpty = new Zend_Validate_NotEmpty();
        $validatorNotEmpty->setMessages(array( 
            Zend_Validate_NotEmpty::IS_EMPTY  => 'agreeRules'));
		
		// Checkbox элемент "Согласен с правилами". 
        $agreeRules = new Zend_Form_Element_Checkbox('agreeRules', array(
            'required'    => true,
            'label'       => 'С правилами системы ознакомлен(-а)',
            'filters'     => array('Int'),
            'validators'  => array($validatorNotEmpty),
        )); 
        $this->addElement($agreeRules);     

		// Кнопка Submit
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label'       => 'Зарегистрироваться',
        ));
        
        $submit->setDecorators(array('ViewHelper'));
               
        $this->addElement($submit);
        
        // Кнопка Reset, возвращает форму в начальное состояние
        $reset = new Zend_Form_Element_Reset('reset', array(
            'label'       => 'Очистить',
        ));
        
        // Перезаписываем декораторы, что-бы выставить две кнопки в ряд
        $reset->setDecorators(array('ViewHelper'));
		
        $this->addElement($reset);

        // Группируем элементы
        // Группа полей связанных с авторизационными данными
        $this->addDisplayGroup(
            array('nickname','email', 'password', 'password_approve','agreeRules','recaptcha_response_field','submit','reset'), 'authDataGroup',
            array(
                'legend' => 'Авторизационные данные'
            )
        );      
        
    }
}