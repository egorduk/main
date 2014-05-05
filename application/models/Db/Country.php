<?php

/**
 * Db_Country
 * 
 */
class Db_Country extends Zend_Db_Table_Abstract 
{
    /**
     * Имя таблицы
     * @var string 
     */  
    protected $_name = 'countries';

    /**
     * Внести пользователя в базу данных
     *
     * @return true
     */
   	public function getCountries() 
    {
		$select = $this->select();
    	$select->order('name ASC');
		
    	return $this->fetchAll($select);
    }
	
	

	
	
	
}