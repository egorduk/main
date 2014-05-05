<?php

/**
 * Works with auth,sessions and etc
 **/
class AuthController extends Zend_Controller_Action 
{
	
	public function indexAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost()) 
		{
			$dataPost = $this->_request->getPost();
			
			if (isset($dataPost['clientRegistry']))
			{			
				$this->_helper->redirector('regclient');
			}			
			else if ((isset($dataPost['authorRegistry'])))	
			{		
				$this->_helper->redirector('regauthor');
			}
		}
	}
	 
	public function loginAction()
	{	
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
	        $auth = Zend_Auth::getInstance();					
			$identity = $auth->getStorage()->read();
			if ($identity->role_id == 2)		
				$this->_helper->redirector('index', 'client');
			else 
				$this->_helper->redirector('index', 'author');
	    }
		
		$formLogin = new Form_Auth_Login();

		$request = $this->getRequest();
		$data = $this->_request->getPost();
		
		if ($request->isPost() && (!isset($data['clientLogin'])) && (!isset($data['authorLogin']))) 
		{
			if (isset($data['token']))
			{
				$obj = new Db_Auth();
				
				$token = file_get_contents('http://ulogin.ru/token.php?token='.$data['token'].'&host='.Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_HOST'));
				$userArr = Zend_Json::decode($token);
				
				$type = $obj->processAuthOpenId($userArr['email']);
				if ($type == 2)
				{
					$this->_helper->redirector('index', 'client');
				}
				elseif ($type == 3)
				{ 
					$this->_helper->redirector('index', 'author');
				}
				else 
				{
					$this->view->msg = 'В системе нету пользователя с таким Email';
				}
			}	
			elseif ($formLogin->isValid($request->getPost())) 
			{
        		$obj = new Db_Auth();
				
				$this->view->msg = 'Неверный Email либо пароль';
				
				if ($obj->processAuth($formLogin->getValues())) 
				{
					/*$session = new Zend_Session_Namespace('user_data'); 
					$session->userlogin = '';
					$session->userpass = '';
					$session->usernick = '';*/
					
					$auth = Zend_Auth::getInstance();					
					$identity = $auth->getStorage()->read();
					if ($identity->role_id == 2)		
						$this->_helper->redirector('index', 'client');
					else 
						$this->_helper->redirector('index', 'author');
				}			
			}
		}
		
		$this->view->form = $formLogin;
	}
	
	
	
	/**
	* Action for register client
	* 
	*/
	public function regclientAction()
	{	
		$formClientRegistry = new Form_Client_Registry();
		$this->view->formRegistry = $formClientRegistry;	

		$request = $this->getRequest();	
		if ($request->isPost()) 
		{
			if ($formClientRegistry->isValid($request->getPost())) 
			{
            	$obj = new Client();
				
				$clientData = array(
                    'nickname'      => $formClientRegistry->getValue('nickname'),
					'email'         => $formClientRegistry->getValue('email'),
                    'password'      => md5($formClientRegistry->getValue('password')),
                );
                
               	if ($obj->regNewClient($clientData))
				{
					$this->view->formRegistry = '';
					$this->view->message = 'Проверьте вашу электронную почту для подтверждения регистрации!';
				}						
					    		   
			    // View successful message
               	//$this->_helper->FlashMessenger->setNamespace('messages')->addMessage('Поздравляем с успешной регистрацией');
                // Redirect
               	//$this->_helper->redirector->gotoRoute(array(), 'default');
			}
		}
	}
	
	/**
	* Registers author
	* 
	*/
	public function regauthorAction()
	{
		$formAuthorRegistry = new Form_Author_Registry();
			
		$this->view->formRegistry = $formAuthorRegistry;	
		
		$request = $this->getRequest();
		
		if ($request->isPost()) 
		{
			if ($formAuthorRegistry->isValid($request->getPost())) 
			{
            	$dbUser = new Author();
				
				$userData = array(
                    'nickname'      => $formAuthorRegistry->getValue('nickname'),
					'email'         => $formAuthorRegistry->getValue('email'),
                    'password'      => md5($formAuthorRegistry->getValue('password'))
                );
				
				$infoData = array(
					'phone_mobile'	=> $formAuthorRegistry->getValue('phone_mobile'),	
					'country_id'	=> $formAuthorRegistry->getValue('country'),
				);
				   
               	if ($dbUser->regNewAuthor($userData,$infoData))
				{
					$this->view->formRegistry = '';
					$this->view->message = 'Проверьте вашу электронную почту для подтверждения регистрации!';
				}		
			}
		}
	}
	
	/**
	* Rescues forgotten user's password
	* 
	*/
	public function forgetpassAction()
	{
		$formRescue = new Form_ForgetPass();
		
		$request = $this->getRequest();
		
		$this->view->formRescue = $formRescue;
		
		if ($request->isPost()) 
		{
			if ($formRescue->isValid($request->getPost())) 
			{
				$obj = new Db_Auth();
				
				$email = $formRescue->getValue('email');
				
				$result = $obj->rescuePassword($email);
				
				if($result)
				{
					$this->view->newPass = 'Новый пароль выслан!';
					$formRescue->reset();
					//$this->_helper->redirector('index','index');
				} 
				else 
				{
					$this->view->newPass = 'Пароль не выслан!';
				}
			}		
		}	
	}
	
	/**
	* Logout
	*/
	public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index','index');
    }


	/**
	* Confirm register
	*/
	public function confirmregAction()
	{
		$uniq_code = $this->_getParam('uniq_code');
		$hash_code = $this->_getParam('hash_code');
		$type = $this->_getParam('type');
		
		if (($hash_code == Zend_Registry::get('config')->mail->hash_code) && (isset($uniq_code)) && (iconv_strlen($uniq_code) == 23) && (isset($type)))
		{
			$confirm = new Db_ConfirmReg();
			$confirm->confirmRegistry($uniq_code,$hash_code,$type);
			
			$this->view->message = 'Спасибо за регистрацию!';
		}
		else
			$this->_helper->redirector('index','index');
	}
	
	

	
    
}
