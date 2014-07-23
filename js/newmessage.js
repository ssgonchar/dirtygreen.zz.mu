var message_save_interval   = 30000;
var chat_description_text   = '';
var is_sending              = false;
var uploads                 = 0;

/**
 * Send message // Отправляет сообщение
 * @version 20120622, zharkov
 */
var chat_modal_send_message = function(object_alias, object_id, type)
{   
    if (uploads > 0) 
    {
        $('#post-message').val('Attaching...');
        is_sending = true;
        return;
    }    

    if (typeof object_alias == 'undefined')
    {
        object_alias = $('#newmessage_object_alias').val();
    }
    else
    {
        $('#newmessage_object_alias').val(object_alias);
    }
    
    if (typeof object_id == 'undefined')
    {
        object_id = $('#newmessage_object_id').val();
    }
    else
    {
        $('#newmessage_object_id').val(object_id);
    }
	//console.log(object_alias);
    var type            = (typeof type == 'undefined') ? 0 : type;    
    var chat_updater    = $('#chat-updater', window.opener.document).length > 0 ? true : false;
	//console.log(chat_updater);
	//return;
    var title = $('#chat-title').val();
    if (title.replace(/\s+/g, '').length == 0)
    {
        Message('Title must be specified !', 'warning');
        return;
    }
    
    var description = tinyMCE.get('chat-description').getContent();
    if (description.replace(/\s+/g, '').length == 0)
    {
        Message('Text must be specified !', 'warning');
        return;
    }

    var recipient   = '';
    var cc          = '';

    $('.chat-msg-relation').each(function(){
        if ($(this).val() == 'r')
        {
            recipient += $(this).attr('id').replace('user-', '').replace('-relation', '') + ',';
        }
        else if ($(this).val() == 'c')
        {
            cc += $(this).attr('id').replace('user-', '').replace('-relation', '') + ',';
        }
    });   
   
    if (recipient == '')
    {
        Message('Recipient must be specified !', 'warning');
        return;
    }    
    
    //$('.new-message-window').after('<div class="post-message-dle"><table style="height: 100%; width: 100%;"><tr><td><p style="font-size: 20px;">Posting message...</p></td></tr></table></div>')
    
    //@version 20130523, Sasha 
    $('#post-message').attr("class","btn100");
    $('#post-message').attr("disabled","disabled");
    $('#post-message').val('Posting...');

    $.ajax({
        url: '/chat/addmessage',
        data : {
            object_alias    : object_alias,
            object_id       : object_id,
            title           : title,
            description     : description,
            type            : type,
            recipient       : recipient,
            cc              : cc,
            alert           : $('#chat-sa').val(),
            pending         : $('#chat-p').val(),
            deadline        : $('#chat-deadline').val()
        },
        success: function(json){        
            if (json.result == 'okay') 
            {    
                window.opener.Message('Message was sent', 'okay');
                
                // add message to list  // если из блога, то сообщение добавляется в ленту
                if (chat_updater)
                {
                    //_modal_message_add_to_list(object_alias, json.content);
					//window.opener.console.log(object_alias);
					//window.opener.console.log(json.content);
                    window.opener._chat_message_add_to_list(object_alias, json.content);
                    window.opener.bind_tooltips();
                    window.opener.bind_prettyphoto();
                    window.opener.show_user_icons();
                }

                // save last message id //  сохраняет идентификатор последнего полученного сообщения
                user_last_chat_message_id               = window.opener.user_last_chat_message_id;
                window.opener.user_last_chat_message_id = json.message_id > user_last_chat_message_id ? json.message_id : user_last_chat_message_id;

                window.close();
            }
            else
            {
                Message(json.code, 'error');
            }                
        }
    });    
};

/**
 * onLoad
 */
