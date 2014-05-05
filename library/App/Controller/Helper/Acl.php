<?php

/**
 * App_Controller_Helper_Acl
 * 
 * 
 */
class App_Controller_Helper_Acl
{
    public $acl;
	
	public function __construct()
	{
		$this->acl = new Zend_Acl();
	}
	
	public function setRoles()
	{
		$this->acl->addRole(new Zend_Acl_Role('guest'));
		$this->acl->addRole(new Zend_Acl_Role('client'));
		$this->acl->addRole(new Zend_Acl_Role('author'));
	}

	public function setResources()
	{
		$this->acl->add(new Zend_Acl_Resource('index'));
		$this->acl->add(new Zend_Acl_Resource('auth'));
		$this->acl->add(new Zend_Acl_Resource('client'));
		$this->acl->add(new Zend_Acl_Resource('author'));
	}

	public function setPrivilages()
	{
		//$this->acl->allow('guest',null,'view');
		$this->acl->allow('guest','index');
		//$this->acl->allow('guest','auth',array('login','regclient','regauthor'));
		$this->acl->allow('guest','auth');
		$this->acl->allow('guest','author');
		$this->acl->allow('guest','client');
		
		$this->acl->allow('author','index');
		$this->acl->allow('author','author');
		//$this->acl->deny('author','client');
		$this->acl->allow('author','auth');
		
		$this->acl->allow('client','index');
		$this->acl->allow('client','client');
		//$this->acl->deny('client','author');
		$this->acl->allow('client','auth');
		//$this->acl->allow('guest','auth',array('login'));
		//$this->acl->allow('admin');
		
		//разрешаем всем все остальное
 		//$acl->allow(null, null, null);
	}
	
	public function setAcl()
	{
		Zend_Registry::set('acl',$this->acl);
	}
}