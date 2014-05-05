<?php

/**
 * Works with clients
 */
class ClientController extends Zend_Controller_Action 
{
	
	public function init() 
	{
		//$this->_helper->AjaxContext()->addActionContext('settings', 'json')->initContext('json');
		//$this->_helper->AjaxContext()->addActionContext('settings', 'html')->initContext();
	}
	
	
	public function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
		}
		
		$this->view->nickname = $identity->nickname;
		$this->view->role = $identity->role_id;	
		$this->view->email = $identity->email;
	}

	public function settingsAction()
	{
		$formSettings = new Form_Client_Settings();
		
		$request = $this->getRequest();
		
		$obj = new Client();
		$dataReceive = $obj->populateForm();
		if (count($dataReceive) != 0)
			$formSettings->populate($dataReceive);
		
		$this->view->formSettings = $formSettings;
		
		if ($request->isPost()) 
		{
			if ($formSettings->isValid($request->getPost())) 
			{		
				//if ($request->isXmlHttpRequest())
				$infoData = array(
						'name'     		=> $formSettings->getValue('name'),
						'lastname' 		=> $formSettings->getValue('lastname'),
						'surname'  		=> $formSettings->getValue('surname'),
						'phone_mobile'	=> $formSettings->getValue('phone_mobile'),
						'skype'			=> $formSettings->getValue('skype'),
						'icq'			=> $formSettings->getValue('icq'),
				);
				
				$countryData = array(
						'country_id'	=> $formSettings->getValue('country')		
				);
				
				if ($obj->updateClientSettings($infoData,$countryData))
				{
					$this->view->formSettings = '';
					$this->view->message = 'Данные сохранены!';
				}
				else 
				{
					$this->view->formSettings = '';
					$this->view->message = 'Данные не сохранены!';
				}
			}		
		}	
	}
	
	
	public function exampleAction() 
	{
		if ($this->getRequest()->isXmlHttpRequest()) 
		{

		try 
		{
		    $curPage = $this->getRequest()->getPost('page');    
		    $rowsPerPage = $this->getRequest()->getPost('rows');
		    $sortingField = $this->getRequest()->getPost('sidx');
		    $sortingOrder = $this->getRequest()->getPost('sord');
		    
		    $dbHost = 'localhost';
		    $dbName = 'main';
		    $dbUser = 'root';
		    $dbPass = '';
		    
		    //подключаемся к базе
		    $dbh = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass);
		    //указываем, мы хотим использовать utf8
		    $dbh->exec('SET CHARACTER SET utf8');
		
			$qWhere = '';
			//определяем команду (поиск или просто запрос на вывод данных)
			//если поиск, конструируем WHERE часть запроса
			if (isset($_POST['_search']) && $_POST['_search'] == 'true') {
				$allowedFields = array('surname', 'fname', 'lname');
				$allowedOperations = array('AND', 'OR');
				
				$searchData = json_decode($_POST['filters']);
		
				//ограничение на количество условий
				if (count($searchData->rules) > 10) {
					throw new Exception('Cool hacker is here!!! :)');
				}
		
				$qWhere = ' WHERE ';
				$firstElem = true;
		
				//объединяем все полученные условия
				foreach ($searchData->rules as $rule) {
					if (!$firstElem) {
						//объединяем условия (с помощью AND или OR)
						if (in_array($searchData->groupOp, $allowedOperations)) {
							$qWhere .= ' '.$searchData->groupOp.' ';
						}
						else {
							//если получили не существующее условие - возвращаем описание ошибки
							throw new Exception('Cool hacker is here!!! :)');
						}
					}
					else {
						$firstElem = false;
					}
					
					//вставляем условия
					if (in_array($rule->field, $allowedFields)) {
						switch ($rule->op) {
							case 'eq': $qWhere .= $rule->field.' = '.$dbh->quote($rule->data); break;
							case 'ne': $qWhere .= $rule->field.' <> '.$dbh->quote($rule->data); break;
							case 'bw': $qWhere .= $rule->field.' LIKE '.$dbh->quote($rule->data.'%'); break;
							case 'cn': $qWhere .= $rule->field.' LIKE '.$dbh->quote('%'.$rule->data.'%'); break;
							default: throw new Exception('Cool hacker is here!!! :)');
						}
					}
					else {
						//если получили не существующее условие - возвращаем описание ошибки
						throw new Exception('Cool hacker is here!!! :)');
					}
				}
			}
			
		    //определяем количество записей в таблице
		    $rows = $dbh->query('SELECT COUNT(id) AS count FROM users'.$qWhere);
		    $totalRows = $rows->fetch(PDO::FETCH_ASSOC);
		
		    $firstRowIndex = $curPage * $rowsPerPage - $rowsPerPage;
		    //получаем список пользователей из базы
		    $res = $dbh->query('SELECT * FROM users '.$qWhere.' ORDER BY '.$sortingField.' '.$sortingOrder.' LIMIT '.$firstRowIndex.', '.$rowsPerPage);
			
		    //сохраняем номер текущей страницы, общее количество страниц и общее количество записей
		    $response->page = $curPage;
		    $response->total = ceil($totalRows['count'] / $rowsPerPage);
		    $response->records = $totalRows['count'];
		
		    $i=0;
		    while($row = $res->fetch(PDO::FETCH_ASSOC)) {
		        $response->rows[$i]['id']=$row['id'];
		        $response->rows[$i]['cell']=array($row['id'], $row['surname'], $row['fname'], $row['lname']);
		        $i++;
		    }
		   // echo json_encode($response);
		    $this->_helper->viewRenderer->setNoRender();
		    $this->_helper->layout->disableLayout();
		    $this->getResponse()->setHeader('Content-Type','application/json',true);
		    echo Zend_Json::encode($response);
		    //$this->_helper->json($response);
		}
		catch (Exception $e) {
		    echo json_encode(array('errMess'=>'Error: '.$e->getMessage()));
		}
			}
	}
	
    
}
