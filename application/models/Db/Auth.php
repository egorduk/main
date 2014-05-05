<?php


class Db_Auth extends Zend_Db_Table_Abstract 
{
		
	/**
	* Gets authentication adapter and checks credentials
	**/
	public function processAuth($values)
	{
		$authAdapter = $this->getAuthAdapter($values);
		$authAdapter->setIdentity($values['email']);
		$authAdapter->setCredential($values['password']);

		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($authAdapter);
		
		if ($result->isValid()) 
		{
			//$namespace = new Zend_Session_Namespace('Zend_Auth');
			//Cookies lifetime
			//$namespace->setExpirationSeconds(100);
						
			$user = $authAdapter->getResultRowObject();
			$auth->getStorage()->write($user);
			
			return true;
		}
		
		return false;
	}
	
	/**
	* Uses auth adapter
	*/
	protected function getAuthAdapter($values) 
	{
		// Selects default adapter
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();		
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$authAdapter->setIdentityColumn('email')
					->setCredentialColumn('password')
					->setCredentialTreatment('MD5(?)');

		$db = Zend_Registry::get('db');
		
		$row = $db->fetchRow($db->select()
				  ->from('author',array('id'))
				  ->where('email = ?', $values['email']));
		
		if($row)
		{
			$authAdapter->setTableName('author');
		}
		else 
		{
			$row = $db->fetchRow($db->select()
					  ->from('client',array('id'))
					  ->where('email = ?', $values['email']));
			if ($row)
			{
				$authAdapter->setTableName('client');
			}
			else 
			{
				$authAdapter->setTableName('temp_login');
			}
		}
		
		return $authAdapter;
	}
	
	
	public function processAuthOpenId($email)
	{
		$type = 0;
			
		$db = Zend_Registry::get('db');
		
		$row = $db->fetchRow($db->select()
				  ->from('author',array('email','role_id','nickname'))
				  ->where('email = ?', $email));
		
		if($row)
		{
			$auth = Zend_Auth::getInstance();
			$auth->getStorage()->write($row);
			$type = 3;
			
			return $type;
		}
		else 
		{
			$row = $db->fetchRow($db->select()
					  ->from('client',array('email','role_id','nickname'))
					  ->where('email = ?', $email));
			
			if($row)
			{
				$auth = Zend_Auth::getInstance();
				$auth->getStorage()->write($row);
				$type = 2;
				
				return $type;
			}
			else 
				return $type;			
		}
			
	}
	
	
	/**
	* Rescues user's password 
	*/
	public function rescuePassword($email)
	{		
		//Generate new password
		$newPassword = uniqid();
		
		$db = Zend_Registry::get('db');
		
		$rowClients = $db->fetchRow($db->select()
						->from('client',array())
						->where('email = ?', $email));
		
		$rowAuthors = $db->fetchRow($db->select()
						->from('author',array())
						->where('email = ?', $email));
		
		if(!$rowClients && !$rowAuthors)
		{
        	//throw new Zend_Exception("Сообщите администрации об ошибке и пройдите процедуру восстановления пароля заново,пожалуйста!");
        	return null;
		}
		else
		{
			$dataUpdate = array(
				'password' => md5($newPassword)
	         );	
			
			if ($rowClients != null)
			{
				$db->update('client',$dataUpdate,'id = '.$rowClients->id);
			}
			else 
			{
				$db->update('author',$dataUpdate,'id = '.$rowAuthors->id);
			}
		}
		
		$subject = 'Восстановление пароля';
		$body = "Ваш новый пароль для входа на ...".$newPassword;
		$send = App_Controller_Helper_Mail::Send($email,$subject,$body);

		return $send;
	}
	


}