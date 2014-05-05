<?php


class Form_Client_Settings extends App_Form
{

    public function init()
    {
        parent::init();
           
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'client_settings'));
        
        $this->setMethod('post');
        
        $this->setName('formClientSettings');
        
        $this->setAttrib('class', 'formClientSettings');

        $id = new Zend_Form_Element_Text('id', array(
        		'label'       => 'Ваш ID:',
        		'maxlength'   => '30',
        		'attribs' => array('readonly' => 'true')
        ));
        $this->addElement($id);
        
        
        $email = new Zend_Form_Element_Text('email', array(
        		'label'       => 'Ваша почта:',
        		'maxlength'   => '30',
        		'attribs' => array('readonly' => 'true')
        ));
        $this->addElement($email);
        
        
        $name = new Zend_Form_Element_Text('name', array(
            'required'    => false,
            'label'       => 'Имя:',
            'maxlength'   => '30',
            'validators'  => array(
                array('StringLength', true, array(2, 15)),
				array('Regex', true, array('pattern' => '/^[А-Яа-яЁё]+$/u'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),	
        ));
        $this->addElement($name); 
      
        //$name->setName('name');
		
        $lastname = new Zend_Form_Element_Text('lastname', array(
            'required'    => false,
            'label'       => 'Фамилия:',
            'maxlength'   => '30',
            'validators'  => array(
                array('StringLength', true, array(2, 15)),
				array('Regex', true, array('pattern' => '/^[А-Яа-яЁё]+$/u'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),	
        ));
        $this->addElement($lastname);
		
		$surname = new Zend_Form_Element_Text('surname', array(
            'required'    => false,
            'label'       => 'Отчество:',
            'maxlength'   => '30',
            'validators'  => array(
                array('StringLength', true, array(2, 15)),
				array('Regex', true, array('pattern' => '/^[А-Яа-яЁё]+$/u'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),	
        ));
		$this->addElement($surname);
		
		
		$phone_mobile = new Zend_Form_Element_Text('phone_mobile', array(
            'required'    => false,
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

		
		// Selection the country
		$country = new Zend_Form_Element_Select('country',array(
		 	'required'    => false,
            'label'       => 'Страна:',
		));				
		$country->setMultiOptions(array('Выбрать'));		
		
		$countries = new Db_Selector();
		
		foreach ($countries->getDataForSelector('countries','name','ASC') as $item) 
    		$country->addMultiOption($item->id, $item->name);
			
		$this->addElement($country);
		
			
		$skype = new Zend_Form_Element_Text('skype', array(
            'required'    => false,
            'label'       => 'Skype:',
            'maxlength'   => '20',
            'validators'  => array(
                array('StringLength', true, array(2, 20))
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
		
		
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label'       => 'Сохранить',
        ));
        
        $submit->setDecorators(array('ViewHelper'));
               
        $this->addElement($submit);
        
        $reset = new Zend_Form_Element_Reset('reset', array(
            'label'       => 'Очистить',
        ));
        
        // Перезаписываем декораторы, что-бы выставить две кнопки в ряд
        $reset->setDecorators(array('ViewHelper'));
		
        $this->addElement($reset);
		

        $this->addDisplayGroup(
            array('id','email','name','lastname','surname',), 'userFioGroup',
            array(
                'legend' => 'Данные пользователя'
            )
        );
		
		$this->addDisplayGroup(
            array('phone_mobile','skype','icq'), 'userContactGroup',
            array(
                'legend' => 'Контактные данные'
            )
        );  
			
		$this->addDisplayGroup(
            array('country','city'), 'userAddressGroup',
            array(
                'legend' => 'Адресные данные'
            )
        );     
        
    }
}