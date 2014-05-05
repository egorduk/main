<?php

class Form_Author_Bid extends App_Form
{
	public function init()
	{
        parent::init();
		 
        $this->setMethod('post');
	   	$this->setName('formPlaceBid');
        $this->setAttrib('class', 'formPlaceBid');
		
		$this->addElement('text', 'authorBid', array(
			'filters' => array(
				array('StringTrim'),
				array('StripTags'),
			),
			'validators' => array(
				array('Digits'),
		 		array('StringLength', true, array(2, 6)),
			),
			'maxlength' => 6,
			'size'	=> 6,
			'required' => true,
			'label' => 'Моя цена за работу:',
		));
		
		$this->addElement('text', 'authorDayImplement', array(
			'filters' => array(
				array('StringTrim'),
				array('StripTags'),
			),
			'validators' => array(
				array('Digits'),
				array('StringLength', true, array(1,3)),
			),
			'maxlength' => 3,
			'size'	=> 3,
			'required' => true,
			'label' => 'Выполню за сколько дней:',
		));

		$this->addElement('textarea', 'authorQuestion', array(
			'filters' => array(
				array('StringTrim'),
				array('StripTags'),
			),
			'cols' => 35,
			'rows' => 7,	
			'required' => false,
			'maxlength' => 255,
			'label' => 'Мой вопрос заказчику:',
		));	
		
		$this->addElement('hidden', 'orderId', array(
		));
		
		$this->addElement('button', 'btnAuthorConfirm', array(
			'required' => false,
			'ignore' => true,
			'label' => 'Поставить ставку',
	 	));

        $this->addDisplayGroup(
            array('authorBid','authorDayImplement','authorQuestion','btnAuthorConfirm'), 'authDataGroup',
            array(
                'legend' => 'Автор'
            )
        );
			
			
	}	
				
}