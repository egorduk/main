<?php


class Form_Author_Settings extends App_Form
{

    public function init()
    {
        parent::init();
 
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'author_settings'));
        
        $this->setMethod('post');
        
       	$this->setName('formAuthorSettings');
        
        $this->setAttrib('class', 'formAuthorSettings');

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
            'required'    => true,
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
		
        $lastname = new Zend_Form_Element_Text('lastname', array(
            'required'    => true,
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
            'required'    => true,
            'label'       => 'Отчество:',
            'maxlength'   => '30',
            'validators'  => array(
                array('StringLength', true, array(2, 20)),
				array('Regex', true, array('pattern' => '/^[А-Яа-яЁё]+$/u'))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),	
        ));
		$this->addElement($surname);
		
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
		
		
		// Selection the payment
		$payment_name = new Zend_Form_Element_Select('payment_name',array(
		 	'required'    => false,
            'label'       => 'Название кошелька:',
		));				
		$payment_name//->addValidator(new Zend_Validate_Int(), false)
             		 //->addValidator(new Zend_Validate_GreaterThan(0), false)
		     		 ->setMultiOptions(array('Выбрать'));		
		
		$payments = new Db_Selector();
		
		foreach ($payments->getDataForSelector('payments','name','ASC') as $item) 
    		$payment_name->addMultiOption($item->id, $item->name);
			
		$this->addElement($payment_name);
		
		
		$payment_num = new Zend_Form_Element_Text('payment_num', array(
            'required'    => false,
            'label'       => 'Номер кошелька:',
            'maxlength'   => '15',
            'validators'  => array(
                array('StringLength', true, array(3, 15))
             ),
            'filters'     => array(
				array('StringTrim'),
				array('StripTags'),
			),
        ));
		$this->addElement($payment_num); 

        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label'       => 'Сохранить',
        ));
        $submit->setDecorators(array('ViewHelper'));           
        $this->addElement($submit);
        
   
        $reset = new Zend_Form_Element_Reset('reset', array(
            'label'       => 'Очистить',
        ));    
        $reset->setDecorators(array('ViewHelper'));
        $this->addElement($reset);
        
        
        $save_payment = new Zend_Form_Element_Button('save_payment', array(
        		'label'       => 'Save',
        ));
        $save_payment->setDecorators(array('ViewHelper'));
        $this->addElement($save_payment);
		

        $this->addDisplayGroup(
            array('id','email','name','lastname','surname',), 'userFioGroup',
            array(
                'legend' => 'Данные пользователя'
            )
        );
		
		$this->addDisplayGroup(
            array('phone_mobile','phone_static','skype','icq'), 'userContactGroup',
            array(
                'legend' => 'Контактные данные'
            )
        );  
		
		$this->addDisplayGroup(
            array('payment_name','payment_num'), 'userPaymentGroup',
            array(
                'legend' => 'Данные о платежных системах'
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