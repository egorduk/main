<?php

class AuthorController extends Zend_Controller_Action 
{
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
		$formSettings = new Form_Author_Settings();
		
		$request = $this->getRequest();
		
		$obj = new Author();
		$data = $obj->populateForm();
		if (count($data) != 0)
			$formSettings->populate($data);
		
		$this->view->formSettings = $formSettings;
		
		if ($request->isPost()) 
		{
			if ($formSettings->isValid($request->getPost())) 
			{		
				$infoData = array(
						'name'     		=> $formSettings->getValue('name'),
						'lastname' 		=> $formSettings->getValue('lastname'),
						'surname'  		=> $formSettings->getValue('surname'),
						'phone_mobile'	=> $formSettings->getValue('phone_mobile'),
						'phone_static'	=> $formSettings->getValue('phone_static'),
						'skype'			=> $formSettings->getValue('skype'),
						'icq'			=> $formSettings->getValue('icq'),
				);
				
				$countryData = array(
						'country_id'	=> $formSettings->getValue('country')		
				);
				
				if ($obj->updateAuthorSettings($infoData,$countryData))
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

	
	public function populatePaymentNumAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$param = $this->_request->getParam('param');

		$obj = new Author();
		$paymentNum = $obj->getPopulatePaymentNum($param);
			
		$result = array(
					'param'	=>	$paymentNum
		);			
		
		//$this->_response->setBody(json_encode($result));
		//echo Zend_Json_Encoder::encode($someData);
		$this->_helper->json($result);	
	}
	
	
	public function updatePaymentNumAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	
		$paymentName = $this->_request->getParam('param1');
		$paymentNum = $this->_request->getParam('param2');
	
		$obj = new Author();
		$result = $obj->updatePaymentNum($paymentName,$paymentNum);
	
		echo $result;
	}

	
	public function ordersAction()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
		}
		else
		{
			throw new Zend_Exception("Error auth");
		}
		
		//$orderId = $this->_getParam('orderId');
		
		//if ($orderId != null)
		{
			//$this->_helper->redirector('author_select_order');
		}
		//else		
		{
			if ($this->getRequest()->isXmlHttpRequest()) 
			{
				try 
				{
				    $data = $this->_request->getPost();	
					$curPage = $data['page'];
					$rowsPerPage = $data['rows'];
					$sortingField = $data['sidx'];
					$sortingOrder = $data['sord'];	
					$obj = new Author();
					$sField = null;
					$sData = null;
					$sTable = null;
					$mode = null;
					
					if (isset($data['_search']) && $data['_search'] == 'true') 
					{
						$mode = $data['searchOper'];
						$sData = $data['searchString'];
						$sField = $data['searchField'];
					}
					
					$totalRows = $obj->getCountOrdersForGrid($mode,$sField,$sData);
				
					/*if ($totalRows < $rowsPerPage)
						$response->page = 1;
					else
						$response->page = $curPage;*/
					
					$firstRowIndex = $curPage * $rowsPerPage - $rowsPerPage;
					$limit = $firstRowIndex.','.$rowsPerPage;	 
					$res = $obj->getOrdersForGrid($mode,$sField,$sData,$sortingField,$sortingOrder,$limit,$authorId);	    
					  
				    $response->total = ceil($totalRows / $rowsPerPage);
				    $response->records = $totalRows;
				    $response->page = $curPage;
				
				    $i=0;
	
				    while($row = $res->fetch(PDO::FETCH_ASSOC)) 
				    {       
				    	$normalDateCreate = $obj->getNormalDate($row['date_create']);
				    	$dateCreate = $obj->getMonthFromDigit($normalDateCreate);  	
				        $normalDateExpire = $obj->getNormalDate($row['date_expire']);
				       	$dateExpire = $obj->getMonthFromDigit($normalDateExpire);
				    	
				    	if ($row['price'] > 0)
				    		$price = $row['price'] . ' руб.';
				    	else 
				    		$price = '';
				        
				    	$response->rows[$i]['id'] = $row['id'];
				        $response->rows[$i]['cell'] = array($row['id'],$row['num'],$dateCreate,$row['sname'],$row['name_theme'],$row['tname'],$dateExpire,$price);
				        $i++;
				    }
				    
				    $this->_helper->json($response);
				}
				catch (Exception $e) 
				{
					$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
				}
			}
		}
	}

	
	public function orderAction()
	{
		$orderId = $this->_getParam('orderId');	
		$formBid = new Form_Author_Bid();
		$data = array('orderId'=> $orderId);
		$formBid->populate($data);
		$this->view->formBid = $formBid;
		
		$obj = new Author();
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
		}
		
		/*$request = $this->getRequest();
		if ($request->isPost())
		{
			if ($formBid->isValid($request->getPost()))
			{
			}
		}*/
	
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$bid = $this->_request->getParam('bid');
			$day = $this->_request->getParam('day');
			$question = $this->_request->getParam('question');
			$mode = $this->_request->getParam('mode');
			$arrayCheck = array('authorBid' => $bid, 'authorDayImplement' => $day, 'authorQuestion' => $question);
						
			if (!$formBid->isValidPartial($arrayCheck) && $mode != 'loadPlaceBid') 
			{
				$messages = $formBid->getMessages();
				$messages['mode'] = 'showError';
				$this->_helper->json($messages);
			}
			else if ($formBid->isValidPartial($arrayCheck) && $mode != 'loadPlaceBid')
			{
				$orderId = $this->_request->getParam('orderId');
				
				$obj->setAuthorBid($bid, $day, $question, $authorId, $orderId);
	
				$jsonArray = array(
					'mode' => 'showPlaceBid',	
					'placePrice' => $bid . ' руб.',
					'placeDayImplement' => $day . ' дн.',
					'placeQuestion' => $question
				);
				
				$this->_helper->json($jsonArray);
			}
			else if ($mode == 'loadPlaceBid')
			{
				$orderId = $this->_request->getParam('orderId');
				
				$res = $obj->getAuthorBid($authorId, $orderId);
				
				$jsonArray = array(
						'placePrice' => $res->price . ' руб.',
						'placeDayImplement' => $res->day_implement . ' дн.',
						'placeQuestion' => $res->question
				);
				
				$this->_helper->json($jsonArray);
			}	
		}
		//else 
		{
			//$orderId = $this->_getParam('orderId');
			
			$obj = new Author();
			$pathImg = Zend_Registry::get('config')->url->img;
			$baseUrl = Zend_Registry::get('config')->url->base;
				
			$arrayInfoOrder = $obj->getInfoAboutOrder($orderId);
			$this->view->num = $arrayInfoOrder->num;
			$this->view->nameTheme = $arrayInfoOrder->name_theme;
			$this->view->task = $arrayInfoOrder->task;
			$this->view->additionInfo = $arrayInfoOrder->addition_info;
			$this->view->capacity = $arrayInfoOrder->capacity . ' стр.' ;
			$this->view->specialty = $arrayInfoOrder->specName;
			$this->view->type = $arrayInfoOrder->typeName;
			$this->view->status = $arrayInfoOrder->statusName;
			$this->view->clientNick = $arrayInfoOrder->nickname;
			$this->view->clientAvatar = $pathImg . '/avatars/' . $arrayInfoOrder->avatar;
			$this->view->clientLink = $baseUrl . '/client/info/' . $arrayInfoOrder->id;
			$dateCreateNormal = $obj->getNormalDate($arrayInfoOrder->date_create);
			$this->view->dateCreate = $obj->getMonthFromDigit($dateCreateNormal);
			$dateExpireNormal = $obj->getNormalDate($arrayInfoOrder->date_expire);
			$this->view->dateExpire = $obj->getMonthFromDigit($dateExpireNormal);
		}
			
	}
	
	
	/*public function orderAction()
	{	
		if ($this->getRequest()->isXmlHttpRequest())
		{	
			try
			{
				$response = array();
				
				$action = $this->_request->getParam('param');
				
				switch($action)
				{
					case 'checkLogged':
						$auth = Zend_Auth::getInstance();
						if ($auth->hasIdentity())
						{
							$identity = $auth->getStorage()->read();
							$nickname = $identity->nickname;
							$roleId = $identity->role_id;
						}
						else
							$this->_helper->json('Not auth');
						
						$response = Author::checkLoggedForChat($nickname,$roleId);
						break;
				
					//case 'logout':
						//$response = Chat::logout();
						//break;
				
					case 'submitChat':
						$text = $this->_request->getParam('text');
						$response = Author::submitChat($text);
						break;
				
					case 'getUsers':
						$response = Author::getUsersForChat();
						break;
				
					case 'getChats':
						$lastId = $this->_request->getParam('lastId');
						$response = Author::getTextForChat($lastId);
						break;
				
					default:
						throw new Exception('Wrong action');
				}
				
				$this->_helper->json($response);
			}
			catch (Exception $e)
			{
				$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
			}
		}
	}*/
	
	
	public function infoAction()
	{
		$authorIdFromBrowser = $this->_request->getParam('authorId');
		
		if (!$this->getRequest()->isXmlHttpRequest())
		{
			if (!Author::checkAuthorId($authorIdFromBrowser))
				throw new Zend_Controller_Action_Exception('This page does not exist', 404);
		}
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
			if ($authorId == $authorIdFromBrowser)
				$this->view->mode = 1;

			$obj = new Author();
			$pathImg = Zend_Registry::get('config')->url->img;
		}
		else
			throw new Zend_Exception("Error auth");	
		
			if ($this->getRequest()->isXmlHttpRequest())
			{	
				$data = $this->_request->getPost();
				
				$curPage = $data['page'];
				$rowsPerPage = $data['rows'];
				$sortingField = $data['sidx'];
				$sortingOrder = $data['sord'];
				
				if ($data['rows'] == 5000)
				{	
					try
					{
						$res = $obj->getCompletedOrdersForGrid($authorId,$sortingField,$sortingOrder);
						$totalRows = count($res);
						 
						$response->total = ceil($totalRows / $rowsPerPage);
						$response->records = $totalRows;
						$response->page = $curPage;			
	
						for ($i = 0;$i < $totalRows;$i++)
						{
							$dateExpireNormal = $obj->getNormalDate($res[$i]->date_expire);
							$dateExpire = $obj->getMonthForNormalDate($dateExpireNormal);		
							$avatar = $pathImg . '/avatars/' . $res[$i]->avatar;
							$clientId = $res[$i]->client_id;
							$price = $res[$i]->price . ' руб.';
								
							if ($res[$i]->grade == 1)
								$grade = $pathImg . '/raitings/one.gif';
							else if ($res[$i]->grade == 2)
								$grade = $pathImg . '/raitings/two.gif';
							else if ($res[$i]->grade == 3)
								$grade = $pathImg . '/raitings/three.gif';
							else if ($res[$i]->grade == 4)
								$grade = $pathImg . '/raitings/four.gif';
							else if ($res[$i]->grade == 5)
								$grade = $pathImg . '/raitings/five.gif';
		
							$response->rows[$i]['cell'] = array($res[$i]->num,$dateExpire,$res[$i]->name,$res[$i]->name_theme,$price,$res[$i]->nickname,$avatar,$clientId,$res[$i]->comment,$grade,$res[$i]->grade,$res[$i]->date_expire);
						}
						 
						$this->_helper->json($response);
					}
					catch (Exception $e)
					{
						$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
					};
				}
				
				if ($data['rows'] == 100)
				{
					try
					{
						$res = $obj->getGuaranteedOrdersForGrid($authorId,$sortingField,$sortingOrder);
						$totalRows = count($res);
					
						$response->total = ceil($totalRows / $rowsPerPage);
						$response->records = $totalRows;
						$response->page = $curPage;
					
						for ($i = 0;$i < $totalRows;$i++)
						{
							$remain = $obj->getHowRemain($res[$i]->date_guarantee);
							$dateExpireNormal = $obj->getNormalDate($res[$i]->date_guarantee);
							$dateExpire = $obj->getMonthForNormalDate($dateExpireNormal);
							$date = $dateExpire.'<br></br>осталось: '.$remain;
							$avatar = $pathImg . '/avatars/' . $res[$i]->avatar;
							$clientId = $res[$i]->client_id;
							$price = $res[$i]->price . ' руб.';
							
							$response->rows[$i]['cell'] = array($res[$i]->num,$date,$res[$i]->name,$res[$i]->name_theme,$price,$res[$i]->nickname,$avatar,$clientId,$res[$i]->note);
						}
					
						$this->_helper->json($response);
					}
					catch (Exception $e)
					{
						$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
					};
				}
	
				if ($data['rows'] == 50)
				{
					try
					{
						$res = $obj->getAppointedOrdersForGrid($authorId,$sortingField,$sortingOrder);
						$totalRows = count($res);
				
						$response->total = ceil($totalRows / $rowsPerPage);
						$response->records = $totalRows;
						$response->page = $curPage;
				
						for ($i = 0;$i < $totalRows;$i++)
						{
							$remain = $obj->getHowRemain($res[$i]->date_appoint);
							$dateAppointNormal = $obj->getNormalDate($res[$i]->date_appoint);
							$dateAppoint = $obj->getMonthForNormalDate($dateAppointNormal);
							$date = $dateAppoint.'<br></br>осталось: '.$remain;
							$avatar = $pathImg . '/avatars/' . $res[$i]->avatar;
							$clientId = $res[$i]->client_id;
							$price = $res[$i]->price . ' руб.';
							
							$response->rows[$i]['cell'] = array($res[$i]->num,$date,$res[$i]->name,$res[$i]->name_theme,$res[$i]->nickname,$price,$avatar,$clientId,$res[$i]->date_appoint);
						}
	
						$this->_helper->json($response);
					}
					catch (Exception $e)
					{
						$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
					};
				}
				
				if ($data['rows'] == 90)
				{
					try
					{
						$res = $obj->getImplementedOrdersForGrid($authorId,$sortingField,$sortingOrder);
						$totalRows = count($res);
							
						$response->total = ceil($totalRows / $rowsPerPage);
						$response->records = $totalRows;
						$response->page = $curPage;
							
						for ($i = 0;$i < $totalRows;$i++)
						{
							$remain = $obj->getHowRemain($res[$i]->date_expire);
							$dateExpireNormal = $obj->getNormalDate($res[$i]->date_expire);
							$dateExpire = $obj->getMonthForNormalDate($dateExpireNormal);
							$date = $dateExpire.'<br></br>осталось: '.$remain;
							$avatar = $pathImg . '/avatars/' . $res[$i]->avatar;
							$clientId = $res[$i]->client_id;
							$price = $res[$i]->price . ' руб.';
				
							$response->rows[$i]['cell'] = array($res[$i]->num,$date,$res[$i]->name,$res[$i]->name_theme,$res[$i]->nickname,$price,$avatar,$clientId,$res[$i]->date_expire);
						}
				
						$this->_helper->json($response);
					}
					catch (Exception $e)
					{
						$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
					};
				}
				
				if ($data['rows'] == 1)
				{
					try
					{
						$nameTable = 'authors_completed_orders';
						$res = $obj->getOrdersForGraphic($data['id'],$nameTable);
						$ordersCompleted = $obj->getOrdersOfMonthForGraphic($res);
						$response->completed = array(
							$ordersCompleted['jan'],
							$ordersCompleted['feb'],
							$ordersCompleted['mar'],
							$ordersCompleted['apr'],
							$ordersCompleted['may'],
							$ordersCompleted['jun'],
							$ordersCompleted['jul'],
							$ordersCompleted['aug'],
							$ordersCompleted['sep'],
							$ordersCompleted['oct'],
							$ordersCompleted['nov'],
							$ordersCompleted['dec']
						);
						
						$nameTable = 'authors_uncompleted_orders';
						$res = $obj->getOrdersForGraphic($data['id'],$nameTable);
						$ordersUnCompleted = $obj->getOrdersOfMonthForGraphic($res);
						$response->uncompleted = array(
							$ordersUnCompleted['jan'],
							$ordersUnCompleted['feb'],
							$ordersUnCompleted['mar'],
							$ordersUnCompleted['apr'],
							$ordersUnCompleted['may'],
							$ordersUnCompleted['jun'],
							$ordersUnCompleted['jul'],
							$ordersUnCompleted['aug'],
							$ordersUnCompleted['sep'],
							$ordersUnCompleted['oct'],
							$ordersUnCompleted['nov'],
							$ordersUnCompleted['dec']	
						);
							
						$this->_helper->json($response);
					}
					catch (Exception $e)
					{
						$this->_helper->json(array('errMess'=>'Error: '.$e->getMessage()));
					};
				}	
				
			}
			else 
			{	
				if ($authorId != $authorIdFromBrowser)
				{
					$authorId = $authorIdFromBrowser;
				}

				$res = $obj->getAuthorInfo($authorId);
				$dayInSystem = $obj->getDayInSystem($res['date_registered']);
				$this->view->authorId = $authorId;
				
				if ($res['role'] == 'client')
					$this->view->role = 'Заказчик';
				else
					$this->view->role = 'Автор';
					
				if ($res['country'] == 'Беларусь')
					$this->view->country = $pathImg . '/flags/Belarus.png';
				else if ($res['country'] == 'Россия')
					$this->view->country = $pathImg . '/flags/Russia.png';
				else if ($res['country'] == 'Украина')
					$this->view->country = $pathImg . '/flags/Ukraine.png';
				else if ($res['country'] == 'Казахстан')
					$this->view->country = $pathImg . '/flags/Kazakhstan.png';
				else if ($res['country'] == 'Болгария')
					$this->view->country = $pathImg . '/flags/Bulgaria.png';
					
				if ($res['avatar'] > 0)
					$this->view->avatar = $pathImg . '/avatars/' . $res['avatar'];
				else
					$this->view->avatar = $pathImg . '/avatars/icon-default.png';
	
				$this->view->specialties = $obj->getAuthorSpecialties($authorId);;
				$this->view->nickname = $res['nickname'];
				$this->view->dayInSystem = $dayInSystem;
				
				$completedOrders = $obj->getTypesCompletedOrdersForDiagram($authorId);	
				$this->view->countCompletedOrders = $completedOrders['countTotal'];
				$this->view->countCompletedDiploms = $completedOrders['countDiploms'];
				$this->view->countCompletedCourses = $completedOrders['countCourses'];
				$this->view->countCompletedControls = $completedOrders['countControls'];
				
				if (!$completedOrders['countOthers'])
					$this->view->countCompletedOthers = 0;
				else
					$this->view->countCompletedOthers = $completedOrders['countOthers'];
	
				$this->view->countUnCompletedOrders = $obj->getCountUnCompletedOrders($authorId);		
			}
		}
	
	
	
}
