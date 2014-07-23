/**
 * Очищает результаты поиска
 */
var email_clear_search_results = function()
{
    $('#keyword').val('');
    $('#email-co-search-result').empty();
};

/**
 * Заполняет данные письма при выборе первого получателя
 * @version 20120803, zharkov
 */
var email_fill_data_by_address = function(contactdata_id)
{
    
    return false;
};

/**
 * Переключает подпись
 * @version 20120803, zharkov
 */
var email_select_signature = function(obj)
{
    sender_address = $('#mailbox_id > option:selected').text();
    sender_address = sender_address.split("@");
    
    if (sender_address[1] == 'steelemotion.com')
    {
        $('#email-signature-pa1').hide();
        $('#email-signature-pa2').hide();
        $('#email-signature-se1').show();
        $('#email-signature-se2').show();
        $('#email-signature2').show();
    }
    else if (sender_address[1] == 'platesahead.com')
    {
        $('#email-signature-pa1').show();
        $('#email-signature-pa2').show();
        $('#email-signature-se1').hide();
        $('#email-signature-se2').hide();
        $('#email-signature2').show();
    }
    else
    {
        $('#email-signature-pa1').hide();
        $('#email-signature-pa2').hide();
        $('#email-signature-se1').hide();
        $('#email-signature-se2').hide();
        $('#email-signature2').val('');
        $('#email-signature2').hide();
    }
};

/**
 * показывает строку доавления СС получателей письма
 * @version 20120803, zharkov
 */
var email_show_cc = function()
{
     $("#email-add-cc-link").hide();
     $("#email-add-cc-input").show();
}

var email_show_bcc = function()
{
     $("#email-add-bcc-link").hide();
     $("#email-add-bcc-input").show();
}


$(function(){
   
    var uploader_object_alias   = $('#uploader_object_alias') ? $('#uploader_object_alias').val() : 'newemail';
    var uploader_object_id      = $('#uploader_object_id') ? $('#uploader_object_id').val() : 0;
    
    var uploader = new qq.FileUploader({
        element         :   $('#fileuploader')[0],
        listElement     :   $('#attachments')[0],
        params          :   {object_alias : uploader_object_alias, object_id : uploader_object_id, template : 'text'},
        action          :   '/attachment/upload/',
        debug           :   false,
        dragDrop        :   false,        
        template        :   '<div class="qq-uploader"><div class="qq-upload-button-text-plus-small">Add Files</div></div>',
        classes         :   {
            button      : 'qq-upload-button-text-plus-small',
            drop        : 'qq-upload-drop-area',
            dropActive  : 'qq-upload-drop-area-active',
            list        : 'qq-upload-list',                        
            file        : 'qq-upload-file',
            spinner     : 'qq-upload-spinner',
            size        : 'qq-upload-size',
            cancel      : 'qq-upload-cancel',
            success     : 'qq-upload-success',
            fail        : 'qq-upload-fail'            
        },
        fileTemplate    :   '<li>' +
                                '<span class="qq-upload-file"></span>' +
                                '<span class="qq-upload-spinner"></span>' +
                                '<span class="qq-upload-size"></span>' +
                                '<a class="qq-upload-cancel" href="#">Cancel</a>' +
                                '<span class="qq-upload-failed-text">Error !</span>' +
                            '</li>',
    });

    $(".email-recipient").autocomplete({
        source: function( request, response ) {
            
            keyword = request.term;

            if (keyword.indexOf(",") != -1)
            {
                keyword = keyword.replace(/.*,/gi, "");
            }
            
            if (keyword.indexOf("\"") != -1 || keyword.indexOf("<") != -1 || keyword.indexOf(">") != -1) return;           
            
            
            $.ajax({
                url     : "/email/getrecipients",
                data    : {
                    maxrows : 25,
                    keyword : keyword
                },
                success : function( data ) {
                    response( $.map( data.list, function( item ) {
                        return {
                            label   : item.title,
                            value   : item.id,
                            company : (item.company ? item.company.title : ''),
                            person  : (item.person ? item.person.full_name : '')
                        }
                    }));
                }
            });
            
        },
        minLength: 3,
        select: function( event, ui ) {
            
            if (ui.item)
            {
                text = $(this).val();
                
                if (text.indexOf(",") == -1)
                {
                    text = ui.item.label + ', ';

                    if ($(this).attr('id') == 'recipients')
                    {
                        $('#email-company').val(ui.item.company);
                        $('#email-person').val(ui.item.person);
                    }
                }
                else
                {
                    subtext = text.substr(text.lastIndexOf(','));
                    text    = text.replace(subtext, ', ' + ui.item.label + ', ');                    
                }                
                
                $(this).val(text);
            }
            
            return false;
            
        },
        open: function() {
            
        },
        close: function() {

        },
        
        focus: function(event, ui) {

            return false;
        }
    });
    
    $(".email-recipient").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });


	tinymce.init({
		selector : '#email_text',
		plugins: [
		    "advlist autolink lists link image charmap print preview anchor",
		    "searchreplace visualblocks code fullscreen",
		    "insertdatetime media table contextmenu paste textcolor"
		],
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontselect | fontsizeselect | forecolor | backcolor"
	});
});


