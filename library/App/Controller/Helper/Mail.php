<?php

/**
 * App_Controller_Helper_Mail
 * 
 * 
 */
class App_Controller_Helper_Mail
{
    public static function Send($email,$subject,$body)
	{
		//Prepare email
		$mail = new Zend_Mail('UTF-8');	
		$mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
		$mail->addTo($email);	
		$mail->setSubject($subject);		
		$mail->setBodyText($body);	
		$mail->setFrom(Zend_Registry::get('config')->mail->from_mail, Zend_Registry::get('config')->mail->from_name);
		
		$sent = true;
				
		try 
		{
			$mail->send();
		} 
		catch (Exception $e)
		{
			$sent = false;
		}

		return $sent;
	}
}