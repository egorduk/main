<?php


class App_Validate_DbRecordNoExistsDouble extends Zend_Validate_Abstract 
{

    /**
     * Метка ошибки
     * @var const 
     */    
    const RECORD_NO_EXISTS_DOUBLE = 'dbRecordNoExistsDouble';
    
    /**
     * Текст ошибки
     * @var array 
     */
    protected $_messageTemplates = array(
        self::RECORD_NO_EXISTS_DOUBLE => 'Данная электронная почта не используется'
    );

    /**
     * Имя таблица в которой будет происходить поиск записи
     * @var string
     */    
    protected $_tableOne = null;    
    protected $_tableTwo = null;
    
    /**
     * Имя поля по которому будет происходить поиск значения 
     * @var string
     */    
    protected $_field = null;    

    /**
     * Используемый адаптер базы данных
     *
     * @var object
     */    
    protected $_adapter = null;    
       
    /**
     * Конструктор
     * 
     * @param string $table Имя таблицы
     * @param string $field Имя поля
     * @param Zend_Db_Adapter_Abstract $adapter Адаптер базы данных
     */
    public function __construct($tableOne, $tableTwo, $field, Zend_Db_Adapter_Abstract $adapter = null)
    {
        $this->_tableOne = $tableOne;
        $this->_tableTwo = $tableTwo;
        $this->_field = $field;
        
        if ($adapter == null) {
        	// Если адаптер не задан, пробуем подключить адаптер заданный по умолчанию для Zend_Db_Table
        	$adapter = Zend_Db_Table::getDefaultAdapter();
        	
        	// Если адаптер по умолчанию не задан выбрасываем исключение
        	if ($adapter == null) {
        	   throw new Exception('No default adapter was found');
        	}
        }
        
        $this->_adapter = $adapter;
    }
    
    /**
     * Проверка
     * 
     * @param string $value значение которое поддается валидации
     */
    public function isValid($value) 
    {
        $this->_setValue($value);
        
        $adapter = $this->_adapter;
        
        $selectOne = $adapter->select()
            				->from($this->_tableOne)
            				->where($adapter->quoteIdentifier($this->_field) . ' = ?', $value)
            				->limit(1);	
        $stmt = $adapter->query($selectOne);
        $resultOne = $stmt->fetch();
        
        $selectTwo = $adapter->select()
        					->from($this->_tableTwo)
        					->where($adapter->quoteIdentifier($this->_field) . ' = ?', $value)
        					->limit(1);
        $stmt = $adapter->query($selectTwo);
        $resultTwo = $stmt->fetch();
        
        if ($resultOne || $resultTwo) 
		{
            return true;
        }
        
        $this->_error();	
		
        return false;

    }

}