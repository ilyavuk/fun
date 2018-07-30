$(function () {

    $(document).on('click', '.remove-item', function (e) {
        e.preventDefault();
        var delConfirm = confirm("Are you sure you wanna to delete post");

        if (delConfirm) {
            var dataid = "&removePostID=" + $(this).attr('data-id');
            var thisItem = $(this);
            $.ajax({
                type: "POST",
                url: '/admin/DeletePost',
                data: dataid,
                success: function (result) {
                    if (result === '1') {
                        thisItem.parents('tr').remove();
                    }
                }
            });
        }
    });

    $(document).on('click', '.open-edit, #NewPost', function (e) {
        e.preventDefault();
        if ($(this).attr('id') === 'NewPost') {
            var dataid = null;
        } else {
            var dataid = $(this).attr('data-id');
        }
        $('<form id="go-on" action="/admin/create_post" method="POST" style="display:none;"><input type="hidden" name="PostID" value="' + dataid + '"></form>').appendTo('body');
        $('#go-on').submit();
    });

    $(document).on('click', '#SavePost', function (e) {
        e.preventDefault();

        $('.form-group').removeClass('has-error');
        $('.alert-danger').remove();
        $('.help-block').remove();

        var url = $('input[name="Url"]').val();
        if(url === ''){
            var title = $('input[name="Title"]').val();
            title = title.replace(/\W+/g,"_");
            $('input[name="Url"]').val( title+'.html' );
        }

        $.each($('.upFinish'), function (i, v) {

            var img = $(v).attr('data-img');
            var display = ($(v).find('.displayCh').prop("checked")) ? 1 : 0;
            $('#create_post').prepend('<input type="hidden" name="imgPath[]" value="' + img + '"><input type="hidden" name="imgVisible[]" value="' + display + '">');

        });

        var RequiredFields = {
            'Title': { 'type': 'input', 're': /^.{5,250}$/i, 'error': 'The field name is not valid. min chars is 5 and max is 250' },
            'Url' : { 'type': 'input', 're': /^[\w]{5,195}\.html$/i, 'error': 'the field is not valid must be words between 5 and 200 chars and ending with .html' }
        };

        var preventForm = false;

        $.each(RequiredFields, function (i, v) {
            if (!validate($(v.type + '[name="' + i + '"]').val(), v.re)) {
                $(v.type + '[name="' + i + '"]').parents('.form-group').addClass('has-error');
                $(v.type + '[name="' + i + '"]').parents('.form-group').append('<span class="help-block">' + v.error + '</span>');
                preventForm = true;
            }
        });

        // decode html
        $('textarea[name="Desc"]').val(encodeURIComponent($('textarea[name="Desc"]').val()));

        if (!preventForm) {
            $.ajax({
                type: "POST",
                url: '/admin/CreatePost',
                data: $("#create_post").serialize(),
                success: function (result) {
                    console.log(result);
                    if (result === '1') {
                        window.location.href = "/admin/my_posts";
                    }
                }
            });
        }

    });


    $(document).on('click', '.rm_ask_img', function (e) {
        var img = $(this).parent('.upFinish').attr('data-img');
        $(this).parent('.upFinish').remove();
    });

    $('.desctext').summernote({
        placeholder: 'Description',
        tabsize: 20,
        height: 100
    });

    $(document).on('change', '#singleUpload', function (e) {
        var file = document.getElementById('singleUpload').files[0];
        handleUploads(file, '/upload/post', handleFinish);
    });

    var obj = $("#dropImage");
    $('.uploadedImages').sortable();

    obj.on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css({
            'border': '2px dotted #0B85A1',
            'background-color': '#cadace'
        });
    });

    obj.on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    obj.on('drop', function (e) {
        e.preventDefault();
        $(this).css({
            'border': '2px dotted #0B85A1',
            'background-color': 'white'
        });

        var files = e.originalEvent.dataTransfer.files;
        $('#dropUploadMultiPlaceholder').remove();


        for (var i = 0; i < files.length; i++) {
            handleUploads(files[i], '/upload/post', handleFinish);
        }

    });

    document.onpaste = function (event) {
        var items = (event.clipboardData || event.originalEvent.clipboardData).items;
        var blob = null;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf("image") === 0) {
                var imgfile = items[i].getAsFile();
                blob = imgfile;
                handleUploads(imgfile, '/upload/post', handleFinish);
            }
        }
        // load image if there is a pasted image
        if (blob !== null) {
            var reader = new FileReader();
            reader.onload = function (event) {
                console.log(event.target.result); // data url!
            };
            reader.readAsDataURL(blob);
        }
    }

});

function handleFinish(data) {
    var img = $('<img class="has-loaded" src="/img/' + data.img + '/100"  >');
    var imgLoaderHeight = 100;
    var imgLoaderWidth = Math.round((imgLoaderHeight / data.height) * data.width);

    var imgLoader = $('<div class="img-loader"></div>');
    imgLoader.css({
        'height': imgLoaderHeight,
        'width': imgLoaderWidth
    });
    var upFinish = $('<div class="upFinish" data-img="' + data.img + '" ><span class="rm_ask_img fa fa-remove"></span><div class="sm_img_panel"><div class="form-check"><input class="form-check-input displayCh" type="checkbox" value=""><label class="form-check-label" for="defaultCheck1">Display</label></div></div></div>');
    upFinish.prepend(imgLoader);

    $('.uploadedImages').prepend(upFinish);

    img.on('load', function () {
        imgLoader.replaceWith(img);
    });
}