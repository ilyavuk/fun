$(function(){

    $(document).on('click', '#SaveUserPersonalData', function (e) {
        e.preventDefault();
        $('.form-group').removeClass('has-error');
		$('.alert-danger').remove();
        $('.help-block').remove();

		var RequiredFields = {
            'Email': {'type': 'input', 're': /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i, 'error': 'Email is not valid'},
			'Password': {'type': 'input', 're': /^[a-z\d ,.'-]{6,30}$/i, 'error': 'The field name is not valid, make sure that the name doesn’t contain special chars, or that is not longer than 100 chars'},
        }; 
               
		var preventForm = false;
		
        $.each(RequiredFields, function (i, v) {
            if (!validate($(v.type + '[name="' + i + '"]').val(), v.re)) {
                $(v.type + '[name="' +  i + '"]').parents('.form-group').addClass('has-error');
                $(v.type + '[name="' +  i + '"]').parents('.form-group').append('<span class="help-block">' + v.error + '</span>');
                preventForm = true;
            }
        });
        
        if( $('input[name="Repassword"]').val() !== $('input[name="Password"]').val() ){
            $('input[name="Repassword"]').parents('.form-group').addClass('has-error');
            $('input[name="Repassword"]').parents('.form-group').append('<span class="help-block">Password doesn\'t match</span>');
            preventForm = true;            
        }

        if(!preventForm){
			$.ajax({
				type: "POST",
				url: '/admin/updateUser',
				data: $("#updateUserData").serialize(),
				success: function (result) {
                    console.log(result);
					if (result === '1') {
						alert("You have been successfully changed data!");
					}
				}
			});            
        }

    });

});