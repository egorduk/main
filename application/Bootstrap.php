<?php

require 'Zend/Loader.php';

/**
 * Kernel
 * 
 * Главный системный класс, используется для настройки и запуска приложения
 */
class Bootstrap 
{
    /**
     * Объект конфигурации
     *
     * @var Zend_Config
     */
    private $_config = null;

	/**
	* Object of acl
	* 
	* @var Zend_Acl
	* 
	*/
	private $_myAcl = null;

    /**
     * Запуск приложения
     *
     * @var array $config Конфигурация
     */
    public function run($config) 
    {
    	try 
		{
            // Настройка загрузчика
            $this->setLoader();
                        
            // Настройка конфигурации
            $this->setConfig($config);

            // Настройка Вида
           	$this->setView();
		   
		   	//Setup ACL
			$this->setAcl();
            
            // Подключение к базе данных
            $this->setDbAdapter();
			
			// Setup mail
			$this->setMail();
			
			//$this->setNavigation();

            // Подключение маршрутизации
            $router = $this->setRouter();
			       
			 // Создание объекта front контроллера 
            $front = Zend_Controller_Front::getInstance();
			
            // Настройка front контроллера, указание базового URL, правил маршрутизации 
            $front->setBaseUrl($this->_config->url->base)
                  ->throwexceptions(true) //false for error
                  ->setRouter($router)
				  ->registerPlugin(new App_Controller_Plugin_Acl())
				 // ->registerPlugin(new App_Controller_Plugin_FlashMessenger())
				  //->registerPlugin(new Zend_Controller_Plugin_ErrorHandler())
				  ;
            
			//$front->setDefaultControllerName('error404');
			
          //  $a = new App_Navigation_Navigation();
           // $a->start();
			
            // Запуск приложения, в качестве параметра передаем путь к папке с контроллерами
            Zend_Controller_Front::run($this->_config->path->controllers);

        } 
        /*catch (Exception $e) {
            // Перехват исключений 
        	//App_Error::catchException($e);
			//new Zend_Controller_Action_Exception('404 Page not found',404);
        }*/
		catch (Zend_Exception $e) 
		{ 
    		//header( 'HTTP/1.0 404 Not Found' );
			//echo "<div style='float:center; width:1000px; margin:0 auto;'><img src='http://www.foundco.com/images/404.jpg' alt='Everything is gonna be fine, please do not panic!' /></div>";
			App_Error::catchException($e);
		}
		
    }	
	
	
    /**
     * Настройка загрузчика
     */
    public function setLoader() 
    {
        // Запуск автозагрузки
        Zend_Loader::registerAutoload();
    }
    
    
    public function setNavigation()
    {
    	$pages = array(
    			array(
    					'label'      => 'Main page',
    					'controller' => 'index',
    					'action'     => 'index',
    					'order'      => -100 // make sure home is the first page
    			),
    			array(
    					'label'      => 'Registry',
    					'controller' => 'auth',
    					'action'     => 'login',
    					'pages'      => array(
    							array(
    									'label'      => 'Foo Server',
    									'module'     => 'products',
    									'controller' => 'server',
    									'action'     => 'index',
    									'pages'      => array(
    											array(
    													'label'      => 'FAQ',
    													'module'     => 'products',
    													'controller' => 'server',
    													'action'     => 'faq',
    													'rel'        => array(
    															'canonical' => 'http://www.example.com/?page=faq',
    															'alternate' => array(
    																	'module'     => 'products',
    																	'controller' => 'server',
    																	'action'     => 'faq',
    																	'params'     => array('format' => 'xml')
    															)
    													)
    											),
    									)
    							),
    					)
    			),
    			array(
    					'label'      => 'Administration',
    					'module'     => 'admin',
    					'controller' => 'index',
    					'action'     => 'index',
    					'resource'   => 'mvc:admin', // resource
    					'pages'      => array(
    							array(
    									'label'      => 'Write new article',
    									'module'     => 'admin',
    									'controller' => 'post',
    									'aciton'     => 'write'
    							)
    					)
    			)
    	);
    	
    	// Create container from array
    	$container = new Zend_Navigation($pages);
    	
    	// Получение объекта Zend_Layout
    	$layout = Zend_Layout::getMvcInstance()->getView();
    	// Инициализация объекта Zend_View
    	//$view = $layout->getView();
    	
    	/*$this->bootstrap('layout');
    	$layout = $this->getResource('layout');
    	$view = $layout->getView();
    	// Store the container in the proxy helper:*/
    	$layout->navigation($container);
    	
    }

    
	/**
	* Setup Mail
	*/
	public function setMail()
	{
		try 
		{
	        $config = array(
	            'auth' => 'login',
	            'username' => 'egorduk91@gmail.com',
	            'password' => 'rezistor',
	            'ssl' => 'tls',
	            'port' => 587
	        );

	        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
	        Zend_Mail::setDefaultTransport($mailTransport);
    	} 
		catch (Zend_Exception $e)
		{
        //Do something with exception
    	}
	}
	
