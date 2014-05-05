$(document).ready(function(){
	
	chat.init();
	
});

var chat = {
	
	// data содержит перменные для использования в классах:
	
	data : {
		lastID 		: 0,
		noActivity	: 0,
		role		: 0
	},
	
	// Init привязывает обработчики событий и устанавливает таймеры:
	
	init : function(){
				
		// Конвертируем div #chatLineHolder в jScrollPane,
		// сохраняем API плагина в chat.data:
		
		chat.data.jspAPI = $('#chatLineHolder').jScrollPane({
			verticalDragMinHeight: 12,
			verticalDragMaxHeight: 12
		}).data('jsp');
		
		
		// Используем перменную working для предотвращения
		// множественных отправок формы:
		
		var working = false;

		// Отправляем данные новой строки чата:
		
		$('#submitForm').submit(function()
		{	
			chat.data.noActivity = 0;
			
			var text = $('#chatText').val();
			
			if (text.length == 0)
			{
				return false;
			}
			
			if (working) 
				return false;
			
			working = true;
			
			// Генерируем временный ID для чата:
			var tempID = 't'+Math.round(Math.random()*1000000);
			
			params = {
					id			: tempID,
					author		: chat.data.name,
					text		: text.replace(/</g,'&lt;').replace(/>/g,'&gt;')
				};
			
			$.tzPOSTsubmitChat('submitChat',text,function(r)
			{
				working = false;
				
				$('#chatText').val('');
				$('div.chat-'+tempID).remove();

				params['id'] = r.insertID;
			});
			
			return false;
		});
		
		// Проверяем состояние подключения пользователя (обновление браузера)
		
		$.tzPOST('checkLogged',null,function(r)
		{
			if(r.logged)
			{
				chat.login(r.loggedAs.name,r.loggedAs.avatar,r.loggedAs.role);
			}
		});
		
		// Самовыполняющиеся функции таймаута
		
		(function getChatsTimeoutFunction(){
			chat.getChats(getChatsTimeoutFunction);
		})();
		
		(function getUsersTimeoutFunction(){
			chat.getUsers(getUsersTimeoutFunction);
		})();
		
	},
	
	// Метод login скрывает данные регистрации пользователя
	// и выводит форму ввода сообщения
	
	login : function(name,avatar,role)
	{
		chat.data.name = name;
		chat.data.role = role;
		
		$('#submitForm').fadeIn();
		$('#chatText').focus();	
	},
	
	// Метод render генерирует разметку HTML, 
	// которая нужна для других методов:
	
	render : function(template,params)
	{
		var arr = [];
		
		switch(template)
		{		
			case 'chatLine':		
				if (chat.data.role == 3)
					arr = [
							'<div class="chattest chat-',params.id,' rounded"><span class="author">',params.author,
							':</span>&nbsp<span class="text">',params.text,'</span><span class="time">',params.time,'</span><span class="date">',params.date,'</span></div>'];	
				else
					arr = [
							'<div class="chat chat-',params.id,' rounded"><span class="author">',params.author,
							':</span>&nbsp<span class="text">',params.text,'</span><span class="time">',params.time,'</span><span class="date">',params.date,'</span></div>'];	
			break;
					
			case 'user':
				arr = [
					'<div class="user" title="',params.name,'"><img src="',
					params.avatar,'" width="50" height="50" onload="this.style.visibility=\'visible\'" /></div>'
				];
			break;
		}

		return arr.join('');
		
	},
	
	// Метод addChatLine добавляет строку чата на страницу
	
	addChatLine : function(params)
	{
		var d = new Date();
		
		for(var i=0;i<params.length;i++)
		{	
			if(params[i].time) 
			{	
				d.setUTCHours(params[i].time.hours,params[i].time.minutes);
			}
			params[i].time = (d.getHours() < 10 ? '0' : '' ) + d.getHours() + ':' + (d.getMinutes() < 10 ? '0':'') + d.getMinutes();
			
			if (params[i].date)
			{
				d.setUTCMonth(params[i].date.month);
				d.setUTCDate(params[i].date.day);
				d.setUTCFullYear(params[i].date.year);
				params[i].date = (d.getDate() < 10 ? '0' : '' ) + d.getDate() + '.' + (d.getMonth() < 10 ? '0' : '' ) + d.getMonth() + '.' + d.getUTCFullYear();
			}
			else
				params[i].date = (d.getDate() < 10 ? '0' : '' ) + d.getDate() + '.' + ((d.getUTCMonth()+1) < 10 ? '0' : '' ) + (d.getUTCMonth()+1) + '.' + d.getUTCFullYear();
		
			var markup = chat.render('chatLine',params[i]),
				exists = $('#chatLineHolder .chat-'+params[i].id);
	
			if(exists.length)
			{
				exists.remove();
			}
			
			if(!chat.data.lastID)
			{
				$('#chatLineHolder p').remove();
			}
			
			if(params[i].id.toString().charAt(0) != 't')
			{
				var previous = $('#chatLineHolder .chat-'+(+params[i].id - 1));
				if(previous.length)
				{
					previous.after(markup);
				}
				else chat.data.jspAPI.getContentPane().append(markup);
			}
			else chat.data.jspAPI.getContentPane().append(markup);	
		}
		
		if (params.length != 0)
		{
			chat.data.jspAPI.reinitialise();
			chat.data.jspAPI.scrollToBottom(true);
		}
		
		
	},
	
	// Данный метод запрашивает последнюю запись в чате
	// (начиная с lastID), и добавляет ее на страницу.
	
	getChats : function(callback)
	{
		$.tzGETgetChats('getChats',chat.data.lastID,function(r)
		{
			
			var arr = [];
			
			for(var i=0;i<r.chats.length;i++)
			{
				//chat.addChatLine(r.chats[i]);
				arr.push(r.chats[i]);
			}
			
			chat.addChatLine(arr);
			
			if(r.chats.length)
			{
				chat.data.noActivity = 0;
				chat.data.lastID = r.chats[i-1].id;
			}
			else
			{
				chat.data.noActivity++;
			}
			
			if(!chat.data.lastID)
			{
				chat.data.jspAPI.getContentPane().html('<p class="noChats">Ничего еще не написано</p>');
			}
				
			if(chat.data.noActivity > 3)
			{
				nextRequest = 2000;
			}
			else if(chat.data.noActivity > 10)
			{
				nextRequest = 5000;
			}	
			else if(chat.data.noActivity > 20)
			{
				nextRequest = 10000;
			}
			else
				var nextRequest = 1000;
		
			setTimeout(callback,nextRequest);
		});
	},
	

	getUsers : function(callback){
		$.tzGETgetUsers('getUsers',null,function(r){
			
			var users = [];
			
			for(var i=0; i< r.users.length;i++)
			{
				if(r.users[i])
				{
					users.push(chat.render('user',r.users[i]));
				}
			}
			
			var message = '';
			
			if(r.total < 1)
			{
				message = 'Online: 0';
			}
			else 
			{
				message = 'Online: ' + r.total;
			}
			
			users.push('<p class="count">'+message+'</p>');
			
			$('#chatUsers').html(users.join(''));
			
			setTimeout(callback,5000);
		});
	},
	
};



$.tzPOST = function(action,data,callback){
	$.post('./order/author',{param:action},callback,'json');
}

$.tzPOSTsubmitChat = function(action,data,callback){
	$.post('./order/author',{param:action,text:data},callback,'json');
}

$.tzGETgetChats = function(action,data,callback){
	$.get('./order/author',{param:action,lastId:data},callback,'json');
}

$.tzGETgetUsers = function(action,data,callback){
	$.get('./order/author',{param:action},callback,'json');
}

// Метод jQuery для замещающего текста:

$.fn.defaultText = function(value){
	
	var element = this.eq(0);
	element.data('defaultText',value);
	
	element.focus(function(){
		if(element.val() == value){
			element.val('').removeClass('defaultText');
		}
	}).blur(function(){
		if(element.val() == '' || element.val() == value){
			element.addClass('defaultText').val(value);
		}
	});
	
	return element.blur();
}