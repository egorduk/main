$(document).ready(function(){
		
	$("#blockAuthorPlaceBid").hide();
	var orderId = $("#orderId").val();
	
	$.ajax({
		url: './order/author/',
		type: 'POST',
		data: 'mode=loadPlaceBid' + '&orderId=' + orderId,
		success: function(data) 
		{			
			addPlaceBidHtml(data);
		} 
		});
	
	
	$("#btnAuthorConfirm").click(function()
	{
		var bid = $("#authorBid").val();
		var day = $("#authorDayImplement").val();
		var question = $("#authorQuestion").val();	
		
		$.ajax({
		url: './order/author/',
		type: 'POST',
		data: 'bid=' + bid + '&day=' + day + '&question=' + question + '&orderId=' + orderId,
		success: function(data) 
		{			
			$(".error").remove();
			
			if (data['mode'] == 'showError')
			{	
				for (key in data['authorBid'])
				{
					$("#authorBid").parent().append(addErrorHtml(data['authorBid'][key]));
				}
				
				for (key in data['authorDayImplement'])
				{	
					$("#authorDayImplement").parent().append(addErrorHtml(data['authorDayImplement'][key]));
				}
				
				for (key in data['authorQuestion'])
				{	
					$("#authorQuestion").parent().append(addErrorHtml(data['authorQuestion'][key]));
				}
			}
			else if (data['mode'] == 'showPlaceBid')
			{	
				addPlaceBidHtml(data);
			}
		} 
		});
	});
	
	
	function addErrorHtml(msg)
	{
	    //var o = '<ul id="errors" class="errors">';
		var append = '';
		
	   // for(errorKey in formErrors)
	    {
	        //o += '<li>' + formErrors[errorKey] + '</li>';
	    	append = '<span class="error">' + msg + '</span>'
	    }
	   // o += '</ul>';
	    
	    return append;
	}
	
	
	function addPlaceBidHtml(data)
	{
		$("#blockAuthorPlaceBid").fadeIn();
		$("#authorBid,#authorDayImplement,#authorQuestion").val('');			
		$("#blockAuthorPlaceBid").empty();	
		$("#blockAuthorPlaceBid").append('<span id="placePrice" class="showPlaceBid">Моя цена:</span>' + data['placePrice']);
		$("#blockAuthorPlaceBid").append('<br><span id="placeDayImplement" class="showPlaceBid">Мой срок:</span>' + data['placeDayImplement']);
		$("#blockAuthorPlaceBid").append('<br><span id="placeQuestion" class="showPlaceBid">Мой вопрос:</span>' + data['placeQuestion']);
	}
	
	
	
});
		  