/**
 * Показывает окно поиска и выбора компаний для бизнеса
 */
var show_email_co_list = function()
{
    $('#email-co-select').show();
};

/**
 * Прячет окно выбора компаний для бизнеса
 */
var close_email_co_list = function()
{
    $('#email-co-select').hide();
};

/**
 * Ищет компании
 */
var find_email_objects = function(title)
{
    if (title.replace(/\s+/g, '').length < 3) return;

    var type_alias = $('#email-co-type-alias').val();
    
    if (type_alias == '') return;

    $('#email-co-search-result').empty();
    $('#email-co-search-result').append($('<option value="0" style="font-style: italic;">loading...</option>'));
    
    $.ajax({
        url: '/email/getobjectslist',
        data : {
            type_alias : type_alias,
            title : title
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#email-co-search-result').empty();
                if (json.list && json.list.length > 0)
                {
                    $.each(json.list, function(key, item){
                        var object  = item[type_alias];
                        var name    = type_alias == 'biz' || type_alias == 'order' ? object.doc_no_full : object.doc_no;
                        $('#email-co-search-result').append($('<option value="' + object.id + '">' + name + '</option>'));
                    });
                }                
                else
                {
                    $('#email-co-search-result').append($('<option value="0" style="font-style: italic;">nothing was found</option>'));
                }
            }
            else
            {
                error = true;
            }                
        }
    });    
};

/**
 * Добавляет найденные компании в бизнес
 */
var add_email_object = function()
{
    var object_alias   = $('#email-co-type-alias').val();
    if (object_alias == '') return;

    $('#email-co-search-result > option:selected').each(function(){        
        var object_id = $(this).val();
        
        if (object_id > 0 && $('#' + object_alias + '-' + object_id).html() == null)
        {           
            object_title    = $(this).text();            
            new_row         = '<span id="' + object_alias + '-' + object_id +'" style="margin-right: 10px;">' + 
                                '<input type="hidden" name="objects[' + object_alias + '-' + object_id + ']" class="' + object_alias + '-object" value="' + object_id + '"><a class="tag-' + object_alias + '" style="vertical-align: top; margin-right: 3px;" href="/' + object_alias + '/' + object_id + '" target="_blank">' + object_title + '</a></td><td width="20px"><img src="/img/icons/cross-small.png" onclick="remove_email_object(\'' + object_alias + '\', ' + object_id + ');"></span>';
            //alert(new_row);return false;
            $(new_row).appendTo('.email-co-objects-list');
            
        }
        close_email_co_list();
    });    
};
/**
 * Удаляет объект из списка компаний 
 */
var remove_email_object = function(object_alias, object_id)
{
    var element = $('#' + object_alias + '-' + object_id);
    
    if (!confirm('Remove ' + element.children('a').html() + ' ?')) return;
    
    element.remove();
};
