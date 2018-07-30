$(function(){

    $(document).on('change', '#avatarUpload', function (e) {
        var file = document.getElementById('avatarUpload').files[0];
        handleUploads( file, '/upload/uploadavatar', handleAvatar );
    });

});


function validate(str, re) {
	return re.test(str);
}

/**
 * Activate Tooltip
 * 
 * @param object Obj 
 * @param string Msg 
 * @param string Position 
 */
function activateTooltip(Obj, Msg, Position){

    if( typeof Obj.attr( 'data-toggle' ) !== 'undefined'){
        
        Obj.tooltip("enable");
        Obj.tooltip("show");
        return;
    }
    Obj.attr({
        'data-toggle' : 'tooltip',
        'data-placement' : Position,
        'title' : Msg
    });
    Obj.tooltip("show");

}

/**
 * Deactivate Tooltip
 * 
 * @param object Obj 
 */
function deactivateTooltip(Obj){
    Obj.tooltip("disable");
}

function handleAvatar(data){
    var img = $('<img class="img-fluid img-thumbnail" alt="Responsive image" src="/img/' + data.img + '/150"  >');
    var imgLoaderHeight = 100;
    var imgLoaderWidth = Math.round((imgLoaderHeight / data.height) * data.width);
    var imgLoader = $('<div class="img-loader"></div>');
    imgLoader.css({
        'height' : imgLoaderHeight,
        'width' : imgLoaderWidth
    });

    $('.uploadedImages').html(imgLoader);

    img.on('load', function () {
        imgLoader.replaceWith(img);
    });

    $('#avicon').attr('src', '/img/' + data.img + '/25');
}



function handleUploads(file, url, handleFinish) {
    var formData = new FormData();
    formData.append('file', file);

    var innerEl = $("<div class='upInside'></div>");
    var outerEl = $("<div class='upOutside'></div>");
    outerEl.append(innerEl);
    $(".dropUploadMulti").append(outerEl);

    $.ajax({
        url: url,
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

            handleFinish( data );
            
        }
    });
}

