<?php

/**
 * App_Validate_PhoneNumber
 * 
 * Checks author's phone number
 * 
 */
class App_Validate_PhoneNumber extends Zend_Validate_Abstract 
{

    const INVALID_PHONE_NUMBER = '';

    protected $_messageTemplates = array(

        self::INVALID_PHONE_NUMBER => "Введенное значение не соответствует требуемому формату"

    );
  
	protected $_pattern;

    /**
     * Sets validator options
     *
     * @param  string $pattern
     * @return void
     */
    public function __construct($pattern)
    {
        $this->setPattern($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * Sets the pattern option
     *
     * @param  string $pattern
     * @return Zend_Validate_Regex Provides a fluent interface
     */
    public function setPattern($pattern)
    {
        $this->_pattern = (string) $pattern;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  string $value
     * @throws Zend_Validate_Exception if there is a fatal error in pattern matching
     * @return boolean
     */
	public function isValid($value)
    {
    	$valueString = (string)$value;

        $this->_setValue($valueString);

        $status = @preg_match($this->_pattern, $valueString);
		
        if (false === $status) 
		{
            require_once 'Zend/Validate/Exception.php';
           
		    throw new Zend_Validate_Exception("Internal error matching pattern '$this->_pattern' against value '$valueString'");
        }
		
        if (!$status) 
		{
            $this->_error();
			
            return false;
        }
		
        return true;
    }

}