	/**
     * Setup ACL
     */
	public function setAcl()
    {
		$helper = new App_Controller_Helper_Acl();
		$helper->setRoles();
		$helper->setResources();
		$helper->setPrivilages();
		$helper->setAcl();  
    } 
	
	/**
     * Настройка конфигурации
     * 
     * @param array $config Настройки
     */
    public function setConfig($config)
    {
        $config = new Zend_Config($config);
        $this->_config = $config;
        Zend_Registry::set('config', $config);
    } 
    
    /**
     * Настройка вида
     */    
    public function setView() 
    {
        // Инициализация Zend_Layout, настройка пути к макетам, а также имени главного макета.
        // Параметр layout указан лишь для примера, по умолчанию имя макета именно "layout"
        Zend_Layout::startMvc(array(
            'layoutPath' => $this->_config->path->layouts,
            'layout' => 'index',
        ));

        // Получение объекта Zend_Layout
        $layout = Zend_Layout::getMvcInstance();

        // Инициализация объекта Zend_View
        $view = $layout->getView();

        // Настройка расширения макетов
        $layout->setViewSuffix('tpl');

        // Задание базового URL
        $view->baseUrl = $this->_config->url->base;

        // Задание пути для view части
        $view->setBasePath($this->_config->path->views);

        // Установка объекта Zend_View
        $layout->setView($view);		
        
        // Настройка расширения view скриптов с помощью Action помошников
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer
            ->setView($view)
            ->setViewSuffix('phtml');

        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
					   
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		$viewRenderer->view->doctype('XHTML1_STRICT'); 
    }
            
    /**
     * Установка соединения с базой данных и помещение его объекта в реестр.
     */
    public function setDbAdapter() 
    {
        // Подключение к БД, так как Zend_Db "понимает" Zend_Config, нам достаточно передать специально сформированный объект конфигурации в метод factory
        $db = Zend_Db::factory($this->_config->db);
		
		$db->getProfiler()->setEnabled(true);
        
        // Изменяем режим извлечения данных, FETCH_OBJ - данные в виде массива объектов
        // По умолчанию стоит режим FETCH_ASSOC - массив ассоциативных массивов.
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        
        // Задание адаптера по умолчанию для наследников класса Zend_Db_Table_Abstract 
        Zend_Db_Table_Abstract::setDefaultAdapter($db);    

        // Занесение объекта соединения c БД в реестр
        Zend_Registry::set('db', $db);
    }

    /**
     * Настройка маршрутов
     */
    public function setRouter() 
    {
        // Подключение файла правил маршрутизации
        require($this->_config->path->settings . 'routes.php');

        // Если переменная router не является объектом Zend_Controller_Router_Abstract, выбрасываем исключение
        if (!($router instanceof Zend_Controller_Router_Abstract)) {
            throw new Exception('Incorrect config file: routes');
        }
        
        return $router;
    }
	
    
}