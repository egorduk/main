<?php

/**
 * App_Controller_Plugin_Acl
 * 
 * 
 */
class App_Controller_Plugin_Red extends Zend_Controller_Plugin_Abstract
{
    
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if (! Zend_Auth::getInstance ()->hasIdentity ()) 
            if(!$this->getRequest()->isXmlHttpRequest()){ 
               //$request->setControllerName('index');
				//$request->setActionName('index');
                         } 
            else { 
               echo 'Sesison ended.Please relogin.'; 
               exit; 
         }
	}
}