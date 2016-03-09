var Login = function () {
    
    return {
        //main function to initiate the module
        init: function () {
        	var form = $('#loginForm');
           form.validate({
	            errorElement: 'label', //default input error message container
	            errorClass: 'help-inline', // default input error message class
	            focusInvalid: false, // do not focus the last invalid input
	            rules: {
	                name: {
	                    required: true
	                },
	                pwd: {
	                    required: true
	                },
                  check: {
                      required: true
                  }
	            },

	            invalidHandler: function (event, validator) { //display error alert on form submit   
	                $('.alert-error', $('.login-form')).show();
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.control-group').addClass('error'); // set error class to the control group
	            },

	            success: function (label) {
	            		$('.alert-error').hide();
	                label.closest('.control-group').removeClass('error');
	                label.remove();
	            },

	            errorPlacement: function (error, element) {
	                error.addClass('help-small no-left-padding').insertAfter(element.closest('.input-icon'));
	            },

	            submitHandler: function (form) {
	            	$('.alert-error').hide();
	            	$('.alert-success').show();
	            }
	        });

	        $('#submit').click(function()
	        {
            if (form.valid() == false) 
            {
              return false;
            }
            var name = $('#name').val();
            var pwd = $('#pwd').val();
            var check = $('#check').val();
            var capImg = $('#capImg').attr('value');

		        var requestData = [
		            "name=",name,'&',
		            "pwd=",pwd,'&',
                "check=",check,'&',
                "capImg=",capImg
		        ].join('');
		        //console.log(requestData)
		        $.ajax({
            url : "login/loginAjax",
            type : "post",
            data : requestData,
            dataType :"json",
            success: function(result)
            {
                //console.log(result);
                if(result.status == 0)
                {
                  $('.alert-error').html(result.msg).removeClass('hide').attr('style','dispaly:block;');
                	setTimeout(function()
                	{
                    window.location.reload();
                	},1000);
                }
                else
                {
	                $('.alert-error').addClass('hide');
	                $('.alert-success').html(result.msg).removeClass('hide');
	                setTimeout(function(){
	                    window.location.href = '/anask/';
	                },1000);
                }
            }
        });

	        });

        }

    };

}();