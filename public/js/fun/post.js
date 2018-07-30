$(function(){

	var emojioneAreas =  $(".emojioneAreas").emojioneArea();

	
    $(document).on('click', '.saveComment', function (e) {
        e.preventDefault();

        var Content = $(this).prev().children('.comment_field').val();
        var ID = $(this).prev().children('.comment_field').attr('data-id');

        if(Content.trim() == '') {
            // alert('Is empty');
            return false;
        }

        var data = "&addComments=1";
		data += "&Content="+encodeURIComponent(Content);
		data += "&PostID="+ID;
		$.ajax({
			type: "POST",
			dataType: "json",
			url: '/save-comment',
			data: data,
			success: function(R){
				console.log(R);
				location.reload();
			}
		});
    });

    

});