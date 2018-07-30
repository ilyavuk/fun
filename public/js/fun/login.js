$(function () {

    $(document).on("click", "#SignIn", function (e) {
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
        
        if(preventForm){
            $('#SignIn').after( '<div class="alert alert-danger" role="alert">Please correct errors and submit form again </div>');
        }else{
            $.ajax({
                type: "POST",
                url: 'admin/Login',
                data: $("#login_form").serialize(),
                success: function (result) {
                    console.log(result);
                    if(result === '1'){
                        window.location.href = rurl;                        
                    }else{
                        $('#SignIn').after( '<div class="alert alert-danger" role="alert">loggin has been failed</div>');
                    }
                }
            });
        }

    });


});