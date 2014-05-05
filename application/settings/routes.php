<?php

/**
 * Файл формирования маршрутов. Происходит инициализация объекта маршрутизации и задание правил маршрутизации
 **/

$router = new Zend_Controller_Router_Rewrite();

/*$router->addRoute('articles',
     new Zend_Controller_Router_Route('articles/:articleId', array('controller' => 'articles', 'action' => 'view'))
);

$router->addRoute('pages',
     new Zend_Controller_Router_Route('pages/:pageId', array('controller' => 'index', 'action' => 'page'))
);*/

$router->addRoute('index',
		new Zend_Controller_Router_Route_Static('index', array('controller' => 'index', 'action' => 'index'))
);



/*AuthController*/
$router->addRoute('auth_client_reg',
     new Zend_Controller_Router_Route_Static('reg/client', array('controller' => 'auth', 'action' => 'regclient'))
);

$router->addRoute('auth_author_reg',
     new Zend_Controller_Router_Route_Static('reg/author', array('controller' => 'auth', 'action' => 'regauthor'))
);

$router->addRoute('auth_forget_pass',
     new Zend_Controller_Router_Route_Static('forgetpass', array('controller' => 'auth', 'action' => 'forgetpass'))
);

$router->addRoute('logout',
		new Zend_Controller_Router_Route_Static('logout', array('controller' => 'auth', 'action' => 'logout'))
);

$router->addRoute('confirmreg',
     new Zend_Controller_Router_Route_Static('auth/confirmreg', array('controller' => 'auth', 'action' => 'confirmreg'))
);

$router->addRoute('auth_login',
		new Zend_Controller_Router_Route_Static('login', array('controller' => 'auth', 'action' => 'login'))
);

$router->addRoute('deny_auth_login',
		new Zend_Controller_Router_Route_Static('auth/login', array('controller' => 'no', 'action' => 'no'))
);




/*IndexController*/
$router->addRoute('index_client',
     new Zend_Controller_Router_Route_Static('index_client', array('controller' => 'index', 'action' => 'index'))
);

$router->addRoute('index_author',
     new Zend_Controller_Router_Route_Static('index_author', array('controller' => 'index', 'action' => 'index'))
);

$router->addRoute('client_rules',
		new Zend_Controller_Router_Route('index/:type_rules', array('controller' => 'index', 'action' => 'rules'))
);

$router->addRoute('author_rules',
		new Zend_Controller_Router_Route('index/:type_rules', array('controller' => 'index', 'action' => 'rules'))
);



/*ClientController*/
$router->addRoute('client_settings',
     new Zend_Controller_Router_Route_Static('client/settings', array('controller' => 'client', 'action' => 'settings'))
);



/*AuthorController*/
$router->addRoute('author_settings',
		new Zend_Controller_Router_Route_Static('author/settings', array('controller' => 'author', 'action' => 'settings'))
);

$router->addRoute('orders',
		new Zend_Controller_Router_Route('author/orders', array('controller' => 'author', 'action' => 'orders'))
);

$router->addRoute('author_info',
		new Zend_Controller_Router_Route('author/info/:authorId', array('controller' => 'author', 'action' => 'info'))
);

$router->addRoute('view_client',
		new Zend_Controller_Router_Route('client/info/:clientId', array('controller' => 'author', 'action' => 'viewclient'))
);

$router->addRoute('order',
		new Zend_Controller_Router_Route('author/order/:orderId', array('controller' => 'author', 'action' => 'order'))
);



