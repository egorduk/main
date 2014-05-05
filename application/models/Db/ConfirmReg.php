<?php

class Db_ConfirmReg extends Zend_Db_Table_Abstract 
{
	
	public function confirmRegistry($uniq_code,$hash_code,$type)
	{
		if ($type == 'client')
		{
			$this->_name = 'clients';
		
			$row = $this->fetchRow($this->select()
					->where('code_confirm = ?', $uniq_code));
			
			if(!$row)
			{
				throw new Zend_Exception("Сообщите администрации об ошибке и пройдите регистрацию заново,пожалуйста!");
			}
			else 
				parent::update(array('role_id' => 2),'id = '.$row['id']);
		}
		else if ($type == 'author')
		{
			$this->_name = 'authors';
			
			$row = $this->fetchRow($this->select()
					->where('code_confirm = ?', $uniq_code));
				
			if(!$row)
			{
				throw new Zend_Exception("Сообщите администрации об ошибке и пройдите регистрацию заново,пожалуйста!");
			}
			else
				parent::update(array('role_id' => 3),'id = '.$row['id']);
		}		
	}

}