<?php

/**

 * 
 * 
 */
class App_Navigation_Navigation extends Zend_Application_Bootstrap_Bootstrap
{
    
   	protected function __initNavigation()
	{
        $this->bootstrapView();
    	$view = $this->getResource('view');
    	
    	// Структура простого меню (можно вынести в XML)
    	$pages = array(
    			array(
    					'controller'	=> 'index',
    					'label'         => 'Главная страница',
    			),
    			array(
    					'controller'	=> 'users',
    					'action'        => 'index',
    					// Я обворачиваю текст в _(), чтобы потом вытянуть его парсером gettext'а
    					'label'         => 'Пользователи',
    					'pages' => array (
    							array (
    									'controller'	=> 'users',
    									'action'        => 'new',
    									'label'         => 'Добавить пользователя',
    							),
    					)
    			),
    			array (
    					'controller'	=> 'users',
    					'action'        => 'registration',
    					'label'         => 'Регистрация',
    			),
    			array (
    					'controller'	=> 'users',
    					'action'        => 'login',
    					'label'         => 'Авторизация',
    			),
    			array (
    					'controller'	=> 'users',
    					'action'        => 'logout',
    					'label'         => 'Выход',
    			)
    	);
    	
    	$container = new Zend_Navigation($pages);
    	
    	$view->menu = $container;
    	
    	return $container;
    }
}