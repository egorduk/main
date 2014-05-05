
	function populatePaymentNum()
	{
		$.ajax({
		url: './populate-payment-num/user/',
		type: 'POST',
			//data: 'jPaymentId='+$.toJSON(a),
		success: function(data) 
		{
			alert(data);
				//var result = $.parseJSON(data);				
		} 
		});
			
			//alert("qwe");
	}

		  