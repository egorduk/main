<?php

/**
 * Db_Selector
 * 
 */
class Db_Selector extends Zend_Db_Table_Abstract 
{
    /**
     * Name of the table
     * @var string 
     */  
    protected $_name = null;

    /**
     * Gets data from db for selector
	 * 
	 * @param string $name - the name of the table
	 * @param string $order - the field for order
	 * @param string $param - the method for order
     */
   	public function getDataForSelector($name,$order,$param) 
    {
		$this->_name = $name;
		$select = $this->select();
    	$select->order($order.' '.$param);
		
    	return $this->fetchAll($select);
    }
	
	

	
	
	
}