<?php

/**
 * App_Controller_Plugin_Acl
 * 
 * 
 */
class App_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$acl = Zend_Registry::get('acl');
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
		}
		
		if (empty($identity->role_id) || ($identity->role_id == 1))
		{
			$role = 'guest';
		}
		else if ($identity->role_id == 2)
		{
			$role = 'client';
		}
		else if ($identity->role_id == 3)
		{
			$role = 'author';
		}

		
		$resource = $request->getControllerName();
		$action = $request->getActionName();
		
		if ($acl->has($resource)) 
		{	
			if(!$acl->isAllowed($role,$resource,$action))
			{
				$request->setControllerName('error');
				$request->setActionName('denied');
			}
		}
	}
}