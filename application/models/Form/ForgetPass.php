<?php

/**
 * Form_ForgetPass
 * 
 */
class Form_ForgetPass extends App_Form
{
	public function init()
	{
        parent::init();
		
        $helperUrl = new Zend_View_Helper_Url();
        $this->setAction($helperUrl->url(array(), 'auth_forget_pass'));
        
        $this->setMethod('post');
		
        $email = new App_Form_Element_Email('email', array(
            'required' => true,
			'validators' => array(
	 			array('DbRecordNoExistsDouble',true, array('clients','authors','email')),
				//array('DbRecordNoExists',true, array('authors', 'email')),
			),
        ));
        $this->addElement($email);
		
		$this->addElement('submit', 'rescuePassword', array(
			'required' => true,
			'ignore' => true,
			'label' => 'Восстановить пароль',
	 	));			
	}	
	
				
}