<?php

/**
 * App_Controller_Plugin_FlashMessenger
 * 
 * Плагин, проверяет наличие сообщений о успешных результатах и в случае если они есть, создает именованый сегмент
 * для их вывода в макете
 * 
 */
class App_Controller_Plugin_NotFound extends Zend_Controller_Plugin_Abstract
{
    
   /**
    * Перехват события postDispatch
    * 
    */
  
     public function preDispatch(Zend_Controller_Request_Abstract $request) 
      { 
          $dispatcher = Zend_Controller_Front::getInstance() 
                        ->getDispatcher(); 

          if (!$dispatcher->isDispatchable($request)) 
          { 

			$request->setControllerName($dispatcher->getDefaultController()) 
                      ->setActionName('noroute') 
                      ->setDispatched(false); 
          } 
      }  
	
}