var UploadedImgs = {};

$(function () {
    /**
     * @author ilya
     */
 

    $('#uploadedImages').sortable();

    // Handle files after being uploaded
    var obj = $("#dropUploadMulti");


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
            handleUploads(files[i]);
        }

    });

    /**
     * need better need better refactoring, working version
     */
    document.onpaste = function (event) {
        var items = (event.clipboardData  || event.originalEvent.clipboardData).items;
        var blob = null;
        for (var i = 0; i < items.length; i++) {
          if (items[i].type.indexOf("image") === 0) {
            var imgfile = items[i].getAsFile();
            blob = imgfile;
            handleUploads(imgfile);
          }
        }
        // load image if there is a pasted image
        if (blob !== null) {
          var reader = new FileReader();
          reader.onload = function(event) {
            console.log(event.target.result); // data url!
          };
          reader.readAsDataURL(blob);
        }
    }    
    /**
     * If the files are dropped outside the div, file is opened in the browser window. To avoid that we can prevent ‘drop’ event on document.
     */

    $(document).on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
        obj.css({
            'border': '2px dotted #0B85A1'
        });
    });
    $(document).on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $(document).on('click', '.rm_ask_img', function (e) {
        var img = $(this).parent('.upFinish').attr('data-img');
        $(this).parent('.upFinish').remove();
        delete UploadedImgs[img];

    });

    $(document).on('keyup', '#Content, #Title', function (e) {
        e.preventDefault();        
        if(e.which == 13) {
            searchData();
        }
    });

    $(document).on('contextmenu', '.bigger-img', function (e) {
        e.preventDefault(); 
        var url = $(this).find('img').attr('src');
        url = url.replace(/\d+$/, 1500);
        window.open(url, "PicWin", "width=1500,height=1200");
        console.log(url);
    });

    $(document).on('click', '.remove-item', function (e) {
        e.preventDefault();
        var answer = confirm("Are you sure you wanna to delete this item?");
        var id = $(this).attr('data-id');
        if(answer){
            var data = "&del_id="+id;
            $.ajax({
                type: "POST",
                url: '/ask/delete',
                data: data,
                success: function (result) {
                    searchData();
                }
            });           
        }
    });    
    /**
     * Save, update
     */
    $(document).on('click', '#save, #update_edit', function (e) {
        e.preventDefault();

        var whereToGo = "/ask/save";
        var edit = false;
        if($(this).attr('id') == 'update_edit'){
            edit = true;
            whereToGo = "/ask/update";
        }

        var Title = $('#Title').val();
        if (Title === '') {
            activateTooltip($('#Title'), 'The field is empty', 'top');
            return false;
        }
        deactivateTooltip($('#Title'));

        var Content = $('#Content').val();
        if (Content === '') {
            activateTooltip($('#Content'), 'The field is empty', 'top');
            return false;
        }

        deactivateTooltip($('#Title'));

        $('input[name="img[]"]').remove();

        $.each($('.upFinish'), function (i, v) {
            $('#form_ask_save').append('<input type="hidden" name="img[]" value="' + $(v).attr('data-img') + '" >');
        });

        $.ajax({
            type: "POST",
            url: whereToGo,
            data: $("#form_ask_save").serialize(),
            success: function (result) {
                $('#dropUploadMulti, #uploadedImagesErr, #uploadedImages').html('');
                $('#dropUploadMulti').html('<p id="dropUploadMultiPlaceholder">Drop images here</p>');
                $('#Title').val('');
                $('#Content').val('');
                var div = $('<div class="alert alert-success"><strong>Success!</strong> You have been successfully saved data</div>');
                $('#main_ask').append(div);
                div.fadeOut("slow");
                if(edit){
                    closeOpenedWindow();                   
                }
            }
        });
        return false;
    });


    $(document).on('click', '#search', function (e) {
        e.preventDefault();
        searchData();
    });


    $(document).on('click', '.open-in-popup', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        winOpen( id );
    });
    
    
    $(document).on('mouseover', '.pzoom', function (e) {
        e.preventDefault();
        $('.bigger-img').remove();
        var imgSource = $(this).attr('src');
        imgSource = imgSource.replace(/\d+$/, "500");
       
        var divBigger = $("<div class='bigger-img'></div>");

        var imgLoader = $('<div class="img-loader"></div>');
        imgLoader.css({
            'height' : 100,
            'width' : 100
        });
           
        divBigger.append(imgLoader);
        $(this).parent('.img-sml').append(divBigger);
        var img = $('<img  src="'+imgSource+'"  >');
        img.on('load', function () {
            $('.bigger-img').css({
                'top' : '-490px'
            });
            imgLoader.replaceWith(img);
        });
    });

    $(document).on('mouseleave', '.img-sml', function (e) {
        e.preventDefault();
        $('.bigger-img').remove();
    });

});