$(function(){
   // var user_id = window.opener;
     //var chat_updater_t    = $('#chat-updater', window.opener.document).length > 0 ? true : false;
	//console.log(chat_updater_t);
     var path = window.location.pathname;
     var param_arr = path.split('/');
     //console.log(param_arr);
     var user_id = param_arr[3];
        chat_modal_select_user(user_id);
	$.ajaxSetup({type : "POST", async : false, dataType : 'json'});
    
    $('#app_messages').bind('click', function(){
            $('#app_messages').html('');
            $('#app_messages').toggle('slow');
        });
    set_timer_for_messages();
    
	
    var object_alias    = $('#qq_object_alias').length > 0 ? $('#qq_object_alias').val() : 'newmessage';
    var object_id       = $('#qq_object_id').length > 0 ? $('#qq_object_id').val() : 0;
	    
    var uploader = new qq.FileUploader({
        element         :   $('#fileuploader')[0],
        listElement     :   $('#attachments')[0],
        params          :   {object_alias : object_alias, object_id : object_id, template : 'text'},
        action          :   '/attachment/upload/',
        debug           :   false,
        template        :   '<div class="qq-uploader"><div class="qq-upload-button-grey">Attach Files</div></div>',
        classes         :   {
            button      : 'qq-upload-button-grey',
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
        onSubmit: function () {
            uploads++;
        },
        onComplete: function () {
            uploads--;
            
            if (is_sending && uploads == 0)
            {
                chat_modal_send_message();
            }
/*
            if (is_sent && uploader._handler._queue.length == 1)
            {
                is_download = false;
                chat_modal_send_message('', 0, 0);
            }    
*/
        },
    });
	/*
    $('div').bind('click', function(){
           console.log(this.id);
    });
	*/
    $('#chat-deadline').datepicker({
        showWeek: true,
		minDate: 0
    });
    
    bind_biz_autocomplete('#chat-biz');
    
    init_resize();
    
    var height = $('#message-modal-text').innerHeight() - $('#message-modal-text-params').innerHeight() - 5;

    chat_description_text = $('#chat-description').html();
    $('#chat-description').html('');
    
	var colortext = $('#font-color').val()
	
    tinyMCE.init({
        selector : '#chat-description',
        convert_urls : false,
        relative_urls : false,
        theme_advanced_path : false,
        theme_advanced_statusbar_location : "",
        paste_auto_cleanup_on_paste : true,         //
        /*paste_strip_class_attributes : "all",*/
        content_css : '/css/mce_advanced.css',
        theme : "advanced",
        height : height,
        plugins : "paste,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,sub,sup,|,image,media,emotions,link,unlink,charmap,hr,|,removeformat",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_buttons4 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        content_css : '/js/tiny_mce/themes/advanced/skins/default/content_email.css',
        template_external_list_url : "/js/template_list.js",
        external_link_list_url : "/js/link_list.js",
        external_image_list_url : "/js/image_list.js",
        media_external_list_url : "/js/media_list.js",        
        forced_root_block : '',
        theme_advanced_resize_horizontal : false,
        init_instance_callback: "init_instance_callback"	
			
    });

    bind_tooltips();
});

var init_instance_callback = function(){
    
    if ($.trim(chat_description_text) != '') {
        tinyMCE.activeEditor.setContent(chat_description_text + '<br><br>');
        
        tinyMCE.activeEditor.execCommand('SelectAll');
        tinyMCE.activeEditor.selection.collapse(false);
		
		
    }
	/*
		formats: {
			alignleft: {classes: 'left'},

			aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'center'},
			alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'right'},
			alignfull: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'full'},
			bold: {inline: 'span', 'classes': 'bold'},
			italic: {inline: 'span', 'classes': 'italic'},
			underline: {inline: 'span', 'classes': 'underline', exact: true},
			strikethrough: {inline: 'del'},
			customformat: {inline: 'span', styles: {color: '#00ff00', fontSize: '20px'}, attributes: {title: 'My custom format'}}
		}	
		*/
		/*
	tinyMCE.activeEditor.formatter.register('for-user-color', {
		selector: 'body',
		styles: {color: "'"+$("#font-color").val()+"'"}
	 });	
	 */
	 var colortext = $('#font-color').val();
	// console.log(tinyMCE);
	/*
	tinyMCE.activeEditor.formatter.register('mycustomformat', {
		selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
		classes : 'forecolor',
		styles : {color : colortext}
	});
	*/
	/*
	tinyMCE.activeEditor.formatter.register('mycustomformat', { 
		//forecolor : {inline : 'span', classes : 'forecolor', styles : {color : colortext}}
		forecolor : {inline : 'font', attributes : {color : colortext}},
	});
	*/
	tinyMCE.DOM.setStyles(tinyMCE.activeEditor.dom.select('body'), {'color' : colortext});
	tinyMCE.DOM.setStyles('chat-description_forecolor_preview', {'background-color' : colortext});
	//tinyMCE.activeEditor.formatter.apply('mycustomformat');	
	tinyMCE.activeEditor.focus();
	};


/**
 * onResize
 */
$(window).resize(function(){     
      
    init_resize();

    var h   = $(this).innerHeight() > 510 ? $(this).innerHeight() : 510;
    var h1  = $('#message-modal-text-params').innerHeight();
    var h2  = $('#newmessage-form-attachments').innerHeight();
    var h3  = $('#newmessage-form-buttons').innerHeight();

    h = h - h1 - h2- h3 - 40;
    
    var ed = tinymce.activeEditor;
    ed.theme.resizeTo(0, h);
}); 

/**
 * Resize tinymce onLoad
 */
var init_resize = function()
{
	var height = $(this).height()-15;
    $('#newmessage-form-text').css('height', height + "px");    
}

/**
 * Show biz select window // Показыввает окно выбора бизнеса
 * @version 20130324, zharkov
 */
var chat_modal_show_biz = function(event)
{
    if ($('#chat-message-biz-tip').is(':visible'))
    {
        chat_modal_hide_biz();
    }
    else
    {
        $('#chat-message-biz-tip').show();        
    }
};

/**
 * Hide biz select window // Прячет окно выбора бизнеса
 * @version 20130324, zharkov
 */
var chat_modal_hide_biz = function()
{
    $('#chat-message-biz-tip').hide();
    
    $('#chat-biz').val('');
    $('#chat-biz-id').val(0);
    
    $('#chat-message-biz-select').empty();
    $('#chat-message-biz-select').prepend($('<option value="0">--</option>'));
    
    $('#chat-biz').show();
    $('#chat-biz-id').show();
    
    $('#chat-message-biz-select').hide();  
	$('#chat_message_biz_select_chosen').hide();	
    
    $("#chat-message-team-select [value='0']").attr("selected", "selected");
};

/**
 * Select biz // Выбирает бизнес
 * @version 20130324, zharkov
 */
 var chat_modal_select_biz = function()
{
    if ($('#chat-message-biz-select').is(':visible') || $('#chat_message_biz_select_chosen').is(':visible'))
    {
        //biz_title   = $('#chat-message-biz-select > option:selected').text();
       // biz_title   = $("#chat-message-biz-select > span").text();
		//alert(biz_title);
		biz_title = $('.chosen-single > span').text();
		//console.log(biz_title);
		team_title  = $('#chat-message-team-select > option:selected').text() + '.';
    }
    else
    {
        biz_title   = $('#chat-biz').val();
        team_title  = '';
    }

    biz_title   = biz_title.substr(0, biz_title.indexOf(' '));
    title       = $('#chat-title').val();
    
    if (biz_title != '')
    {
        title = team_title + biz_title + ' : ' + title;        
        $('#chat-title').val(title);        
    }
    
    $('#chat-title').focus();
    
    chat_modal_hide_biz();    
}
/*
var chat_modal_select_biz = function()
{
    if ($('#chat-message-biz-select').is(':visible') || $('#chat-message-biz-select').is(':visible'))
    {
        biz_title   = $('#chat-message-biz-select > option:selected').text();
		team_title  = $('#chat-message-team-select > option:selected').text() + '.';
    }
    else
    {
        biz_title   = $('#chat-biz').val();
        team_title  = '';
    }

    biz_title   = biz_title.substr(0, biz_title.indexOf(' '));
    title       = $('#chat-title').val();
    
    if (biz_title != '')
    {
        title = team_title + biz_title + ' : ' + title;        
        $('#chat-title').val(title);        
    }
    
    $('#chat-title').focus();
    
    chat_modal_hide_biz();    
}
*/
/**
 * Устанавливает значение состояния переключателя
 * @version 20120622, zharkov
 */
var _chat_modal_set_switch_state = function(object, state)
{
    if (object == 'pending')
    {
        $('#chat-p').val(state);
        $('#chat-p-switch').attr('src', (state == 1 ? '/img/icons/pendingon.png' : '/img/icons/pendingoff.png'));        
    }
    else if (object == 'sound')
    {
        $('#chat-sa').val(state);
        $('#chat-sa-switch').attr('src', (state == 1 ? '/img/icons/soundon.png' : '/img/icons/soundoff.png'));        
    }    
};

/**
 * Переключает признак сообщения "pending"
 * @version 20120622, zharkov
 */
var chat_modal_switch_pending = function()
{
    if ($('#chat-p').val() == 0)
    {
        _chat_modal_set_switch_state('pending', 1);
    }
    else
    {
        _chat_modal_set_switch_state('pending', 0);
    }    
};

/**
 * Переключает признак сообщения "звуковое"
 * @version 20120622, zharkov
 */
var chat_modal_switch_sound_alert = function()
{
    if ($('#chat-sa').val() == 0)
    {
        _chat_modal_set_switch_state('sound', 1);
    }
    else
    {
        _chat_modal_set_switch_state('sound', 0);        
    }    
};

/**
 * Fill biz list for chat message
 * @version: 20120710, novikov
 */
var chat_fill_biz_select = function(team_id)
{
    var selector = '#chat-message-biz-select';
    
    if (team_id > 0)
    {
        $(selector).prepend($('<option selected="" value="0">loading...</option>')); 
        
        error = false;                
        $.ajax({
            url: '/team/getbiz',
            data : {
                team_id : team_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    _modal_fill_select(selector, json.list, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });
        
        $('#chat-biz').hide();
        $('#chat-biz-id').hide();
        
        $('#chat-message-biz-select').show();
		$('#chat-message-biz-tip').height(400);
		/*$('#chat-message-biz-select').chosen({no_results_text:'No search results for '});
		$("#chat-message-biz-select").trigger("chosen:updated");
		$('#chat-message-biz-select').hide();
		$('#chat_message_biz_select_chosen').show();*/
    }
    else
    {
        $('#chat-biz').show();
        $('#chat-biz-id').show();
        
        $('#chat-message-biz-select').hide();
    }

    if (team_id == 0 || error)
    {
        $(selector).empty();
        $(selector).prepend($('<option value="0">--</option>'));        
    }
    
};

/**
 * Init object tooltips
 * @version: 20130410, zharkov
 */
var bind_tooltips = function()
{
    $('.tooltip').tooltip({ 
        track: true, 
        delay: 0, 
        showURL: false, 
        showBody: " || ", 
        fade: 250,
        left: 0
    });
}

/**
 * Select recipient in chat modal window
 */
var chat_modal_select_user = function(user_id)
{
    var relation = $('#user-' + user_id + '-relation').val();

    if (relation == '')
    {
        relation = 'r';
        
        $('#user-' + user_id + '-cc').hide();
        $('#user-' + user_id + '-re').show();
    }
    else if (relation == 'r')
    {
        relation = 'c';
        
        $('#user-' + user_id + '-cc').show();
        $('#user-' + user_id + '-re').hide();        
    }
    else
    {
        relation = '';
        
        $('#user-' + user_id + '-cc').hide();
        $('#user-' + user_id + '-re').hide();        
    }
    
    $('#user-' + user_id + '-relation').val(relation);
};

/**
 * Назначает элементу вода бизнеса с классом biz-autocomplete функцию автозаполнения
 * для работы должно быть два поля: {text id="biz-title" class="biz-autocomplete"} и {hidden id="biz-title-id"}
 * у hidden поля должен быть id такой же как у text с суффиксом "-id"
 * 
 * @version 20120722, zharkov
 */
var bind_biz_autocomplete = function(biz_selector, callback_function)
{
    biz_selector = biz_selector || '.biz-autocomplete';

    if ($(biz_selector).length == 0) return;
        
    $(biz_selector).each(function(){        
        
        obj_id      = $(this).attr('id');
        title_field = $(this).data('titlefield') || 'doc_no_full';

        // предотвращает пост формы при нажатии Enter в поле
        $(this).keypress(function(event){
            if(event.keyCode == 13) 
            {
                return false;
            }
        });
        
        $(this).autocomplete({
            source: function( request, response ) {
                
//                obj_id = $(this).attr('id');
                $('#' + obj_id + '-id').val(0);                
                
                $.ajax({
                    url     : "/biz/getlistbytitle",
                    data    : {
                        maxrows     : 250,
                        title       : request.term,
                        title_field : title_field
                    },
                    success : function( data ) {
                        response( $.map( data.list, function( item ) {
                            return {
                                label: item.biz.list_title,
                                value: item.biz.id
                            }
                        }));
                    }
                });
                
            },
            minLength: 3,
            delay: 500,
            select: function( event, ui ) {
                
                obj_id = $(this).attr('id');
                if (ui.item)
                {
                    $(this).val(ui.item.label);
                    $('#' + obj_id + '-id').val(ui.item.value);
                }
                else
                {
                    $('#' + obj_id + '-id').val(0);
                }
                
                if (callback_function) callback_function(ui.item.value);

                return false;
                            
            },
            open: function() { 
                
                if ($('.ui-autocomplete > li').length > 20)
                {
                    $(this).autocomplete('widget').css('z-index', 1002).css('height', '200px').css('overflow-y', 'scroll');
                }
                else
                {
                    $(this).autocomplete('widget').css('z-index', 1002);
                }
                
                return false;                
            },
            close: function() { },
            focus: function(event, ui) 
            { 
                return false;
            }        
        });    

    });
        
}

/**
 * Shows system message
 */
function Message(message, type)
{
    $('<a class="' + type + '">' + message + '</a>').appendTo('#app_messages'); 
    $('#app_messages').show();  
    set_timer_for_messages();
};

/**
 * Устанавливает таймер на показ сообщений и прячет блок сообщений если сообщений нет
 */
function set_timer_for_messages(){
    
    if ($('#app_messages .okay').length > 0) 
    {
        $('#app_messages .okay')
        .delay(3000)
        .queue(function(){
            $(this).remove().dequeue();            
            if ($("#app_messages").children().length == 0) $('#app_messages').hide();
        });
    }
    
    if ($('#app_messages .warning').length > 0) 
    {
        $('#app_messages .warning')
        .delay(6000)
        .queue(function(){
            $(this).remove().dequeue();            
            if ($("#app_messages").children().length == 0) $('#app_messages').hide();
        });
    }    
};

/**
 * Удаляет атачмент
 * 20120710, zharkov : перенесена из dropbox_index.js
 */
var remove_attachment = function(object_alias, object_id, attachment_id)
{
    if (!confirm('Remove attachment ?')) return;

    $.ajax({
        url: '/attachment/remove',
        data : {
			object_alias	: object_alias, 
			object_id		: object_id,
            attachment_id 	: attachment_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#attachment-' + attachment_id).remove();
                $('#attachment-' + attachment_id + '-external').remove();
				$('#attachment-remove-' + attachment_id).remove();
            }
            else
            {
                Message(json.message, 'error');
            }            
        }
    });
};

/**
 * Fill <select> element with data
 */
var _modal_fill_select = function(id, json_arr, first_option)
{
    $(id).empty();
    $(id).prepend($('<option value="' + first_option.value + '">' + first_option.name + '</option>'));
    
    start_index = 0;
    for (i = start_index; i < json_arr.length; i++)
    {
        el = json_arr[i];
        $(id).append($('<option value="' + el.id + '">' + el.name + '</option>'));
    }    
};
