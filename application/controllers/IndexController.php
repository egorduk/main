<?php

/**
 * Main controller
 */
class IndexController extends Zend_Controller_Action 
{

    public function indexAction() 
    {
		//$formIndexClient = new Form_Index_client();	
		//$this->view->formIndexClient = $formIndexClient;
		
		//$formIndexAuthor = new Form_Index_author();
		//$this->view->formIndexAuthor = $formIndexAuthor;
		
    }

	
	public function rulesAction() 
    {
		$type_rules = $this->_getParam('type_rules');
		$this->view->type_rules = $type_rules;
	}
	
	public function menuAction()
	{
		
	}
	

}