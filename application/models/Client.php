<?php

/**
 * User_User
 * 
 * Works with user
 */
class Client extends Zend_Db_Table_Abstract 
{
	
	//protected $dbAdapter = null; 

    /**
     * Registers new client 
     */
   	public function regNewClient($clientData) 
    {
		$clientData['date_registered'] = new Zend_Db_Expr("NOW()");     
		$clientData['ip_registered'] = Zend_Controller_Front::getInstance()->getRequest()->getServer('REMOTE_ADDR');	
		$clientData['browser'] = Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_USER_AGENT');	
		$clientData['role_id'] = 1;  	 
		$uniq_code = uniqid('',true);
		$clientData['code_confirm'] = $uniq_code;

		$db = Zend_Registry::get('db');	
		$db->insert('clients',$clientData);
		
		$lastIdClient = $this->getAdapter()->lastInsertId();
		
		$db->insert('info',array());
		$lastIdInfo = $this->getAdapter()->lastInsertId();				
		
		$db->update('clients',array('info_id' => $lastIdInfo),'id = '.$lastIdClient);
		
		$hash_code = Zend_Registry::get('config')->mail->hash_code;
		$email = $clientData['email'];
		$subject = 'Подтверждение регистрации на сайте ';
		$body = 'Спасибо за регистрацию '.$clientData['nickname'].'! Для подтверждения регистрации перейдите по ссылке http://localhost/main/public/auth/confirmreg?uniq_code='.$uniq_code.'&hash_code='.$hash_code.'&type=client';
		$mail = App_Controller_Helper_Mail::Send($email,$subject,$body);
			
        return true;
    }
	
	/**
	 * Updates client's settings 
	 * @param unknown $infoData
	 * @param unknown $countryData
	 * @throws Zend_Exception
	 * @return boolean
	*/
	public function updateClientSettings($infoData,$countryData)
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
		}	
		else 
			return false;
		
		$clientId = $identity->id;
		
		$db = Zend_Registry::get('db');

		$row = $db->fetchRow($db->select()
						->from('clients',array('info_id'))
						->where('id = ?', $clientId));
					
		if(!$row)
		{
			throw new Zend_Exception("Error select from clients");
		}
		
		$db->update('info',array_merge($infoData,$countryData),'id = '.$row->info_id);
			
		return true;
	}
	
	
	/**
	 * Populates form "user/settings"
	 * @throws Zend_Exception
	 * @return unknown
	 */
	public function populateForm()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$clientId = $identity->id;
			
			$db = Zend_Registry::get('db');
			$row = $db->fetchRow($db->select()
						->from('clients',array('info_id','email'))
						->where('id = ?', $clientId));
			
			if(!$row)
			{
				throw new Zend_Exception("Error select from clients");
			}
			
			$infoId = $row->info_id;
			$email = $row->email;
			
			$row = $db->fetchRow($db->select()
					->from('info')
					->where('id = ?', $infoId));
			
			if(!$row)
			{
				throw new Zend_Exception("Error select from info");
			}
			
			$arrayInfo = array('id' => $clientId,'email' => $email,'name' => $row->name,'lastname' => $row->lastname,'surname' => $row->surname,'phone_mobile' => $row->phone_mobile,'skype' => $row->skype,'icq' => $row->icq);
			
			$countryId = $row->country_id;	
			if ($countryId != null)
			{
				$row = $db->fetchRow($db->select()
						  ->from('countries')
						  ->where('id = ?', $countryId));
				
				if(!$row)
				{
					throw new Zend_Exception("Error select from countries");
				}
				
				$arrayCountry = array('country' => $row->id);
				return array_merge($arrayInfo,$arrayCountry);
			}				
		}	
		return $arrayInfo;
	}
	
	
	public function getData()
	{
		$db = Zend_Registry::get('db');
		
		$row = $db->fetchAll($db->select()
				  ->from('clients'));
		
		//$rowsetArray = $row->toArray();
		
		/*$row = $db->fetchRow($db->select()
				->from('clients')
				->where('id = ?', 81));*/
		
		return $row;
	}

	
	


}