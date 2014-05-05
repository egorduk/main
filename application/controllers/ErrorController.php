<?php

/**
 * ErrorController
 * 
 * Errors handler
 */
class ErrorController extends Zend_Controller_Action 
{

    /**
     * Handles error 404 or 500
     */
    public function errorAction() 
    {

        $errors = $this->_getParam('error_handler');
		
		if (!$errors || !$errors instanceof ArrayObject) 
		{
      		$this->view->message = 'You have reached the error page';
      		return;
    	}	

        switch ($errors->type) 
		{
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error - controller or action not found
            	$this->getResponse()->setHttpResponseCode(404);
        		//$priority = Zend_Log::NOTICE;
        		$this->view->error_code = $this->getResponse()->getHttpResponseCode();
        		$this->view->message = "Page Not Found";
        		$this->renderScript('error/error404.phtml');
        		break;
			   
			   	/*$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $errorMsg = 'HTTP/1.1 404 Not Found';	
				$content =<<<EOH
				<h1>Ошибка!</h1>
				<p>Запрошенная вами страница не найдена.</p>
				EOH;
                break;*/
            default:
                // application error
		        print_r($this->getResponse());
		        $this->getResponse()->setHttpResponseCode(500);
		        //$priority = Zend_Log::CRIT;
				$this->getResponse()->clearBody();
		        $this->view->error_code = $this->getResponse()->getHttpResponseCode();
		        $this->view->message = 'Application error';
		        
				/*if ($log = $this->getLog()) 
				{
		        	$log->log($this->view->message, $priority, $errors->exception);
		        	$log->log('Request Parameters', $priority, $errors->request->getParams());
		        	$this->renderScript('error/error_500.phtml');
		        }*/
				
				// Ошибка приложения
                /*$errorMsg = "System error! Please try later!";	
				$content =<<<EOH
				<h1>Ошибка!</h1>
				<p>Произошла непредвиденная ошибка. Пожалуйста, попробуйте позднее.</p>
				EOH;	
                break;*/	
				
				// conditionally display exceptions
		       	/*if ($this->getInvokeArg('displayExceptions') == true) 
				{
		        	$this->view->exception = $errors->exception;
		        }*/

		        $this->view->request = $errors->request;
		        $this->view->error_code = $this->getResponse()->getHttpResponseCode();
		       // $this->renderScript('error/error_500.phtml');
			   	$this->renderScript('error/error404.phtml');
		        break;
		}
				
        /*}

        // Удаление добавленного ранее содержимого
        $this->getResponse()->clearBody();

        $this->view->content = $content;

    }

}*/
		// Log exception, if logger available
	   /* if ($log = $this->getLog()) 
		{
	    	$log->log($this->view->message, $priority, $errors->exception);
	    	$log->log('Request Parameters', $priority, $errors->request->getParams());
	    }

	    // conditionally display exceptions
	    if ($this->getInvokeArg('displayExceptions') == true) 
		{
	    	$this->view->exception = $errors->exception;
	    }*/

	   // $this->view->request = $errors->request;
	}
	
	
	public function deniedAction()
	{
    	
	}
	
	

	/*public function getLog() 
	{
	  	$bootstrap = $this->getInvokeArg('bootstrap');
	    
		if (!$bootstrap->hasResource('Log')) 
		{
	      return false;
	    }
		
	    $log = $bootstrap->getResource('Log');
		
	    return $log;
	}*/
	
}
