/**
 * OnLoad
 */
$(function(){

    var object_alias    = $('#qq_object_alias') ? $('#qq_object_alias').val() : 'qq';
    var object_id       = $('#qq_object_id') ? $('#qq_object_id').val() : 1;

    var uploader = new qq.FileUploader({
        element         :   $('#fileuploader')[0],
        listElement     :   $('#photolist')[0],
        params          :   {object_alias : object_alias, object_id : object_id},
        action          :   '/attachment/upload/',
        debug           :   false,
        template        :   '<div class="qq-uploader"><div class="qq-upload-button">Upload</div></div>',
        fileTemplate    :   '<li>' +
                                '<span class="qq-upload-file"></span>' +
                                '<span class="qq-upload-spinner"></span>' +
                                '<span class="qq-upload-size"></span>' +
                                '<a class="qq-upload-cancel" href="#">Cancel</a>' +
                                '<span class="qq-upload-failed-text">Error !</span>' +
                            '</li>',
        onComplete     : function(id, fileName, result){
            $('#no-photolist').hide();
            bind_prettyphoto();
        },
    });

});

/**
 * Показывает блок управления картинкой
 */
var show_attachment_block = function(obj, type, attachment_id, is_main)
{
    $('#attachment-' + attachment_id + '-actions').show();
    
    $(obj).mouseleave(function(){
        $('#attachment-' + attachment_id + '-actions').hide();
    });
};

/**
 * Устанавливает картинку как главную
 */
var set_as_main = function(attachment_id)
{
    $.ajax({
        url: '/attachment/setasmain',
        data : {
            attachment_id : attachment_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('.attachment-block-main').addClass('attachment-block').removeClass('attachment-block-main');
                $('#attachment-' + attachment_id).addClass('attachment-block-main');
                
                $('.attachment-ismain').show();
                $('#attachment-' + attachment_id + '-ismain').hide();
            }
            else
            {
                Message(json.message, 'error');
            }            
        }
    });
};