<?php


class Form_Author_Registry extends App_Form
{

    public function init()
    {
        parent::init();
        
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'auth_author_reg'));
        
        $this->setMethod('post');
        
        $this->setAttrib('class', 'formAuthorReg');

        $name = new Zend_Form_Element_Text('nickname', array(
            'required'    => true,
            'label'       => 'Ник в системе:',
            'maxlength'   => '30',
            'validators'  => array(
                array('StringLength', true, array(5, 30)),
				//array('Regex', true, array('pattern' => '/^[a-zA-Zа-яА-Я]*$/'))
            	array('Regex', true, array('pattern' => '/^[А-Яа-яЁёa-zA-Z]+$/u'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),	
        ));
        $this->addElement($name);


        $email = new App_Form_Element_Email('email', array(
            'required'    => true,
			/*'validators' => array(
	 			array('DbRecordExists',true, array('users', 'email')),
			),*/
        ));
        $this->addElement($email);
        
		
        $password = new Zend_Form_Element_Password('password', array(
            'required'    => true,
            'label'       => 'Пароль:',
            'validators'  => array('Password'),		
        ));
        $this->addElement($password);
        
		
        $passwordApprove = new Zend_Form_Element_Password('password_approve', array(
            'required'    => true,
            'label'       => 'Подтвердите пароль:',
            'validators'  => array(
				array('EqualInputs', true, array('password'))
			),
        )); 
		$this->addElement($passwordApprove); 
		
		
		$phone_mobile = new Zend_Form_Element_Text('phone_mobile', array(
            'required'    => true,
            'label'       => 'Мобильный телефон:',
            'maxlength'   => '20',
            'validators'  => array(
                array('StringLength', true, array(6, 20)),
				array('PhoneNumber', true, array(
					'pattern' => '/^((8|\+7|\+375)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,12}$/'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),
        ));	
		$this->addElement($phone_mobile); 
		
		
		$phone_static = new Zend_Form_Element_Text('phone_static', array(
            'required'    => false,
            'label'       => 'Стационарный телефон:',
            'maxlength'   => '20',
            'validators'  => array(
                array('StringLength', true, array(6, 20)),
				array('PhoneNumber', true, array(
					'pattern' => '/^((8|\+7|\+375)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,12}$/'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),
        ));	
		$this->addElement($phone_static);


		// Selection the country
        $country = new Zend_Form_Element_Select('country',array(
        		'required'    => true,
        		'label'       => 'Страна:',
        ));
        $country->addValidator(new Zend_Validate_Int(), true)
        		->addValidator(new Zend_Validate_GreaterThan(0), true)
        		->setMultiOptions(array('Выбрать'));
        $countries = new Db_Selector();
        
        foreach ($countries->getDataForSelector('countries','name','ASC') as $item)
        	$country->addMultiOption($item->id, $item->name);
        	
        $this->addElement($country);
        
		
		$skype = new Zend_Form_Element_Text('skype', array(
            'required'    => false,
            'label'       => 'Skype:',
            'maxlength'   => '20',
            'validators'  => array(
                array('StringLength', true, array(1, 20))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),
        ));
		$this->addElement($skype);  
		
		
		$icq = new Zend_Form_Element_Text('icq', array(
            'required'    => false,
            'label'       => 'Icq:',
            'maxlength'   => '20',
            'validators'  => array(
                array('Digits'),
                array('StringLength', true, array(6, 10))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),
        ));	
		$this->addElement($icq); 
          
		  
        /*$captcha = new Zend_Form_Element_Captcha('captcha', array(
            'label' => "Введите символы:",
            'captcha' => array(
                'captcha'   => 'Image', 
                'wordLen'   => 4,      
                'width'     => 260,     
                'timeout'   => 120,    
                'expiration'=> 300, 
                'font'      => Zend_Registry::get('config')->path->rootPublic . 'fonts/arial.ttf', // Путь к шрифту
                'imgDir'    => Zend_Registry::get('config')->path->rootPublic . 'images/captcha/', // Путь к изобр.
                'imgUrl'    => Zend_Registry::get('config')->url->base. '/images/captcha/', // Адрес папки с изображениями
                'gcFreq'    => 5 
            ),
        ));           
        $this->addElement($captcha);*/
		
		$element = new Recaptcha_Form_Element_ReCaptcha('recaptcha_response_field');
		$element->addValidator(new Recaptcha_Validate_ReCaptcha(@$_POST['recaptcha_challenge_field']))
		->setRequired(true);
		$this->addElement($element);
		
        
        $validatorNotEmpty = new Zend_Validate_NotEmpty();
        $validatorNotEmpty->setMessages(array( 
            Zend_Validate_NotEmpty::IS_EMPTY  => 'agreeRules'));
		
		
        $agreeRules = new Zend_Form_Element_Checkbox('agreeRules', array(
            'required'    => true,
            'label'       => 'С правилами системы ознакомлен(-а)',
            'filters'     => array('Int'),
            'validators'  => array($validatorNotEmpty),
        ));   
        $this->addElement($agreeRules);   
		   
		
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label'       => 'Зарегистрироваться',
        ));
        $submit->setDecorators(array('ViewHelper'));         
        $this->addElement($submit);
        
		
        $reset = new Zend_Form_Element_Reset('reset', array(
            'label'       => 'Очистить',
        ));
        $reset->setDecorators(array('ViewHelper'));
        $this->addElement($reset);


        $this->addDisplayGroup(
            array('nickname','email', 'password', 'password_approve','country','phone_mobile','phone_static','skype','icq','agreeRules','recaptcha_response_field','submit','reset'), 'authDataGroup',
            array(
                'legend' => 'Авторизационные данные'
            )
        );      
        
    }
}