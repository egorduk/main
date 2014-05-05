<?php 

/**
 * Файл с переводами сообщений об ошибках
 * 
 */

return $errors = array (

    Zend_Validate_Alnum::NOT_ALNUM     => "Значение содержит запрещенные символы. Разрешены символы латиници, кирилици, цифры и пробел",
    Zend_Validate_Alnum::STRING_EMPTY  => "Поле не может быть пустым",
    
    Zend_Validate_Date::NOT_YYYY_MM_DD => 'Значение не соответствует формату год-месяц-день',   
    Zend_Validate_Date::INVALID        => 'Неверная дата',   
    Zend_Validate_Date::FALSEFORMAT    => 'Значение не соответствует указанному формату',   
     
    Zend_Validate_EmailAddress::INVALID            => "Неверный формат адреса электронной почты. Введите почту в формате local-part@hostname",
    Zend_Validate_EmailAddress::INVALID_HOSTNAME   => "Неверный домен для адреса электронной почты '%value%'",
    Zend_Validate_EmailAddress::INVALID_MX_RECORD  => "'%hostname%' не имеет MX-записи об адресе электронной почты '%value%'",
    Zend_Validate_EmailAddress::DOT_ATOM           => "'%localPart%' не соответствует формату dot-atom",
    Zend_Validate_EmailAddress::QUOTED_STRING      => "'%localPart%' не соответствует формату quoted-string",
    Zend_Validate_EmailAddress::INVALID_LOCAL_PART => "'%localPart%' неверное имя для адреса электронной почты '%value%'",
	Zend_Validate_EmailAddress::LENGTH_EXCEEDED	   => "Введена слишком длинная электронная почта",

    //Zend_Validate_Hostname::UNKNOWN_TLD	=>	"'%value%' представлен неизвестным доменом",
	//Zend_Validate_Hostname::LOCAL_NAME_NOT_ALLOWED => "Возможно указано локальное имя",
	Zend_Validate_Hostname::INVALID_HOSTNAME => "'%value%' указан не полностью. Например local-part@gmail.com",
	//Zend_Validate_Hostname::INVALID_LOCAL_NAME => "Возможно указано локальное имя",
	Zend_Validate_Hostname::UNDECIPHERABLE_TLD => "'%value%' указан не полностью. Например local-part@gmail.com",
	
	Zend_Validate_Digits::NOT_DIGITS => "Icq должно состоять только из цифр",
	
	Zend_Validate_Regex::NOT_MATCH => "Разрешено использовать только русские и английские буквы",
	
	Zend_Validate_Int::NOT_INT => 'Значение не является целочисленным значением',   
     
    Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым',
	
	Zend_Validate_GreaterThan::NOT_GREATER => 'Выберите правильное значение',
     
    Zend_Validate_StringLength::TOO_SHORT => 'Длина введённого значения меньше, чем %min% символов',   
    Zend_Validate_StringLength::TOO_LONG  => 'Длина введённого значения больше, чем %max% символов',   

    App_Validate_EqualInputs::NOT_EQUAL => 'Пароли не совпадают',
    
    App_Validate_Password::INVALID => 'Неверный формат пароля',
    App_Validate_Password::INVALID_LENGTH => 'Длина пароля должна быть от 6 до 30ти символов',
    
    App_Validate_DbRecordExists::RECORD_EXISTS => 'Такая электронная почта уже занята',
	App_Validate_DbRecordNoExists::RECORD_NO_EXISTS => 'Такая электронная почта не используется',
    
    Zend_Captcha_Word::BAD_CAPTCHA   => 'Вы указали неверные символы',
    Zend_Captcha_Word::MISSING_VALUE => 'Поле не может быть пустым',
    
    'agreeRules' => 'Вы должны согласиться с правилами',
    
 );
