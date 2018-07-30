$(function () {

    $(document).on("click", "#Register", function (e) {
        e.preventDefault();
        var preventForm = false;
        $('.alert').remove();

        var Email = $('input[name="Email"]').val();
        var Pass = $('input[name="Pass"]').val();

		var RequiredFields = {
			'Email': {'type': 'input', 're': /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i, 'error': 'Email is not valid'},
			'Pass': {'type': 'input', 're': /^.{6,60}$/, 'error': 'The password is not valid, make sure that is between 6 and 100 chars long'}
		};

		$.each(RequiredFields, function (i, v) {
			if (!validate($(v.type + '[name="' + i + '"]').val(), v.re)) {
				$(v.type + '[name="' + i + '"]').after('<div class="alert alert-danger" role="alert"><strong>Ups!</strong> '+v.error+'</div>');
				preventForm = true;
			}
        });

        if(!preventForm && $('input[name="Pass"]').val() !== $('input[name="CPass"]').val()){
            $('input[name="CPass"]').after('<div class="alert alert-danger" role="alert"><strong>Ups!</strong>Password doesn\'t match!</div>');
            preventForm = true;
        }

        if(preventForm){
            $('#Register').after( '<div class="alert alert-danger" role="alert">Please correct errors and submit form again </div>');
        }else{
        $.ajax({
                type: "POST",
                url: '/checkEmail',
                data: '&Email='+Email,
                success: function (result) {
                    if(result === '1'){

                        $.ajax({
                            type: "POST",
                            url: '/Register',
                            data: $("#signin").serialize(),
                            success: function (result) {
                                if(result === '1'){
                                    window.location.href = '/';                        
                                }else{
                                    $('#SignIn').after( '<div class="alert alert-danger" role="alert">Signing in has been failed</div>');
                                }
                            }
                        }); 
                        

                    }else{
                        $('input[name="Email"]').after( '<div class="alert alert-danger" role="alert">Email already exists, please pick another one!</div>');
                    }
                }
            });

        }

    });


});