<?php

/**
 * Works with authors
 */
class Author extends Zend_Db_Table_Abstract 
{
	
	public $_db = null;
	
	public function __construct()
	{
		$this->_db = Zend_Registry::get('db');
	}
    
    /**
     * Registers new author 
     */
   	public function regNewAuthor($authorData,$infoData) 
    {
		$authorData['date_registered'] = new Zend_Db_Expr("NOW()");     
		$authorData['ip_registered'] = Zend_Controller_Front::getInstance()->getRequest()->getServer('REMOTE_ADDR');	
		$authorData['browser'] = Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_USER_AGENT');	
		$authorData['role_id'] = 1;  	 
		$uniq_code = uniqid('',true);
		$authorData['code_confirm'] = $uniq_code;

		$db = Zend_Registry::get('db');	
		$db->insert('authors',$authorData);
		$lastIdAuthors = $this->getAdapter()->lastInsertId();
		
		$db->insert('passports',array());
		$lastIdPassports = $this->getAdapter()->lastInsertId();
		
		$db->insert('info',$infoData);
		$lastIdInfo = $this->getAdapter()->lastInsertId();
			
		$dataUpdateAuthors = array(
			'info_id' => $lastIdInfo,
			'passport_id' => $lastIdPassports
         );
		
		$db->update('authors',$dataUpdateAuthors,'id = '.$lastIdAuthors);
		
		$hash_code = Zend_Registry::get('config')->mail->hash_code;
		$email = $authorData['email'];
		$subject = 'Подтверждение регистрации на сайте ';
		$body = 'Спасибо за регистрацию '.$authorData['nickname'].'! Для подтверждения регистрации перейдите по ссылке http://localhost/main/public/auth/confirmreg?uniq_code='.$uniq_code.'&hash_code='.$hash_code.'&type=author';
		$mail = App_Controller_Helper_Mail::Send($email,$subject,$body);
			
        return true;
    }
	