function searchData(){
    var Title = $('#Title').val();
    var Content = $('#Content').val();
    
    if (Title === '' && Content === '') {
        var msg = "The both fields are empty";
        activateTooltip($('#Title'), msg, 'top');
        activateTooltip($('#Content'), msg, 'top');
        return false;
    }

    deactivateTooltip($('#Title'));
    deactivateTooltip($('#Content'));

    $.ajax({
        type: "POST",
        url: "/ask/search",
        data: $("#form_ask_save").serialize(),
        dataType: "json",
        success: function (result) {
            
            if(result){                    
                var html = ''; 
                html += '<div class="row">'; 
                html += '<div class="col-md-4">Title</div>'; 
                html += '<div class="col-md-6">Desc</div>'; 
                html += '</div>'; 

                $.each(result, function( i, v ) {
                    html += '<div class="row no-pad">'; 
                    html += '<div class="col-md-4">'; 
                    html += '<input type="text" class="form-control" name="Title1" value="'+v.Title+'">'; 
                    html += '</div>'; 
                    html += '<div class="col-md-7">'; 
                    html += '<textarea name="Content1" class="form-control" rows="1">'+v.Content+'</textarea>'; 
                    html += '</div>'; 

                    html += '<div class="col-md-1">'; 
                    html += '<i class="open-in-popup fa fa-fw fa-edit fasize-3" data-id="'+v.id+'" ></i>'; 
                    html += '<i class="remove-item fa fa-fw  fa-remove fasize-3" data-id="'+v.id+'" ></i>';
                    html += '</div>';

                    html += '</div>'; 
                    html += '<div class="row ">'; 
                    html += '<div class="col-md-12">'; 
                    $.each(v.Img, function( i2, v2 ) {                                                        
                        html += '<picture class="img-sml"><img class="pzoom img-thumbnail" src="/img/'+v2+'/50"></picture>';                                                                                     
                    });
                    html += '</div>'; 
                    html += '</div>';
                });

                $('#results2005').html(html);

            }else{
                $('#results2005').html('');
            }
        }
    });
}

function handleUploads(file) {
    var formData = new FormData();
    formData.append('file', file);

    var innerEl = $("<div class='upInside'></div>");
    var outerEl = $("<div class='upOutside'></div>");
    outerEl.append(innerEl);
    $("#dropUploadMulti").append(outerEl);

    $.ajax({
        url: '/ask/upload',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function (e) {

                    if (e.lengthComputable) {
                        /**
                         * AppendDiv
                         */
                        var max = e.total;
                        var current = e.loaded;
                        var Percentage = parseInt((current * 100) / max);

                        innerEl.css(
                            "width", Percentage + '%'
                        );
                        innerEl.html(Percentage + "%");

                        if (Percentage >= 100) {

                        }
                    }

                }, false);
            }
            return myXhr;
        },
        success: function (data) {


            // replace with nothing :)
            outerEl.replaceWith('');

            if (/^Err:/.test(data)) {
                $('#uploadedImagesErr').prepend('<p>' + data + '</p>');
            } else {
                var img = $('<img class="has-loaded" src="/img/' + data.img + '/100"  >');
                var imgLoaderHeight = 100;
                var imgLoaderWidth = Math.round((imgLoaderHeight / data.height) * data.width);

                var imgLoader = $('<div class="img-loader"></div>');
                imgLoader.css({
                    'height' : imgLoaderHeight,
                    'width' : imgLoaderWidth
                });
                var upFinish = $('<div class="upFinish" data-img="' + data.img + '" ><span class="rm_ask_img fa fa-remove"></span></div>');
                upFinish.prepend(imgLoader);

                $('#uploadedImages').prepend(upFinish);

                img.on('load', function () {
                    imgLoader.replaceWith(img);
                });

                // UploadedImgs[data] = data;
            }

        }
    });
}


function winOpen(id){
    window.open("/ask?edit="+id, "MsgWindow", "width=1200,height=1200");
}

function closeOpenedWindow() {
    // window.opener.location.reload(true);
    window.opener.searchData();
    window.close();
    searchData();
}