	public function updateAuthorSettings($infoData,$countryData)
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
		}	
		else 
			return false;
		
		$db = Zend_Registry::get('db');
		
		$row = $db->fetchRow($db->select()
					->from('authors',array('info_id'))
					->where('id = ?', $authorId));
		
		if(!$row)
		{
			throw new Zend_Exception("Error select from authors");
		}

		$infoData = array_merge($infoData,$countryData);
		$db->update('info',$infoData,'id = '.$row->info_id);
			
		return true;
	}
	
	/**
	 * Populates form "author/settings"
	 * @throws Zend_Exception
	 * @return unknown
	 */
	public function populateForm()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
			
			$db = Zend_Registry::get('db');
			$row = $db->fetchRow($db->select()
					->from('authors',array('info_id','email'))
					->where('id = ?', $authorId));
			
			if(!$row)
			{
				throw new Zend_Exception("Error select from authors");
			}
			
			$infoId = $row->info_id;
			$email = $row->email;
			
			$row = $db->fetchRow($db->select()
					->from('info')
					->where('id = ?', $infoId));
			
			if(!$row)
			{
				throw new Zend_Exception("Error select from info");
			}
			
			$arrayInfo = array('id' => $authorId,'email' => $email,'name' => $row->name,'lastname' => $row->lastname,'surname' => $row->surname,'phone_mobile' => $row->phone_mobile,'phone_static' => $row->phone_static,'skype' => $row->skype,'icq' => $row->icq);
			
			$countryId = $row->country_id;	
			if ($countryId != null)
			{
				
				$row = $db->fetchRow($db->select()
						  ->from('countries')
						  ->where('id = ?', $countryId));
				
				if(!$row)
				{
					throw new Zend_Exception("Error select from countries");
				}
				
				$arrayCountry = array('country' => $row->id);
				
				return array_merge($arrayInfo,$arrayCountry);
			}				
		}	
		return $arrayInfo;
	}

	
	/**
	 * Gets data from database for populates field "PaymentNum"
	 */
	public function getPopulatePaymentNum($selectedItem)
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
				
			$db = Zend_Registry::get('db');
			$row = $db->fetchRow($db->select()
					->from('payments_buff',array('num'))
					->where('author_id = ?', $authorId)
					->where('payment_id = ?', $selectedItem));
				
			if(!$row)
			{
				//throw new Zend_Exception("Error select from payments_buff");
				return null;
			}
			else	
				$num = $row->num;
			
			return $num;
		}	
		else 
			return null;	
	}
	
	
	public function updatePaymentNum($paymentName,$paymentNum)
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getStorage()->read();
			$authorId = $identity->id;
			
			$paymentData = array('author_id' => $authorId,'payment_id' => $paymentName,'num' => $paymentNum);
				
			$db = Zend_Registry::get('db');
			$row = $db->fetchRow($db->select()
					->from('payments_buff',array('id'))
					->where('author_id = ?', $authorId)
					->where('payment_id = ?', $paymentName));
				
			if (!$row)
			{
				$db->insert('payments_buff',$paymentData);
			}
			else
			{
				$db->update('payments_buff',$paymentData,'id = '.$row->id);
			}
			
			return true;
		}
		else
			return false;
	}
	
	
	public function getOrdersForGrid($mode = null,$sField = null,$sData = null,$sortingField,$sortingOrder,$limit,$authorId)
	{	
		if ($sField != null && $mode != null)
		{
			if ($sField == 'date_create' || $sField == 'date_expire')
			{	
				$sTable = 'datetime';	
				$where = $sTable . '.' . $sField;
			}
			else if ($sField == 'specialty_id')
			{
				$sTable = 'specialty';
				$where = $sTable . '.name';
			}	
			else if ($sField == 'num')
			{
				$sTable = 'order';
				$where = $sTable . '.' . $sField;
			}
			else if ($sField == 'name_theme')
			{
				$sTable = 'order';
				$where = $sTable . '.' . $sField;
			}	
			else if ($sField == 'type_id')
			{
				$sTable = 'type';
				$where = $sTable . '.name';
			}
			else if ($sField == 'price')
			{
				$sTable = 'author_has_estimated_order';
				$where = $sTable . '.price';
			}
			
			if ($mode == 'cn')	
			{	
				$row = $this->_db->query("SELECT order.id,order.name_theme,order.num,datetime.date_create,datetime.date_expire,type.name AS tname,specialty.name AS sname,author_has_estimated_order.price
					FROM `order`
					INNER JOIN `datetime` ON datetime.id = order.datetime_id
					INNER JOIN `type` ON type.id = order.type_id
					INNER JOIN specialty ON specialty.id = order.specialty_id
					LEFT JOIN author_has_estimated_order ON author_has_estimated_order.order_id = order.id AND author_has_estimated_order.author_est_id = '$authorId'
					WHERE $where LIKE '%" . $sData . "%'
					LIMIT $limit");
			}
			else if ($mode == 'bw')
			{	
				$row = $this->_db->query("SELECT order.id,order.name_theme,order.num,datetime.date_create,datetime.date_expire,type.name AS tname,specialty.name AS sname,author_has_estimated_order.price
					FROM `order`
					INNER JOIN `datetime` ON datetime.id = order.datetime_id
					INNER JOIN `type` ON type.id = order.type_id
					INNER JOIN specialty ON specialty.id = order.specialty_id
					LEFT JOIN author_has_estimated_order ON author_has_estimated_order.order_id = order.id AND author_has_estimated_order.author_est_id = '$authorId'
					WHERE $where LIKE '" . $sData . "%'
					LIMIT $limit");
			}
			else if ($mode == 'eq')
			{
				$row = $this->_db->query("SELECT order.id,order.name_theme,order.num,datetime.date_create,datetime.date_expire,type.name AS tname,specialty.name AS sname,author_has_estimated_order.price
					FROM `order`
					INNER JOIN `datetime` ON datetime.id = order.datetime_id
					INNER JOIN `type` ON type.id = order.type_id
					INNER JOIN specialty ON specialty.id = order.specialty_id
					LEFT JOIN author_has_estimated_order ON author_has_estimated_order.order_id = order.id AND author_has_estimated_order.author_est_id = '$authorId'
					WHERE $where = '$sData'
					LIMIT $limit");
			}
			else if ($mode == 'ne')
			{
				$row = $this->_db->query("SELECT order.id,order.name_theme,order.num,datetime.date_create,datetime.date_expire,type.name AS tname,specialty.name AS sname,author_has_estimated_order.price
					FROM `order`
					INNER JOIN `datetime` ON datetime.id = order.datetime_id
					INNER JOIN `type` ON type.id = order.type_id
					INNER JOIN specialty ON specialty.id = order.specialty_id
					LEFT JOIN author_has_estimated_order ON author_has_estimated_order.order_id = order.id AND author_has_estimated_order.author_est_id = '$authorId'
					WHERE $where <> '$sData'
					LIMIT $limit");
			}
		}
		else
		{	
			$row = $this->_db->query("SELECT o.id,o.name_theme,o.num,d.date_create,d.date_expire,t.name AS tname,s.name AS sname,aheo.price 
				FROM `order` o
				INNER JOIN `datetime` d ON d.id = o.datetime_id 
				INNER JOIN `type` t ON t.id = o.type_id 
				INNER JOIN specialty s ON s.id = o.specialty_id
				LEFT JOIN author_has_estimated_order aheo ON aheo.order_id = o.id AND aheo.author_est_id = '$authorId'
				ORDER BY '.$sortingField . ' ' . $sortingOrder.' 
				LIMIT $limit");
		}
		
		return $row;
	}
	
	public function getCountOrdersForGrid($mode = null,$sField = null,$sData = null)
	{
		if ($mode != null)
		{
			$table = null;
			
			if ($sField == 'price')
			{
				$table = 'author_has_estimated_order';
			}
			else if ($sField == 'specialty_id')
			{
				$table = 'specialty';
			}
			else if ($sField == 'type_id')
			{
				$table = 'type';
			}
			
			if ($mode == 'eq')	
			{		
				if ($sField == 'price' || $sField == 'specialty_id' || $sField == 'type_id')
				{
					$row = $this->_db->query("SELECT COUNT(id) AS count FROM $table WHERE '$sField' = '$sData'");
					$row = $row->fetch(PDO::FETCH_ASSOC);
					
					return $row['count'];
				}
				else
				{
					$qWhere = ' WHERE ' . $sField . '=' . $this->_db->quote($sData);
				} 			
			}
			else if ($mode == 'ne')
			{
				if ($sField == 'price' || $sField == 'specialty_id' || $sField == 'type_id')
				{
					$row = $this->_db->query("SELECT COUNT(id) AS count FROM $table WHERE '$sField' <> '$sData'");
					$row = $row->fetch(PDO::FETCH_ASSOC);
						
					return $row['count'];
				}	
				else
				{
					$qWhere = ' WHERE ' . $sField . '<>' . $this->_db->quote($sData);
				}
			}
			else if ($mode == 'cn')
			{
				if ($sField == 'price' || $sField == 'specialty_id' || $sField == 'type_id')
				{
					$row = $this->_db->query("SELECT COUNT(id) AS count FROM $table WHERE '$sField' LIKE '%" . $sData . "%'");
					$row = $row->fetch(PDO::FETCH_ASSOC);
				
					return $row['count'];
				}
				else
				{
					$qWhere = ' WHERE ' . $sField . ' LIKE ' . $this->_db->quote('%' . $sData . '%');
				}		
			}
			else if ($mode == 'bw')
			{
				if ($sField == 'price' || $sField == 'specialty_id' || $sField == 'type_id')
				{
					$row = $this->_db->query("SELECT COUNT(id) AS count FROM $table WHERE '$sField' LIKE '" . $sData . "%'");
					$row = $row->fetch(PDO::FETCH_ASSOC);
				
					return $row['count'];
				}
				else
				{
					$qWhere = ' WHERE ' . $sField . ' LIKE ' . $this->_db->quote($sData . '%');
				}
			}
		}
		else 
		{	
			$qWhere = '';
		}
		
		$row = $this->_db->query('SELECT COUNT(id) AS count FROM `order`'.$qWhere);
		$row = $row->fetch(PDO::FETCH_ASSOC);
		
		return $row['count'];
	}
	
	public function getNormalDate($date)
	{
		$normalDate = new Zend_Date($date,'dd.MM.yyyy');
		$date = $normalDate->toString('dd-MM-yyyy');	
		
		return $date;
	}
	
	
	public function getMonthFromDigit($normalDate)
	{
		$dateArray = explode('-',$normalDate);
		if ($dateArray[0][0] == '0')
			$dateArray[0][0] = '';
		
		if($dateArray[1] == "1"){$month="января";}
		elseif($dateArray[1] == "2"){$month="февраля";}
		elseif($dateArray[1] == "3"){$month="марта";}
		elseif($dateArray[1] == "4"){$month="апреля";}
		elseif($dateArray[1] == "5"){$month="мая";}
		elseif($dateArray[1] == "6"){$month="июня";}
		elseif($dateArray[1] == "7"){$month="июля";}
		elseif($dateArray[1] == "8"){$month="августа";}
		elseif($dateArray[1] == "9"){$month="сентября";}
		elseif($dateArray[1] == "10"){$month="октября";}
		elseif($dateArray[1] == "11"){$month="ноября";}
		elseif($dateArray[1] == "12"){$month="декабря";}
		
		$fullDate = $dateArray[0].' '.$month.' '.$dateArray[2];
		
		return $fullDate;
	}
	

	public static function getTextForChat($lastId)
	{
		$lastId = (int)$lastId;
		
		$db = Zend_Registry::get('db');
		
		$result = $db->query('SELECT * FROM webchat_lines WHERE id > '.$lastId.' ORDER BY id ASC');
		
		$chats = array();
			
		while($chat = $result->fetch(PDO::FETCH_OBJ))
		{
			$chat->time = array(
					'hours'		=> gmdate('H',strtotime($chat->ts)),
					'minutes'	=> gmdate('i',strtotime($chat->ts))
			);
			
			$chat->date = array(	
					'year' 		=> gmdate('Y',strtotime($chat->ts)),
					'month' 	=> gmdate('m',strtotime($chat->ts)),
					'day' 		=> gmdate('d',strtotime($chat->ts))
			);
			
			$chats[] = $chat;
			unset($chat);
		}
		
		return array('chats' => $chats);
	}
	
	
	public static function getUsersForChat()
	{	
		$db = Zend_Registry::get('db');
		
		$session = new Zend_Session_Namespace('chat');
		$name = $session->nickname;
		$dataUpdate = array('last_activity' => new Zend_Db_Expr("NOW()"));
		
		if ($session->nickname)
			//$db->update('webchat_users',$dataUpdate,'name = '.$name);
			$db->query("INSERT INTO webchat_users (name,avatar) VALUES ('$name','') ON DUPLICATE KEY UPDATE last_activity = NOW()");
		
		$db->query("DELETE FROM webchat_users WHERE last_activity < SUBTIME(NOW(),'0:0:10')");
		
		$total = $db->fetchAll($db->select()
				    ->from('webchat_users',array('id')));
		
		$result = $db->query('SELECT * FROM webchat_users ORDER BY name ASC LIMIT 2');
		
		$users = array();
		
		while($user = $result->fetch(PDO::FETCH_OBJ))
		{
			$user->avatar = '';
			$user->role = 3;
			$users[] = $user;
		}
		
		return array(
				'users' => $users,
				'total' => count($total)
		);
	}
	
	
	public static function checkLoggedForChat($nickname,$roleId)
	{	
		$session = new Zend_Session_Namespace('chat');
		
		if ($session->nickname != $nickname)
		{	
			$session->nickname = $nickname;
			$session->roleId = $roleId;
			
			$userData = array(
					'name'		=> $nickname,
					'avatar'	=> '',
					'last_activity'	=> new Zend_Db_Expr("NOW()")
			);
			
			$db = Zend_Registry::get('db');
			$db->insert('webchat_users',$userData);
		}	
		
		$response['logged'] = true;
		$response['loggedAs'] = array(
				'name'		=> $nickname,
				'avatar'	=> '',
				'role'		=> $roleId
		);
			
		return $response;
	}

	
	public static function submitChat($chatText)
	{
		/*if(!$_SESSION['user'])
		{
			throw new Exception('Вы вышли из чата');
		}
		
		if(!$chatText)
		{
			throw new Exception('Вы не ввели сообщение');
		}*/
		
		$session = new Zend_Session_Namespace('chat');
		
		$chatData = array(
		 	'author'	=> $session->nickname,
			'text'		=> $chatText
		);
		
		$db = Zend_Registry::get('db');	
		$db->insert('webchat_lines',$chatData);
		$insertId = $db->lastInsertId();
		
		return array(
				'status'	=> 1,
				'insertID'	=> $insertId
		);
		
	}
	
	
	public function getAuthorInfo($authorId)
	{
		$row = $this->_db->fetchRow($this->_db->select()
						 ->from('authors',array('nickname','email','avatar','date_registered','country_id'))
						 ->join('countries','countries.id = authors.country_id',array('countryName' => 'name'))	
						 ->join('ranks','ranks.id = authors.rank_id',array('rankName' => 'name'))
						 ->join('roles','roles.id = authors.role_id',array('roleName' => 'name'))
						 ->where('authors.id = ?', $authorId)
		);
		
		if (!$row)
		{
			throw new Zend_Exception("Error select from authors");
		}
		
		$res = array(
				'nickname' 		  => $row->nickname,
				'email'		  	  => $row->email,
				'date_registered' => $row->date_registered,
				'avatar' 		  => $row->avatar,
				'role'		  	  => $row->roleName,
				'country'		  => $row->countryName,
				'rank'			  => $row->rankName
		);

		return $res;
	}
	
	
	public function getDayInSystem($dateRegistered)
	{
		$day = (strtotime(date("Y-m-d")) - strtotime($dateRegistered)) / 3600 / 24;;
		
		return $day;
	}
	
	
	public function getCompletedOrdersForGrid($authorId,$sortingField,$sortingOrder)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from('authors_completed_orders',array())
						  ->join('orders','orders.id = authors_completed_orders.order_id',array('num','name_theme','comment','grade','client_id'))
						  ->join('datetime','datetime.id = orders.datetime_id',array('date_expire'))
						  ->join('types','types.id = orders.type_id',array('name'))
						  ->join('clients','clients.id = orders.client_id',array('nickname','avatar'))
						  ->join('authors_estimated_orders','authors_estimated_orders.order_id = orders.id AND authors_estimated_orders.author_est_id = authors_completed_orders.author_id',array('price'))
						  ->where('author_id = ?', $authorId)
						  ->order($sortingField.' '.$sortingOrder));
		
		return $rows;
	}
	
	
	public function getAuthorSpecialties($authorId)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						 ->from('authors_specialties',array())
						 ->join('specialties','authors_specialties.specialty_id = specialties.id',array('name'))
						 ->where('author_id = ?', $authorId)
						 ->order('name ASC'));
		
		return $rows;
	}
	
	
	public function getTypesCompletedOrdersForDiagram($authorId)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from('authors_completed_orders',array())
						  ->join('orders','orders.id = authors_completed_orders.order_id',array())
						  ->join('types','types.id = orders.type_id',array('name'))
						  ->where('author_id = ?', $authorId));
		
		$res = array();
		$res['countTotal'] = count($rows);
		$res['countDiploms'] = 0;
		$res['countOthers'] = 0;
		$res['countCourses'] = 0;
		$res['countControls'] = 0;			
		$countOrders = count($rows);
		
		for ($i=0;$i<$countOrders;$i++)
		{
			if ($rows[$i]->name == 'Диплом')
				$res['countDiploms']++;
			else if ($rows[$i]->name == 'Курсовая')
				$res['countCourses']++;
			else if ($rows[$i]->name == 'Контрольная')
				$res['countControls']++;
			else 
				$res['countOthers']++;
		}	
		
		return $res;
	}
	
	
	public function getCountUnCompletedOrders($authorId)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from('authors_uncompleted_orders',array('id'))
						  ->join('orders','orders.id = authors_uncompleted_orders.order_id',array())
						  ->where('author_id = ?', $authorId));
	
		return count($rows);
	}
	
	
	public function getGuaranteedOrdersForGrid($authorId,$sortingField,$sortingOrder)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from('authors_guaranteed_orders',array('note'))
						  ->join('orders','orders.id = authors_guaranteed_orders.order_id',array('num','name_theme','client_id'))
						  ->join('datetime','datetime.id = orders.datetime_id',array('date_guarantee'))
						  ->join('types','types.id = orders.type_id',array('name'))
						  ->join('clients','clients.id = orders.client_id',array('nickname','avatar'))
						  ->join('authors_estimated_orders','authors_estimated_orders.order_id = orders.id AND authors_estimated_orders.author_est_id = authors_guaranteed_orders.author_id',array('price'))
						  ->where('author_id = ?', $authorId)
						  ->order($sortingField.' '.$sortingOrder));
		
		return $rows;
	}
	
	
	public function getHowRemain($dateGuaranteed)
	{
		$date = new DateTime($dateGuaranteed);
		$replaceDate = $date->format("d-m-Y H:i");
		$timeDiff = strtotime($replaceDate) - strtotime(date("d-m-Y H:i"));
		$remain = sprintf('%d дн. %d ч. %d мин.',($timeDiff > 0 ? $timeDiff / 3600 / 24 : 0),($timeDiff > 0 ? (($timeDiff % 86400) / 3600) : 0),(($timeDiff % 3600) > 0 ? ($timeDiff % 3600) / 60 : 0));
		
		return $remain;
	}
	
	
	public function getAppointedOrdersForGrid($authorId,$sortingField,$sortingOrder)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from('authors_appointed_orders',array('id'))
						  ->join('orders','orders.id = authors_appointed_orders.order_id',array('num','name_theme','client_id'))
						  ->join('datetime','datetime.id = orders.datetime_id',array('date_appoint'))
						  ->join('types','types.id = orders.type_id',array('name'))
						  ->join('clients','clients.id = orders.client_id',array('nickname','avatar'))
						  ->join('authors_estimated_orders','authors_estimated_orders.order_id = orders.id AND authors_estimated_orders.author_est_id = authors_appointed_orders.author_id',array('price'))
						  ->where('author_id = ?', $authorId)
						  ->order($sortingField.' '.$sortingOrder));

		return $rows;
	}
	
	
	public function getImplementedOrdersForGrid($authorId,$sortingField,$sortingOrder)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from('authors_implemented_orders',array('id'))
						  ->join('orders','orders.id = authors_implemented_orders.order_id',array('num','name_theme','client_id'))
						  ->join('datetime','datetime.id = orders.datetime_id',array('date_expire'))
						  ->join('types','types.id = orders.type_id',array('name'))
						  ->join('clients','clients.id = orders.client_id',array('nickname','avatar'))
						  ->join('authors_estimated_orders','authors_estimated_orders.order_id = orders.id AND authors_estimated_orders.author_est_id = authors_implemented_orders.author_id',array('price'))
						  ->where('author_id = ?', $authorId)
						  ->order($sortingField.' '.$sortingOrder));
		
		return $rows;
	}
	
	
	public function getOrdersForGraphic($authorId,$nameTable)
	{
		$rows = $this->_db->fetchAll($this->_db->select()
						  ->from($nameTable,array())
						  ->join('orders','orders.id = ' . $nameTable . '.order_id',array('datetime_id'))
						  ->join('datetime','datetime.id = orders.datetime_id',array('date_expire'))
						  ->where('author_id = ?', $authorId));
		
		return $rows;
	}
	
	
	public function getOrdersOfMonthForGraphic($arr)
	{
		$res = array();
		$res['jan'] = 0;
		$res['feb'] = 0;
		$res['apr'] = 0;
		$res['may'] = 0;
		$res['jul'] = 0;
		$res['jun'] = 0;
		$res['aug'] = 0;
		$res['sep'] = 0;
		$res['oct'] = 0;
		$res['mar'] = 0;
		$res['oct'] = 0;
		$res['nov'] = 0;
		$res['dec'] = 0;
		$countArr = count($arr);
		$nowDate = date("Y-m-d");
		$nowDate = explode('-',$nowDate);
		$nowYear = $nowDate[0];
		
		for ($i=0;$i<$countArr;$i++)
		{
			$date = explode('-',$arr[$i]->date_expire);
			
			if ($nowYear == $date[0])
			{
				if($date[1] == "1" || $date[1] == "01"){$res['jan']++;}
				elseif($date[1] == "2" || $date[1] == "02"){$res['feb']++;}
				elseif($date[1] == "3" || $date[1] == "03"){$res['mar']++;}
				elseif($date[1] == "4" || $date[1] == "04"){$res['apr']++;}
				elseif($date[1] == "5" || $date[1] == "05"){$res['may']++;}
				elseif($date[1] == "6" || $date[1] == "06"){$res['jun']++;}
				elseif($date[1] == "7" || $date[1] == "07"){$res['jul']++;}
				elseif($date[1] == "8" || $date[1] == "08"){$res['aug']++;}
				elseif($date[1] == "9" || $date[1] == "09"){$res['sep']++;}
				elseif($date[1] == "10"){$res['oct']++;}
				elseif($date[1] == "11"){$res['nov']++;}
				elseif($date[1] == "12"){$res['dec']++;}	
			}		
		}
		
		return $res;
	}
	
	
	public static function checkAuthorId($authorId)
	{
		$db = Zend_Registry::get('db');
		
		$row = $db->fetchAll($db->select()
				  ->from('authors',array('id'))
				  ->where('id = ?', $authorId));
		
		if ($row)
			return true;
		else 
			return false;
	}
	
	
	public function getInfoAboutOrder($orderId)
	{
		$row = $this->_db->fetchRow($this->_db->select()
				->from('order',array('num','name_theme','task','addition_info','capacity'))
				->join('datetime','datetime.id = order.datetime_id',array('date_create','date_expire'))
				->join('specialty','specialty.id = order.specialty_id',array('specName' => 'name'))
				->join('type','type.id = order.type_id',array('typeName' => 'name'))
				->join('status','status.id = order.status_id',array('statusName' => 'name'))
				->join('client','client.id = order.client_id',array('id','nickname','avatar'))		
				->where('order.id = ?', $orderId));
		
		return $row;	
	}
	
	
	public function setAuthorBid($price, $dayImplement, $question, $authorId, $orderId)
	{	
		$row = $this->_db->fetchRow($this->_db->select()	
						 ->from('author_bid',array('id'))
						 ->where('author_id = ?',$authorId)
						 ->where('order_id = ?',$orderId));
		
		if ($row)
		{
			$updateArray = array(
					'price' => $price,
					'day_implement' => $dayImplement,
					'question' => $question,
			);
			
			$whereArray = array(
					'author_id = ?' => $authorId,
					'order_id = ?' => $orderId
			);
				
			$this->_db->update('author_bid', $updateArray, $whereArray);
		}
		else 
		{
			$insertArray = array(
					'price' => $price,
					'day_implement' => $dayImplement,
					'question' => $question,
					'author_id' => $authorId,
					'order_id' => $orderId
			);
				
			$this->_db->insert('author_bid',$insertArray);	
		}
		
		return true;

	}
	
	
	public function getAuthorBid($authorId,$orderId)
	{
		$row = $this->_db->fetchRow($this->_db->select()
						 ->from('author_bid',array('price','day_implement','question'))
						 ->where('author_id = ?',$authorId)
						 ->where('order_id = ?',$orderId));
		
		return $row;
	}
	
	
}