//глобальная переменная - хранит id последнего заказа (для функции get_orders_last_id())
var order_last_id;
//document.ready function 
$(function()
{
    /**
     * запрещает ввод веса позиции пользователем
     * стирает значения веса unitweight и weight если хоть один параметр не введен
     * @version 20140619, Uskov
     */
    //селекторы
    var thickness = $(".thickness-input");
    var width = $(".width-input");
    var length = $(".length-input");
    var unitweight = $(".unitweight-input");
    var qtty = $(".qtty-input");
    var weight = $(".weight-input");
    //делаем неактивными поля unitweight и weight
    unitweight.attr("readonly", true);
    weight.attr("readonly", true);
    //стираем значения веса unitweight и weight если хоть один параметр не введен
    //толщина
    thickness.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){
            $("#unitweight-" + this_id).val("");
            $("#weight-" + this_id).val("");
        }
    });
    //ширина
    width.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){
            $("#unitweight-" + this_id).val("");
            $("#weight-" + this_id).val(""); 
        }
    });
    //длина
    length.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ 
            $("#unitweight-" + this_id).val("");
            $("#weight-" + this_id).val(""); 
        }
    });
});

/*
 * Получает id последнего заказа из таблицы orders
 */
var get_orders_last_id = function()
{
    var json_data = $.ajax({
        url     : '/order/getorderslastid',

        success: function(json){
            //если только открыли страницу - записываем id последнего заказа в глобальную переменную
            if(typeof(order_last_id) == "undefined"){
                order_last_id = json.object;
            }
            else if(json.object > order_last_id){
                //сообщаем о новом заказе
                alert("We have a new order! (" +json.order_info.customer+ ", BIZ " +json.order_info.biz_id+ ", order for '" +json.order_info.order_for+ "', qtty = " +json.order_info.qtty+ ", value = " +json.order_info.value+ " " +json.order_info.currency+ ")");
                //обновляем id последнего заказа
                order_last_id = json.object;
            }
        }
    });
}

// Задержка выполнения ajax-запроса для safari, chrome для показа блока loading
var webkit_timeout          = 100;

var pinger_start_time       = 60000; // 60 sec from load
var pinger_refresh_time     = 60000; // every 60 sec

var qq_uploader             = {};


/**
 * Инициализирует prettyPhoto
 * 
 * @version 20120901, zharkov
 */
var bind_prettyphoto = function()
{
    $("a[rel^='prettyPhoto']").prettyPhoto({social_tools: false});
}


/**
 * Добавляет позиции и айтемы в сертификат качества
 * 
 * @version 20120809, zharkov
 */
var put_positions_to_qc = function(qc_id)
{
    var stock_id        = $('#stock').val();
    var position_ids    = '';
    var item_ids       = '';

    // selected positions
    $('.cb-row-position:checked').each(function(){
        position_ids += '' + (position_ids != '' ? ',' : '') + $(this).val();
    });

    // selected items
    $('.cb-row-item:checked').each(function(){
        item_ids += '' + (item_ids != '' ? ',' : '') + $(this).val();
    });    
    
    // hide order select block
    $('#pos-orders-list-container').hide();
    
    $.ajax({
        url: '/qc/addpositions',
        data : { 
            qc_id           : qc_id,
            stock_id        : stock_id,
            position_ids    : position_ids,
            item_ids        : item_ids
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                location.href = json.href; //'/order/edit/' + json.order_id;
            }
        }
    });    
};

/**
 * Показывает окно выбора сертификата качества
 * 
 * @version: 20120809, zharkov
 */
var show_qc_select = function()
{
    var stock_id = $('#stock').val();
    if (stock_id == 0) 
    {
        alert('Stock must be specified !');
        return false;
    }
    
    $.ajax({
        url: '/qc/getlist',
        data : { 
            stock_id : stock_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#docselform-container').html(json.content);
            }
            else
            {
                error = true;
            }                
        }
    });    
    
    $('#docselcontainer').show();
};

/**
 * Переводит на страницу редактирования выделенных айтемов
 * 
 * @version 20120817, zharkov
 */
var edit_items = function(doc, doc_id)
{
    var doc     = doc || '';
    var doc_id  = doc_id || 0;    
    
    var ids     = '';
        
    $('.cb-row-item:checked').each(function(){
        ids += (ids == '' ? '' : ',') + $(this).val();
    });    

    if (ids == '')
    {
        alert('Please check items for edit !');
        return;
    }
    
    location.href = (doc != '' ? '/target/' + doc + ':' + doc_id : '') + '/item/edit/' + ids;
};

/**
 * Прячет блок выбора заказа
 */
var close_document_select = function()
{
    $('#docselform-content').html('');
    $('#docselcontainer').hide();
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
                    async   : true,
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
 * Прячет окно с ссылкой на текущее сообщение
 * @version 20120716, zharkov: перенесено из chat_index.js
 * @version 20120707, zharkov
 */
var hide_chat_message_ref = function(message_id)
{
    $('#chat-message-ref-tip-' + message_id).remove();
}


/**
 * Выводит окно с ссыолкой на текущее сообщение
 * @version 20120716, zharkov: перенесено из chat_index.js
 * @version 20120707, zharkov
 */
var show_chat_message_ref = function(obj, message_id, message_date)
{
    var parent   = $('#chat-message-text-' + message_id);
    var position = $(parent).position();
    
    if(position === null) {
        var parent   = $(obj);
        var position = $(parent).position();
        console.log($(position));
    }
    
    if ($('#chat-message-ref-tip-' + message_id).length > 0)
    {
        $('#chat-message-ref-tip-' + message_id).remove();
    }
    else
    {
        $('.chat-message-ref-tip').remove();
        
        jQuery('<div/>', {
            id          : 'chat-message-ref-tip-' + message_id,
            html        : 
                'Message ref. :'
                +'<input id="js-message-' + message_id + '-ref-text" type="text" style="display: block; width: 292px; margin: 5px 0;" value="<ref message_id=' + message_id + '>Ref. ' + message_date + '</ref>">'
                +'<a class="close-chat-message-ref-tip" style="float: right; color: black;" href="javascript: void(0);" onclick="hide_chat_message_ref(' + message_id + ');">close</a>',
            'class'     : 'chat-message-ref-tip',
            'style'     : 'top: ' + (position.top - 10) + 'px; right: 0px;'
        }).appendTo(parent);

        $('#js-message-' + message_id + '-ref-text').select();
    }
};

/**
 * Выводит окно с ссыолкой на текущее сообщение блога BIZ
 * 05.05.2014 uskov
 */
var show_blog_message_ref = function(obj, message_id, message_date)
{
    var parent   = $(obj).parent();
    var position = $(obj).position();
    if ($('#chat-message-ref-tip-' + message_id).length > 0) 
	 {
		hide_chat_message_ref(message_id);
	 }
	 else 
	{
		jQuery('<div/>', {
			id          : 'chat-message-ref-tip-' + message_id,
			html        : 
				'Message ref. :'
				+'<input id="js-message-' + message_id + '-ref-text" type="text" style="display: block; width: 292px; margin: 5px 0;" value="<ref message_id=' + message_id + '>Ref. your ' + message_date + '</ref> : ">'
				+'<a class="close-chat-message-ref-tip" style="float: right; color: black;" href="javascript: void(0);" onclick="hide_chat_message_ref(' + message_id + ');">close</a>',
			'class'     : 'chat-message-ref-tip',
			'style'     : 'top: ' + (position.top + 15) + 'px; right: 12px;'
		}).appendTo(parent);
	}

	$('#js-message-' + message_id + '-ref-text').select();
};

/**
 * Show tooltip with blog message ref
 * @version 20130415, zharkov
 */
/*
var show_blog_message_ref = function(obj, message_id, message_date)
{
    var parent      = $(obj).parents('.biz-blog-entity');
    var position    = $(obj).position();

    if ($('#chat-message-ref-tip-' + message_id).length > 0)
    {
        $('#chat-message-ref-tip-' + message_id).remove();
    }
    else
    {
        $('.chat-message-ref-tip').remove();
        
        jQuery('<div/>', {
            id          : 'chat-message-ref-tip-' + message_id,
            html        : 
                'Message ref. :'
                +'<input id="js-message-' + message_id + '-ref-text" type="text" style="display: block; width: 292px; margin: 5px 0;" value="<ref message_id=' + message_id + '>Ref. ' + message_date + '</ref> : ">'
                +'<a class="close-chat-message-ref-tip" style="float: right; color: black;" href="javascript: void(0);" onclick="hide_chat_message_ref(' + message_id + ');">close</a>',
            'class'     : 'chat-message-ref-tip',
            'style'     : 'color: black; top: ' + (position.top + 20) + 'px; left: 236px;'
        }).appendTo(parent);

        $('#js-message-' + message_id + '-ref-text').select();        
    }        
};
*/
/**
 * Show tooltip with blog message ref
 * @version 20130415, zharkov
 */
var show_blog_email_ref = function(obj, email_id, email_title)
{
    var parent      = $(obj).parents('.biz-blog-entity');
    var position    = $(obj).position();

    if ($('#chat-message-ref-tip-' + email_id).length > 0)
    {
        $('#chat-message-ref-tip-' + email_id).remove();
    }
    else
    {
        $('.chat-message-ref-tip').remove();
        
        jQuery('<div/>', {
            id          : 'chat-message-ref-tip-' + email_id,
            html        : 
                'Message ref. :'
                +'<input id="js-message-' + email_id + '-ref-text" type="text" style="display: block; width: 292px; margin: 5px 0;" value="<ref email_id=' + email_id + '>Ref. ' + email_title + '</ref> : ">'
                +'<a class="close-chat-message-ref-tip" style="float: right; color: black;" href="javascript: void(0);" onclick="hide_chat_message_ref(' + email_id + ');">close</a>',
            'class'     : 'chat-message-ref-tip',
            'style'     : 'color: black; top: ' + (position.top + 20) + 'px; left: 236px;'
        }).appendTo(parent);

        $('#js-message-' + email_id + '-ref-text').select();        
    }
};


/**
 * Помечает сообщение как сделанное
 * @version 20120716, zharkov: перенесено из chat_index.js
 * @version 20120703, zharkov
*/ 
var mark_message_as_done = function(message_id)
{
	$.ajax({
        url: '/chat/markasdone',
        data : {
            message_id : message_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                // прячет иконку пендинга
                $('#message-' + message_id + '-pending').hide();
                
                // прячет сообщение пендинга на странице пендингов
                if ($('#chat-pending-' + message_id).length > 0) $('#chat-pending-' + message_id).remove();
                
                if ($('#chat-messages li').size() == 0)
                {
                    if ($('#chat-messages').length > 0) $('#chat-messages').hide();
                    if ($('#chat-no-pendings').length > 0) $('#chat-no-pendings').show();
                    if ($('#chat-pending-controls').length > 0) $('#chat-pending-controls').hide();
                }
            }
            else
            {
                Message('Error marking message as done !', 'error');
            }                
        }
    });
};
/*
var mark_message_as_done = function(message_id)
{
	$.ajax({
        url: '/chat/markasdone',
        data : {
            message_id : message_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
					 if ($('#message-' + message_id + '-pending').text() == 'Done')
					 {
						//$('#message-' + message_id + '-pending').remove();
					 }
                if ($('#message-' + message_id + '-pending').text() !== 'Done')
					 {
						// Меняет цвет и текст при пометке Done
						 $('#message-' + message_id + '-pending').text("Done");
						 $('#message-' + message_id + '-pending').css('background-color', 'green');
                }
            }
            else
            {
                Message('Error marking message as done !', 'error');
            }                
        }
    });
};*/

/**
 * Показывает блок с дополнительными параметрами поиска
 * @version 20120603, zharkov
 * @version 20120711, zharkov перенесено из company_index.js
 */
var show_more_params = function()
{
    $('#more-params').show();
    $('#a-show-params').hide();
};

/**
 * Прячет блок с дополнительными параметрами поиска
 * @version 20120603, zharkov
 * @version 20120711, zharkov перенесено из company_index.js
 */
var hide_more_params = function()
{
    $('#more-params').hide();
    $('#a-show-params').show();
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
 * Помечает звуковые сообщения как проигранные
 * @version 20120704, zharkov
 */
var chat_message_delivered = function()
{
    $.ajax({
        url     : '/service/chatmessagedelivered',
        data    : {
            message_id : user_last_chat_message_id
        },
        success : function(json){
        }
    });    
}

/**
 * Обновляет список получателей чата
 * @version 20120728, zharkov
 */
var update_chat_recipients = function()
{
    $.ajax({
        url     : '/service/getchatrecipients',
        success : function(json){
            if (json.result == 'okay') 
            {                                
		        $('#chat-icon-park').html(json.content);
		        bind_tooltips();
            }
        }
    });
};

/**
 * Обновляет список получателей чата
 * @version 20120728, zharkov
 */
var update_chat_customers = function()
{
    $.ajax({
        url     : '/service/getchatcustomers',
        success : function(json){
            if (json.result == 'okay') 
            {                                
		        $('#chat-icon-park-customers').html(json.content);
		        bind_tooltips();
            }
        }
    });
};

/**
 * Получет новые сообщения чата
 * @version 20120703, zharkov
 */
var get_new_chat_messages = function()
{
    var chat_updater    = $('#chat-updater').length > 0 ? true : false;
    var object_alias    = $('#app_object_alias').length > 0 ? $('#app_object_alias').val() : '';
    var object_id       = $('#app_object_id').length > 0 ? $('#app_object_id').val() : 0;

    $.ajax({
        url     : '/service/getchatmessages',
        data    : {
            object_alias    : object_alias,
            object_id       : object_id,
            message_id      : user_last_chat_message_id,
            chat_updater    : chat_updater
        },
        success : function(json){
            if (json.result == 'okay') 
            {                
                // updates last message id
                // сохраняет идентификатор последнего полученного сообщения, с проверкой от возможный сбоев, каждый слеюущий id должен быть больше предыдущего
                user_last_chat_message_id = json.message_id > user_last_chat_message_id ? json.message_id : user_last_chat_message_id;
                
                // add message to blog // добавляет сообщения в ленту
                if (chat_updater)
                {
                    if (json.messages)
                    {
                        $.each(json.messages, function(key, message)
                        {
                            _chat_message_add_to_list(object_alias, message);
                        });                        
                    }
                    
                    if (json.statuses)
                    {
                        $.each(json.statuses, function(user_id, status){
                            $('#user-picture-' + user_id).attr('class', 'chat-user-' + status);
                        });                        
                    }
                    
                    bind_prettyphoto();
		            bind_tooltips();
                }
                
                // show service alert about new messages // и выводит сервисное сообщение о новых сообщениях
                if (json.mycount > 0)
                {
                    // Message('<a href="/touchline">TouchLine message !</a>', 'warning');
                }
                
                if (json.alert != '')
                {
                    $('#chat-audio').html('');
                    $('#chat-audio').append('<embed height="0px" width="0px" src="/img/sounds/' + json.alert + '.mp3" autostart="true" hidden="true" loop="false"/>');
                    
                    chat_message_delivered();
                }

				if (json.update_icons)
				{
					update_chat_recipients();
                                        update_chat_customers();
				}
            }
        }
    });    
};

/*@version 20130520, sasha bind title for tooltip*/
var bind_title_data = function()
{
	$('.tooltip').each(function(){
		if (this.title != '')
		{	
			$(this).attr('data-title', this.title);
		}
		$(this).attr('title', $(this).data('title'));
	});
}


/**
 * pinger, keep session alive
 */
var pinger = function(){
    
    var chat_object_alias    = $('#chat-object-alias').length > 0 ? $('#chat-object-alias').val() : '';
    var chat_object_id       = $('#chat-object-id').length > 0 ? $('#chat-object-id').val() : 0;    
    
    $.ajax({
        url     : '/service/pinger',
        data    : {
            chat_object_alias   : chat_object_alias,
            chat_object_id      : chat_object_id,
            last_message_id     : user_last_chat_message_id
        },
        success : function(json){
            
            if (json.new_messages) get_new_chat_messages();
            
        },        
        complete : function () {
            setTimeout(function () {
                pinger();
		get_pending_counter();
                get_orders_last_id();
            }, pinger_refresh_time);
        }
    });
};


/**
 * Заполняет Select значениями
 */
var fill_select = function(id, json_arr, first_option)
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
 * Shows system message
 */
function Message(message, type)
{
    $('<a class="' + type + '">' + message + '</a>').appendTo('#app_messages'); 
    $('#app_messages').show();  
    set_timer_for_messages();
};

/**
 * Shows debug information
 */
var dg = function(obj)
{
    var x; var s='';
    for (x in obj)
    if (x != 'outerText' && x != 'innerText' && x != 'outerHTML' && x != 'innerHTML' && typeof(obj[x]) != 'function')
    s += x + ': ' + obj[x] + ';\t';
    
    return alert(s);
};

/**
 * Returns mouse coords
 */
function getMouseCoords(e) {
    var posx = 0;
    var posy = 0;
    
    if (!e) var e = window.event;
    
    if (e.pageX || e.pageY)
    {
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY)    
    {
        posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    
    return {
        'x': posx, 
        'y': posy
    };
};

/**
 * Возвращает ширину и высоту клиентской области
 * @version: 20121101, zharkov
 */
function getClientSize()
{
    var w = 0, h = 0;
    
    if(typeof(window.innerWidth) == 'number') {
        w = window.innerWidth;
        h = window.innerHeight;
    }
    else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        w = document.documentElement.clientWidth;
        h = document.documentElement.clientHeight;
    }
    else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
        w = document.body.clientWidth;
        h = document.body.clientHeight;
    }
    
    return {
        'width' : w, 
        'height': h
    };
}


/**
 * Init MCE instances
 */
var bind_mce_editors = function()
{
    if (typeof mce_editors == 'undefined') return;

    for (object_id in mce_editors)
    {
        theme       = mce_editors[object_id].theme;
        settings    = mce_simple;

        if (theme == 'advanced')
        {
            settings = mce_advanced;
        }

        if (theme == 'chat')
        {
            settings = mce_chat;
        }
        
        if (theme == 'normal')
        {
            settings = mce_normal;
        }
    
        if (theme == 'enormal')
        {
            //settings = $.extend(settings, mce_enormal);
            settings = mce_enormal;
        }
        
        settings['elements']                            = object_id;
        settings['height']                              = mce_editors[object_id].height;
        settings['convert_urls']                        = false;
        settings['relative_urls']                       = false;
        settings['theme_advanced_path']                 = false;
        settings['theme_advanced_statusbar_location']   = "";
        settings['paste_auto_cleanup_on_paste']         = true;
        settings['paste_strip_class_attributes']        = "all";
        settings['content_css']                         = '/css/mce_advanced.css';

/*        
        settings['paste_preprocess']                    = function(pl, o) {
            alert(o.content);
            o.content = o.content.replace(/id="(.+)"/g, 'id="mce_$1"');
        };
*/
        tinyMCE.init(settings);
    }    
};

/**
 * Calculates weight of the product
 */
var calc_unitweight = function(row_index, dimension_unit, weight_unit, prefix)
{
    dimension_unit = (typeof dimension_unit == 'undefined') ? '' : dimension_unit;
    if (dimension_unit == '' && $('#dimension_unit')) dimension_unit = $('#dimension_unit').val();
 
    weight_unit = (typeof weight_unit == 'undefined') ? '' : weight_unit;
    if (weight_unit == '' && $('#weight_unit')) weight_unit = $('#weight_unit').val();
    
    prefix = (typeof prefix == 'undefined') ? '' : prefix + '-';

    if (dimension_unit == '' || weight_unit == '') return;

    unitweight_obj  = '#' + prefix + 'unitweight-' + row_index;
    thickness_obj   = '#' + prefix + 'thickness-' + row_index;
    width_obj       = '#' + prefix + 'width-' + row_index;
    length_obj      = '#' + prefix + 'length-' + row_index;

    if (!$(unitweight_obj) || !$(thickness_obj) || !$(width_obj) || !$(length_obj)) return;


    thickness = parseNumber($(thickness_obj).val());
    if (thickness == 0) return;
    
    width = parseNumber($(width_obj).val());
    if (width == 0) return;
    
    length = parseNumber($(length_obj).val());
    if (length == 0) return;
    
    unit_weight = 0;
    if (dimension_unit == 'mm' && weight_unit == 'mt')
    {                                        //was 0.000000008
        unit_weight = thickness * width * length * 0.00000000785; 
        if (unit_weight > 0)
        {
            $(unitweight_obj).val(numberRound(unit_weight, 2));
        }  
    }
    else if (dimension_unit == 'in' && weight_unit == 'lb')
    {                                        //was 0.289067809 
        unit_weight = thickness * width * length * 0.28364779;
        if (unit_weight > 0)
        {
            $(unitweight_obj).val(numberRound(unit_weight, 0));
        } 
    }
    
      
};

/**
 * Расчитывает вес позиции
 */
var calc_weight = function(row_index, prefix)
{
    var weight_unit = $('#weight_unit').val();
    var dimension_unit = $('#dimension_unit').val();
    var prefix = (typeof prefix == 'undefined') ? '' : prefix + '-';
    
    var unitweight_obj  = '#' + prefix + 'unitweight-' + row_index;
    var qtty_obj        = '#' + prefix + 'qtty-' + row_index;
    var weight_obj      = '#' + prefix + 'weight-' + row_index;
    
    if (!$(unitweight_obj) || !$(qtty_obj) || !$(weight_obj)) return;
    
    var unitweight = parseNumber($(unitweight_obj).val());
    if (unitweight == 0) return;
    
    var qtty = parseNumber($(qtty_obj).val());
    //if (qtty == 0) return;

    //$(weight_obj).val(numberRound(unitweight * qtty, 2));
    
    if (dimension_unit == 'mm' && weight_unit == 'mt'){
        $(weight_obj).val(numberRound(unitweight * qtty, 2));
    } 
    else if (dimension_unit == 'in' && weight_unit == 'lb'){
        $(weight_obj).val(numberRound(unitweight * qtty, 0));
    }
};

/**
 * Расчитывает стоимость позиции
 */
var calc_value = function(row_index, prefix)
{
    var prefix      = (typeof prefix == 'undefined') ? '' : prefix + '-';
    var price_unit  = '';
    var weight_unit = '';
    
    if ($('#' + prefix + 'position-params-' + row_index).length > 0)
    {
        price_unit  = $('#' + prefix + 'position-params-' + row_index).data('price_unit');
        weight_unit = $('#' + prefix + 'position-params-' + row_index).data('weight_unit');
    }
    else if ($('#' + prefix + 'item-params-' + row_index).length > 0)
    {
        price_unit  = $('#' + prefix + 'item-params-' + row_index).data('price_unit');
        weight_unit = $('#' + prefix + 'item-params-' + row_index).data('weight_unit');        
    }
    else 
    {
        if ($('#price_unit')) price_unit = $('#price_unit').val();
        if ($('#weight_unit')) weight_unit = $('#weight_unit').val();
    }

    var weight_obj  = '#' + prefix + 'weight-' + row_index;
    var price_obj   = '#' + prefix + 'price-' + row_index;
    var value_obj   = '#' + prefix + 'value-' + row_index;

    if (!$(weight_obj) || !$(price_obj) || !$(value_obj)) return;

    var weight = parseNumber($(weight_obj).val());
    //if (weight == 0) return;
    
    var price = parseNumber($(price_obj).val());
    if (price == 0) return;
    
    var value = weight * price;

    if (weight_unit == 'lb' && price_unit == 'cwt')
    {
        value = value / 100;
    }

    $(value_obj).val(numberRound(value, 2));
};

/**
 * Подсчитывает тотал
 */
var calc_total = function(obj_name)
{
    var prefix      = (typeof obj_name == 'undefined') ? '' : obj_name + '-';
    var suffix      = (typeof obj_name == 'undefined') ? '' : '-' + obj_name;
    
    var qtty_obj    = '#' + prefix + 'qtty-';
    var weight_obj  = '#' + prefix + 'weight-';
    var value_obj   = '#' + prefix + 'value-';
    var p_value_obj = '#' + prefix + 'purchasevalue-';
        
    // total
    if ($('#lbl-total-qtty' + suffix) && $('#lbl-total-weight' + suffix) && $('#lbl-total-value' + suffix))
    {
        var qtty    = 0;
        var weight  = 0;
        var value   = 0;
        var p_value = 0;

        $('.cb-row' + suffix).each(function(){        
            
            i = $(this).val();

            if ($(qtty_obj + i))    qtty += parseNumber(getVal($(qtty_obj + i)));
            if ($(weight_obj + i))  weight += parseNumber(getVal($(weight_obj + i)));
            if ($(value_obj + i))   value += parseNumber(getVal($(value_obj + i)));
            if ($(p_value_obj + i)) p_value += parseNumber(getVal($(p_value_obj + i)));

        });
        
        $('#lbl-total-qtty' + suffix).html(qtty);    
        $('#lbl-total-weight' + suffix).html(numberRound(weight, 2));    
        $('#lbl-total-value' + suffix).html(numberRound(value, 2));
        if ($('#lbl-total-purchasevalue' + suffix)) $('#lbl-total-purchasevalue' + suffix).html(numberRound(p_value, 2));
    }
    
    // calculate selected    
    var qtty    = 0;
    var weight  = 0;
    var value   = 0;
    var p_value = 0;
    var checked = 0;
    
    $('.cb-row' + suffix + ':checked').each(function(){
    
        i = $(this).val();
        
        if ($(qtty_obj + i))    qtty += parseNumber(getVal($(qtty_obj + i)));
        if ($(weight_obj + i))  weight += parseNumber(getVal($(weight_obj + i)));
        if ($(value_obj + i))   value += parseNumber(getVal($(value_obj + i)));
        if ($(p_value_obj + i)) p_value += parseNumber(getVal($(p_value_obj + i)));
        
        checked++;
    });    
    
    // show selected total
    if ($('#lbl-selected-qtty' + suffix) && $('#lbl-selected-weight' + suffix) && $('#lbl-selected-value' + suffix))
    {        
        $('#lbl-selected-qtty' + suffix).html(qtty);    
        $('#lbl-selected-weight' + suffix).html(numberRound(weight, 2));    
        $('#lbl-selected-value' + suffix).html(numberRound(value, 2));
        if ($('#lbl-selected-purchasevalue' + suffix)) $('#lbl-selected-purchasevalue' + suffix).html(numberRound(p_value, 2));
    }
    
    // show selected actions
    if ($('#selected-actions' + suffix))
    {
        if (checked > 0)
            $('#selected-actions' + suffix).show();
        else
            $('#selected-actions' + suffix).hide();
    }
};

/**
 * Получает значение объекта
 */
var getVal = function(obj)
{
    if (obj.is('input'))
    {
        return obj.val();
    }
    else
    {
        return obj.html();
    }
};

/**
 * Устанавливает значение объекта
 */
var setVal = function(obj, val)
{
    if (obj.is('input'))
    {
        return obj.val(val);
    }
    else
    {
        return obj.html(val);
    }    
}

/**
 * Convert value to number
 */
var parseNumber = function(value)
{
    value = new String(value);
    value = value.replace(/\s*/gi, '').replace(/[^0-9-.]+/gi, '');
    value = value == '' ? 0 : value;
    
    re = new RegExp("^([0-9]*\\.{1}[0-9]+)$", "i")
    if(re.test(value)) 
        return parseFloat(value);
    else
        return parseInt(value);
};

/**
 * Round float value
 */
var numberRound = function(value, precision)
{
    value = parseNumber(value);
    return value.toFixed(precision);
    //return Math.round(value * Math.pow(10, precision)) / Math.pow(10, precision);
}

/**
 * Устанавливает параметры склада
 */
var bind_stock_params = function(stock_id, strict)
{
	strict = (typeof strict == 'undefined') ? true : strict;


    if (stock_id > 0)
    {
        $('#locations').prepend($('<option selected="" value="0">loading...</option>'));
        if ($('#steelgrades')) $('#steelgrades').prepend($('<option selected="" value="0">loading...</option>'));  
        
        error = false;
        
        $.ajax({
            url: '/stock/getparams',
            data : {
                stock_id : stock_id,
		strict : strict
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('#dimension_unit').val(json.stock.dimension_unit);
                    $('#weight_unit').val(json.stock.weight_unit);
                    $('#price_unit').val(json.stock.price_unit);
                    
                    $('.lbl-dim').html(', ' + json.stock.dimension_unit);
                    $('.lbl-wgh').html(', ' + (json.stock.weight_unit == 'mt' ? 'ton' : json.stock.weight_unit));
                    $('.lbl-price').html(', ' + json.stock.currency_sign + '/' + (json.stock.price_unit == 'mt' ? 'ton' : json.stock.price_unit));
                    $('.lbl-value').html(', ' + json.stock.currency_sign);
                    
                    fill_select("#locations", json.locations, {'value' : 0, 'name' : "--"});
                    if ($('#steelgrades')) fill_select("#steelgrades", {}, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (stock_id == 0 || error)
    {
        $('#dimension_unit').val('');
        $('#weight_unit').val('');
        $('#price_unit').val('');
        
        $('.lbl-dim').html('');
        $('.lbl-wgh').html('');
        $('.lbl-price').html('');
        $('.lbl-value').html('');
        
        $('#locations').empty();
        $('#locations').prepend($('<option value="0">--</option>'));
        
        if ($('#steelgrades'))
        {
            $('#steelgrades').empty();
            $('#steelgrades').prepend($('<option value="0">--</option>'));
        }        
    }
};

/**
 * Заполняет locations склада
 */
var bind_locations = function(stock_id, prefix, suffix)
{
    prefix      = typeof prefix == 'undefined' ? '' : prefix + '-';    
    suffix      = typeof suffix == 'undefined' ? '' : '-' + suffix;
    
    selector    = '#' + prefix + 'location' + suffix;
    
    if (stock_id > 0)
    {
        $(selector).prepend($('<option selected="" value="0">loading...</option>')); 

        error = false;                
        $.ajax({
            url: '/stock/getlocations',
            data : {
                stock_id : stock_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select(selector, json.locations, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (stock_id == 0 || error)
    {
        $(selector).empty();
        $(selector).prepend($('<option value="0">--</option>'));        
    }
};

/**
 * Заполняет марки стали со склада
 */
var bind_stock_steelgrades = function()
{
    stock_id    = 0;
    location_id = 0;
    
    if ($('#stocks')) stock_id = $('#stocks').val();
    if ($('#locations')) location_id = $('#locations').val();
    
    if (stock_id > 0)
    {
        $('#steelgrades').prepend($('<option selected="" value="0">loading...</option>')); 
        
        $.ajax({
            url: '/stock/getsteelgrades',
            data : {
                stock_id : stock_id,
                location_id : location_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#steelgrades", json.steelgrades, {'value' : 0, 'name' : "--"});                    
                }
                else
                {
                    $('#steelgrades').empty();
                    $('#steelgrades').prepend($('<option value="0">--</option>'));                    
                }                
            }
        });        
    }
    else
    {
        $('#steelgrades').empty();
        $('#steelgrades').prepend($('<option value="0">--</option>'));        
    }
};

/**
 * Заполняет элементы на странице /positions
 */
var bind_positions_filter = function()
{
    var stock_id        = $('#stock').val();
    var stock_selected  = false;
    
    	if(stock_id == '1') {
		var size_item = 'mm';
		var weight_item = 'Ton';
	} else if(stock_id == '2'){
		var size_item = 'In';
		var weight_item = 'lb';
	} else {
		var size_item = '';
		var weight_item = '';
	}

	if(size_item !== '' && weight_item !== '') {
		$('.size').html(size_item);
		$('.weight').html(weight_item);
	}		
    
    if (stock_id > 0)
    {
        $('#locations').html('loading...'); 
        $('#deliverytimes').html('loading...');
        $('#steelgrades').prepend($('<option selected="" value="0">loading...</option>'));
        $('#stockholders').html('loading...');
	
        var rev_date = $('#rev_date').val();
        var rev_time = $('#rev_time').val();

        $.ajax({
            url: '/stock/getpositionfilter',
            data : {
                stock_id : stock_id,
                rev_date : rev_date,
                rev_time : rev_time
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    stock_selected = true;
                    
                    $('#locations').html(json.locations);
                    $('#deliverytimes').html(json.deliverytimes);			
                    //fill_select("#steelgrades", json.steelgrades, {'value' : 0, 'name' : "--"});
                    $("#steelgrades").html(json.steelgrades);
		    $("#stockholders").html(json.stockholders);
                    
                }
            }
        });
        
        $('.chosen-select').chosen();
    }
    else
    {
        $('#locations').html('<span style="color: #aaa;">none</span>');
        $('#deliverytimes').html('<span style="color: #aaa;">none</span>');
        $('#steelgrades').prepend($('<option selected="" value="0">--</option>'));
    }
};

/**
 * Заполняет элементы фильтрации на странице /items
 */
var bind_items_filter = function()
{
    var stock_id        = $('#stock').val();
    var stock_selected  = false;
	
	if(stock_id == '1') {
		var size_item = 'mm';
		var weight_item = 'Ton';
	} else if(stock_id == '2'){
		var size_item = 'In';
		var weight_item = 'lb';
	} else {
		var size_item = '';
		var weight_item = '';
	}

	if(size_item !== '' && weight_item !== '') {
		$('.size').html(size_item);
		$('.weight').html(weight_item);
	}		
    
    if (stock_id > 0)
    {
		
        $('#locations').html('loading...'); 
        $('#deliverytimes').html('loading...'); 
        
        var rev_date = $('#rev_date').val();
        var rev_time = $('#rev_time').val();        
        
        $.ajax({
            url: '/stock/getitemfilter',
            data : {
                stock_id : stock_id,
                rev_date : rev_date,
                rev_time : rev_time                
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    stock_selected = true;
                    console.log(stock_selected);
                    $('#locations').html(json.locations);
                    $('#deliverytimes').html(json.deliverytimes);
                    fill_select("#steelgrade", json.steelgrades, {'value' : 0, 'name' : "All"});
                    fill_select("#order", json.orders, {'value' : 0, 'name' : "All"});
                    $('.chosen-selec').chosen;                    
                    $(".chosen-select").trigger("chosen:updated");
                }
            }
        });        
    }
    
    if (!stock_selected)
    {
        $('#locations').html('<span style="color: #aaa;">none</span>');
        $('#deliverytimes').html('<span style="color: #aaa;">none</span>');
        
        $("#steelgrade").empty();
        $("#steelgrade").prepend($('<option value="0">All</option>'));        
        $(".chosen-select").chosen();  
    }
    
};

/**
 * Открывает список айтемов для позиции
 */
var position_show_items = function(position_id)
{
    if ($('#img-' + position_id).attr('src') == '/img/icons/plus.png')
    {
        $('#img-' + position_id).attr('src', '/img/icons/minus.png');
        $('#position-items-' + position_id).show();
    }
    else
    {
        if ($('#pos' + position_id + '-thickness-1').length > 0) position_canceledit(position_id);
        
        $('#img-' + position_id).attr('src', '/img/icons/plus.png');
        $('#position-items-' + position_id).hide();        
    }
};

/**
 * Заполняет список компаний из бизнеса
 */
var bind_biz_companies = function(biz_id, role, obj_name)
{
    var has_errors  = false;
    var selector    = (typeof obj_name == 'undefined') ? '.biz-companies-' + role : '#' + obj_name;
    
    if (biz_id > 0)
    {
        $.ajax({
            url: '/biz/getcompanies',
            data : {
                biz_id : biz_id,
                role : role
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $(selector).each(function(){
                        fill_select('#' + $(this).attr('id'), json.companies, {'value' : 0, 'name' : "--"});    
                    });
                }
                else
                {
                    has_errors = true;
                }                
            }
        });        
    }
    
    if (biz_id == 0 || has_errors)
    {
        $(selector).each(function(){
            $(this).empty();
            $(this).prepend($('<option value="0">--</option>'));
        });
    }
};

/**
 * Повторяет выбор бизнеса
 */
var select_bizes = function(selected_value)
{
    if ($('.biz').length > 0) $('.biz').val(selected_value);
};

/**
 * Повторяет выбор компании
 */
var select_companies = function(selected_value, selector)
{
    if ($('.' + selector).length > 0) $('.' + selector).val(selected_value);
};


/**
 * Редактирует позицию
 */
var position_quickedit = function(position_id)
{
    if (position_id > 0)
    {
        $('#a-position-quick-edit-' + position_id).toggle();
        $('#a-position-save-' + position_id).toggle();
        $('#a-position-cancel-' + position_id).toggle();
        
        $.ajax({
            url: '/position/quickedit',
            data : {
                position_id : position_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('#position-' + position_id).html(json.content);
                    bind_prettyphoto();
                }
                else
                {
                    alert('Error receiving data !');
                }                
            }
        });        
    }
}

/**
 * Отменяет быстрое редактирование
 */
var position_canceledit = function(position_id)
{
    if (position_id > 0)
    {
        $('#a-position-quick-edit-' + position_id).toggle();
        $('#a-position-save-' + position_id).toggle();
        $('#a-position-cancel-' + position_id).toggle();
        
        $.ajax({
            url: '/position/quickcancel',
            data : {
                position_id : position_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('#position-' + position_id).html(json.content);
                    bind_prettyphoto();
                }
                else
                {
                    alert('Error receiving data !');
                }                
            }
        });        
    }
}

/**
 * Отправляет запрос на сохранение позиции
 */
var send_position_save = function(position_id)
{
    if (position_id > 0)
    {
        steelgrade_id = $('#pos' + position_id + '-steelgrade-1').val();
        thickness       = $('#pos' + position_id + '-thickness-1').val();
        width           = $('#pos' + position_id + '-width-1').val();
        length          = $('#pos' + position_id + '-length-1').val();
        unitweight      = $('#pos' + position_id + '-unitweight-1').val();
        weight          = $('#pos' + position_id + '-weight-1').val();
        price           = $('#pos' + position_id + '-price-1').val();
        value           = $('#pos' + position_id + '-value-1').val();
        delivery_time   = $('#pos' + position_id + '-delivery_time-1').val();
        notes           = $('#pos' + position_id + '-notes-1').val();
        internal_notes  = $('#pos' + position_id + '-internal_notes-1').val();
        
        $.ajax({
            url: '/position/quicksave',
            data : {
                position_id     : position_id,
                steelgrade_id   : steelgrade_id,
                thickness       : thickness,
                width           : width, 
                length          : length,
                unitweight      : unitweight,
                weight          : weight,
                price           : price,
                value           : value,
                delivery_time   : delivery_time,
                notes           : notes,
                internal_notes  : internal_notes
                
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('#position-' + position_id).html(json.content);
                    
                    $('#a-position-quick-edit-' + position_id).toggle();
                    $('#a-position-save-' + position_id).toggle();
                    $('#a-position-cancel-' + position_id).toggle();
                    
                    calc_total('position');
                    update_position_items(position_id, json.position);
                }
                else
                {
                    alert('Error receiving data !');
                }
                
                // прячет модальное окно loading...
                hide_idle();
            }
        });        
    }
}

/**
 *  Сохраняет позицию после быстрого редактирования
 */
var position_save = function(position_id)
{
    if ($('#pos' + position_id + '-steelgrade-1').val() == 0) 
    {
        alert('I forgot to specify Steel Grade !');
        return;
    }    
    
    // показывает модальное окно loading...
    show_idle();

    // если chrome или safari отправляем с задержкой, чтобы показалось модальное окно
    if ($.browser.webkit) 
    {
        setTimeout("send_position_save(" + position_id + ");", webkit_timeout);
    }
    else
    {
        send_position_save(position_id);
    }    
}

/**
 * Обновляем позицию
 */
var update_position_items = function(position_id, position_value)
{
    $('.pos' + position_id + '-steelgrade').html(position_value.steelgrade.title);
    $('.pos' + position_id + '-thickness').html(position_value.thickness);
    $('.pos' + position_id + '-width').html(position_value.width);
    $('.pos' + position_id + '-length').html(position_value.length);
    $('.pos' + position_id + '-unitweight').html(numberRound(position_value.unitweight, 2));
}

/**
 * Отправляет запрос на удаление айтемов
 * @version: 20120429, zharkov
 */
var send_item_remove = function(item_id, position_id)
{
    $.ajax({
        url: '/position/removeitems',
        data : {
            position_id : position_id,
            ids : item_id
        },
        success: function(json){
            // обрабатывает ответ сервера
            item_remove_hadler(json);
            
            // прячет модальное окно loading...
            hide_idle();
            
            // removes item details window
            remove_modal();            
        }
    });    
}

/**
 * Удаляет айтем
 */
var item_remove = function(item_id, position_id, force_alert)
{
    force_alert = force_alert || false;
    
    if (!force_alert && !confirm('Am I sure I want to remove item ?')) return false;
    
    // показывает модальное окно loading...
    show_idle();

    // если chrome или safari отправляем с задержкой, чтобы показалось модальное окно
    if ($.browser.webkit) 
    {
        setTimeout("send_item_remove('" + item_id + "', " + position_id + ");", webkit_timeout);
    }
    else
    {
        send_item_remove(item_id, position_id);
    }
};


/**
 * Удаляет выделенные айтемы
 */
var items_remove = function(position_id)
{   
    ids         = '';
    selected    = $('.cb-row-item-position-' + position_id + ':checked');
    if (selected.length > 0)
    {
        if (!confirm('Remove selected items ?')) return;

        $.each(selected, function(key, cb){
            ids += '' + (ids != '' ? ',' : '') + $(cb).val();
        });
        
        item_remove(ids, position_id, true);        
    }
    else
    {
        alert('I need to select items !');
    }
}


/**
 * Перенаправляет на страницу добавления позиции
 */
var goto_position_add = function()
{
    location.href = '/position/add' + ($('#stock').val() > 0 ? '/' + $('#stock').val() : '');
}

/**
 * Выделяет все позиции
 */
var check_all_positions = function(obj)
{
    $('.cb-positions').attr('checked', $(obj).is(':checked'));
    calculate_total_positions();
}

/**
 * Выделяет все чекбоксы в таблице
 */
var check_all = function(obj, suffix)
{
    cb_class = '.cb-row' + (typeof suffix == 'undefined' ? '' : '-' + suffix);
    $(cb_class).attr('checked', $(obj).is(':checked'));
    
    show_selected_controls(suffix);
}
/**
 * Показывает элементры управления над выделенными в таблице обхектами
 */
var show_selected_controls = function(suffix)
{
    cb_class = '.cb-row' + (typeof suffix == 'undefined' ? '' : '-' + suffix);
    if ($(cb_class + ':checked').length > 0) 
    {
        $('.selected-control').removeClass('btn-default');
        $('.selected-control').addClass('btn-primary');
    }
    else 
    {
        $('.selected-control').removeClass('btn-primary');
        $('.selected-control').addClass('btn-default');
    }    
}

/**
 * Подсчитывает тотал
 */
var calculate_total_positions = function()
{
    var total_qtty      = 0;
    var total_weight    = 0;
    var total_value     = 0;
    
    $('.cb-positions:checked').each(function(){
        total_qtty      += parseNumber($('#position-' + $(this).val() + '-qtty').html());
        total_weight    += parseNumber($('#position-' + $(this).val() + '-weight').html());
        total_value     += parseNumber($('#position-' + $(this).val() + '-value').html());
    });
    
    $('#lbl-selected-qtty').html(total_qtty);
    $('#lbl-selected-weight').html(numberRound(total_weight, 2));
    $('#lbl-selected-value').html(numberRound(total_value, 2));
    
    if (total_qtty > 0) $('#selected-positions-actions').show(); 
    else $('#selected-positions-actions').hide();
    
}

/**
 * Подсчитывает тотал выделенных позиций при групповом редактировании
 * разница в .val() вместо .text()
 */
var calculate_selected_positions = function()
{

    var total_qtty      = 0;
    var total_weight    = 0;
    var total_value     = 0;
    
    $('.cb-positions:checked').each(function(){
        total_qtty      += parseNumber($('#qtty-' + $(this).val()).val());
        total_weight    += parseNumber($('#weight-' + $(this).val()).val());
        total_value     += parseNumber($('#value-' + $(this).val()).val());
    });
    
    $('#lbl-selected-qtty').html(total_qtty);
    $('#lbl-selected-weight').html(numberRound(total_weight, 2));
    $('#lbl-selected-value').html(numberRound(total_value, 2));
    
    if ($('#selected-positions-actions'))
    {
        if (total_qtty > 0) $('#selected-positions-actions').show(); 
        else $('#selected-positions-actions').hide();        
    }
}

/**
 * Выделяет все айтемы
 */
var check_all_items = function(obj, position_id)
{    
    c = typeof position_id == 'undefined' ? 'cb-items' : 'cb-position-' + position_id + '-items';
    $('.' + c).attr('checked', $(obj).is(':checked'));
    
    show_item_group_actions(position_id);
}

/**
 * Показывает элементы управления списком выделенный айтемов
 */
var show_item_actions = function(position_id)
{
    ext = typeof position_id == 'undefined' ? '' : 'position-' + position_id + '-';
    
    cbclass = '.cb-' + ext + 'items';
    actions = '#' + ext + 'selected-items-actions';
    
    if ($(cbclass + ':checked').length > 0) 
    {
        $('.selected-control').show();
        $(actions).show();        
    }
    else 
    {
        $('.selected-control').hide();
        $(actions).hide();
    }
}

/**
 * Переносит айтемы в другую позицию
 */
var selected_items_action = function(position_id, action)
{
    var with_position = typeof position_id == 'undefined' ? false : true;
    
    ext = with_position ? 'position-' + position_id + '-' : '';
    
    ids         = '';
    selected    = $('.cb-' + ext + 'items:checked');

    if (selected.length > 0)
    {
        selected.each(function(){
            ids += '' + (ids != '' ? ',' : '') + $(this).val();
        });

        location.href = (with_position ? '/position/' + position_id : '') + '/item/' + action + '/' + ids;
    }    
}

/**
* Перенаправляет выделенные элементы
*/
var redirect_selected = function(obj_name, url, confirm_text)
{
    if (typeof confirm_text != 'undefined' && !confirm(confirm_text)) return false;
    
    var ids = '';    

    var suffix      = (typeof obj_name == 'undefined') ? '' : '-' + obj_name;
    var selected    = $('.cb-row' + suffix + ':checked');
    
    if (selected.length > 0)
    {
        selected.each(function(){
            ids += '' + (ids != '' ? ',' : '') + $(this).val();
        });

        location.href = url + ids;
    }
};

/**
 * Отправляет на групповое редактирование выделенные позиции
 */
var positions_groupedit = function()
{
    
    ids         = '';
    selected    = $('.cb-positions:checked');

    if (selected.length > 0)
    {
        selected.each(function(){
            ids += '' + (ids != '' ? ',' : '') + $(this).val();
        });

        location.href = '/position/groupedit/' + ids;
    }
    else
    {
        alert('I forgot to check positions !');
    }
};

/**
 * Показывает модальное окно "loading..."
 */
var show_idle = function()
{
    if ($('#idle')) $('#idle').remove();
    
    jQuery('<div/>', { 
        id      : 'idle',
        html    : '<div style="margin-top:20px;"><h1>Data loading ...</h1></div>',
        'style' : 'height:' + $('body').height() + 'px;'
    }).appendTo('body');    
};

/**
 * Прячет модальное окно "loading..."
 */
var hide_idle = function()
{
    if ($('#idle')) $('#idle').remove();
}

/**
 * Очищает выбранную ревизию
 */
var clear_revision = function()
{
    $('#rev_date').val('');
    $('#rev_time').val('');
    $('#tr-revision').removeClass('revision');
}

/**
 * Показывает таблицу добавления позиций
 */
var show_position_table = function(stock_id)
{
    if (stock_id > 0)
    {
        $('#position-add-table').show();
        $('#position-add-text').hide();
    }
    else
    {
        $('#position-add-table').hide();
        $('#position-add-text').show();
    }
};


/**
 * Возвращает знак валюты
 */
var get_curreny_sign = function(currency)
{
    if (currency == 'eur')
    {
        return '&euro;'
    }
    else if (currency == 'gbp')
    {
        return '&pound;'
    }
    else if (currency == 'usd')
    {
        return '$'
    }

    return '';        
}

/**
 * Заполняет список регионов страны
 */
var fill_regions_select = function(country_id, selector)
{
    selector = '#' + (typeof prefix == 'undefined' ? 'sel-region' : selector);
    
    if (country_id > 0)
    {
        $(selector).prepend($('<option selected="" value="0">loading...</option>')); 
        
        error = false;                
        $.ajax({
            url: '/region/getlist',
            data : {
                country_id : country_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select(selector, json.list, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (country_id == 0 || error)
    {
        $(selector).empty();
        $(selector).prepend($('<option value="0">--</option>'));        
    }
};

/**
 * Вставляет картинку в MCEditor
 */
var mce_insert_picture = function(object_alias, secret_name, original_name)
{
    tinyMCE.execCommand('mceInsertContent', false, '<img src=/picture/'+ object_alias + '/'+ secret_name +'/g/'+ original_name +'>');return false;
};

/**
 * OnLoad
 */
$(function(){
    // ajax settings
    $.ajaxSetup({type : "POST", async : false, dataType : 'json'});

    $('#app_messages').bind('click', function(){
            $('#app_messages').html('');
            $('#app_messages').toggle('slow');
        });
    set_timer_for_messages();

    //запускает Pinger по таймауту
    setTimeout(function(){
        pinger();
	get_pending_counter();
        get_orders_last_id();
    }, pinger_start_time);
    
    
    if ($(".datepicker").length > 0) 
    {
        $(".datepicker").datepicker({
            showWeek: true,
            changeMonth : true,
            changeYear  : true,
            yearRange   : '-12',            
        });
    }
/*  слету не получилось, нужно доработать
    $('body').click(function(){
        $('.js-app-context').each(function(){
            
            obj = $(this);
            
            if (obj.data('leave'))
            {
                obj.hide();
            }
            else
            {
                obj.remove();
            }
        });    
    });
*/
    // инициализирует MCE редакторы
    bind_mce_editors();
    
    // назначает автокомлит для объектов выбора бизнеса
    bind_biz_autocomplete();
    
    // назначает автокомлит для объектов выбора компании
    bind_company_autocomplete();
    
    // назначение обработчика на ресайз окна
    $(window).resize(resize_container_content);
    resize_container_content();    

    bind_tooltips();
    
       
});





/*

tricks:

$('#total').val(total).format({format:"#,###.00", locale:"br"});

if( isNaN( xat ) ) xat = 0;

function RoundTo(val, count)
{
    return Math.round(val * Math.pow(10,count))/Math.pow(10,count);
}  

function ToDigital(val)
{
    re = new RegExp("^\\s*$", "i")

    if(re.test(val))
        return 0;
    else
    {
        re = new RegExp("^([0-9]+\\.{1}[0-9]+)$", "i")
        if(re.test(val))
                return parseFloat(val);
        else
            return parseInt(val);
    }
}
*/

/*
var myFn = function(fn) {
    var result = fn();
    console.log(result);
};

var myOtherFn = function() {
    return 'hello world';
};

myFn(myOtherFn);   // logs 'hello world'
*/


/**
 * Получает данные с формы
 * адаптирует ее для отправки посредством AJAX
 * на выходе получается стандартный массив form
 * 
 * @param string form_selector Селектор HTML элемента форма
 * 
 * @version 20121008, d10n
 */
function _get_form(form_selector)
{
    var form = new Object();
    var form_element = $(form_selector);
    
    if (form_element == undefined) return form;
    
    var form_elements = form_element.find('input, select, textarea, button');
    
    form_elements.each(function(key, value){
        var item = $(value);
        var name = item.attr('name');
        
        if (item.attr('type') == 'checkbox')
        {
            if (item.attr('checked') == undefined) return;
            name = name + '[' + key + ']';
        }
        form[name] = item.val();
    });
    
    return form;
}

/**
 * Удаляет объект DOM по идентификатору
 * 
 */
var destroy_obj = function(id)
{
    $('#' + id).remove();
};

/**
 * Устанавливает статус текущего пользователя
 * @version: 20121113, zharkov
 */
var set_user_status = function(status)
{
    $.ajax({
        url: '/user/setstatus',
        data : {
            status : status
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (status == 'away')
                {
                    $('#user-status-link').removeAttr("onclick");
                    $('#user-status-link').attr("onclick", "set_user_status('online');");
                    $('#user-status-link').text("I'm away");
                    $('#user-status-link').removeClass("online");
                    $('#user-status-link').addClass("away");                    
                }
                else if (status == 'online')
                {
                    $('#user-status-link').removeAttr("onclick");
                    $('#user-status-link').attr("onclick", "set_user_status('away');");
                    $('#user-status-link').text("I'm online");                    
                    $('#user-status-link').removeClass("away");
                    $('#user-status-link').addClass("online");
                }
            }
            else
            {
                alert('Error');
            }                
        }
    });
};

/**
 * Назначает элементу вода компании с классом {company_selector} функцию автозаполнения
 * для работы должно быть два поля: {text id="company" class="{company_selector}"} и {hidden id="company_id"}
 * у hidden поля должен быть id такой же как у text с суффиксом "_id"
 * 
 * @version 20121114, zharkov
 */
var bind_company_autocomplete = function(company_selector, callback_function)
{
    company_selector = company_selector || '.company-autocomplete';

    if ($(company_selector).length == 0) return;
        
    $(company_selector).each(function(){        
        
        obj_id      = $(this).attr('id');
        title_field = $(this).data('titlefield') || 'doc_no';
        
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
                $('#' + obj_id + '_id').val(0);
                
                $.ajax({
                    url     : "/company/getlistbytitle",
                    data    : {
                        maxrows     : 250,
                        title       : request.term,
                        title_field : title_field
                    },
                    success : function( data ) {
                        response( $.map( data.list, function( item ) {
                            return {
                                label: item.company.list_title,
                                value: item.company.id
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
                    $('#' + obj_id + '_id').val(ui.item.value);
                }
                else
                {
                    $('#' + obj_id + '_id').val(0);
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
 * Переключает отображение информации в контестном меню айтема
 * @version 20121202, zharkov
 */
var item_context_togle = function(index)
{
    if (index == 1)
    {
        $('#item-context-props-21').hide();
        $('#item-context-props-22').hide();
        $('#item-context-props-31').hide();
        $('#item-context-props-32').hide();
        
        $('#item-context-props-11').show();
        $('#item-context-props-12').show();        
    }
    else if (index == 3)
    {
        $('#item-context-props-11').hide();
        $('#item-context-props-12').hide();
        $('#item-context-props-21').hide();
        $('#item-context-props-22').hide();
        
        $('#item-context-props-31').show();
        $('#item-context-props-32').show();
    }
    else
    {
        $('#item-context-props-11').hide();
        $('#item-context-props-12').hide();
        $('#item-context-props-31').hide();
        $('#item-context-props-32').hide();
        
        $('#item-context-props-21').show();
        $('#item-context-props-22').show();
    }
};

/**
 * Закрывает контекстное меню айтема
 * @version 20121202, zharkov
 */
var item_context_close = function(item_id)
{
    $('#item-context-' + item_id).remove();
};

/**
 * Показывает контекстное меню айтема
 * @version 20121202, zharkov
 */
var show_item_context = function(event, item_id)
{
    show_idle();
    
    $.ajax({
        url     : '/item/getcontext',
        data    : { 
            item_id : item_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                show_modal(json.content, 800, 550);
            }
            else if (json.message) 
            {
                Message(json.message, 'error');
            }
            
            hide_idle();
            bind_prettyphoto();            
        }
    });
}

/**
 * Shows the history of the shortcut menu
 * 
 * @version 20130528, sasha
 */
var show_item_history_context = function(id)
{ 
	html = $('#hiden-history-' + id).html();

	show_modal(html, 800, 600);
}

/**
 * Показывет модальное окно загрузчика файлов
 * @version 20130224, zharkov
 */
var uploader_show_modal = function(object_alias, object_id)
{
    $.ajax({
        url     : '/attachment/getmodalbox',
        data    : {
            oalias : object_alias, 
            oid : object_id
        },
        success: function(json)
        {
            if (json.result == 'okay') 
            {
                show_modal(json.content, 800, 500);
                
                bind_qq_uploader();
            }
            else
            {
                Message(json.message, 'error');
            }            
        }
    });    
    
    return false;
}

/**
 * Сохраняет форму модального окна аплоадера
 * @version 20130224, zharkov
 */
var uploader_save_form = function()
{
    var form            = new Object();
    var form_elements   = $('.table-list.uploaded-files').find('input.title-input');
    var obj             = $('.table-list.uploaded-files').find('.uf-container:first[data-id]');

    $.each(form_elements, function(key, value){
        var item = $(value);
        var name = item.attr('name');
        
        if (item.attr('type') == 'checkbox')
        {
            if (item.attr('checked') == undefined) return;
            name = name + '[' + key + ']';
        }
        form[name] = item.val();
    });
    
    form.oalias = obj.data('oalias');
    form.oid    = obj.data('oid');
    
    $.ajax({
        url: '/attachment/save',
        data : form,
        beforeSend : function()
        {
            if ($('.tla-toggler') && $('.tla-toggler:checked').size() == 1
                && ($('.tla-company-id').val() == 0 || $('.tla-company-id').val() == ''))
            {
                    Message('Please specify BIZ for TL alert !', 'error');
                    return false;
            }
            
            return true;
        },
        success: function(json){
            if (json.result == 'error') 
            {
                Message(json.message, 'error');
                return false;
            }
            
            $('.no-uploaded-files.view').hide();
            $(json.content.span).appendTo('span.uploaded-attachments');
            
            if ($('.tla-toggler') && $('.tla-toggler:checked').size() == 1
                && json.att_ids && json.att_ids.length > 0)
            {
                send_tl_alert(json.att_ids);
            }
            
            remove_modal();
            bind_prettyphoto();
            Message('Files were shared successfully !', 'okay');
        }
    });
    
    return false;    
};

/**
 * Удаляет атачмент из модального окна аплоадера
 * @version 20130224, zharkov
 */
var uploader_remove_attachment = function(obj, object_alias, object_id)
{
    if (!confirm('Remove attachment ?')) return false;
    
    var obj     = $(obj).parents('.uf-container');
    var oalias  = obj.data('oalias');
    var oid     = obj.data('oid');
    var id      = obj.data('id');
        
    $.ajax({
        url: '/attachment/remove',
        data : {
            object_alias    : oalias, 
            object_id       : oid,
            attachment_id   : id
        },
        success: function(json){
            if (json.result != 'okay') 
            {
                Message(json.message, 'error');
                return false;
            }
            
            $('.uf-container[data-id=' + id + ']').remove();
            var elementAttCount = $('.attachments-count');
            elementAttCount.html(parseInt(elementAttCount.html())-1);
            
            if ($('.table-list.uploaded-files .uf-container[data-id]').size() <= 0)
            {
                $('.no-uploaded-files.modalbox').show();
                $('.save.btn100b').hide();
                $('.tl-alert-container').hide();
            }
            
            if ($('.uploaded-attachments .uf-container[data-id]').size() <= 0)
            {
                $('.no-uploaded-files.view').show();
            }
        }
    });
    
    return false;    
};

/**
 * Устанавливает картинку как основную 
 * @version 20130224, zharkov
 */
var uploader_set_as_main = function(obj)
{
    var obj = $(obj).parents('.uf-container');
    var id  = obj.data('id');
    
    $.ajax({
        url: '/attachment/setasmain',
        data : {attachment_id : id },
        success: function(json){
            if (json.result != 'okay') 
            {
                Message(json.message, 'error');
                return false;
            }
            $('.uf-container').removeClass('is-main');
            $('.uf-container[data-id=' + id + ']').addClass('is-main');
        }
    });
    
    return false;    
};

/**
 * Изеняет пропорции окна при ресайзе
 * @version d10n
 */
var resize_container_content = function()
{
    var body_min_width          = parseInt($('body').css('min-width'));
    var _window     = {width: $(window).width()};
    var cw_rfilter    = $('.cw-rfilter').width();
    
    var content_main_width = _window.width-cw_rfilter-50;
    
    if (_window.width <= body_min_width)
    {
        content_main_width = body_min_width-cw_rfilter-50;
    }
    
    $('.cwc-content').css('width', content_main_width);
    
    _window.height      = $(window).height();
    var header_height   = $('#header').height();
    var footer_height   = $('#footer').height();
    var pname_bcrumb    = $('.pname-bcrumb').height();
    var cwc_hfilter     = $('.cwc-hfilter').height()
    var content_wrapper_height = _window.height -(header_height + pname_bcrumb + footer_height);
    
    $('.cw-rfilter').css('height', content_wrapper_height - 50);
    $('.cwc-content').css('height', content_wrapper_height -cwc_hfilter - 31);
    $('.content-wrapper, .cw-container').css('height', content_wrapper_height - 30);
};

/**
 * Инициализирует QQ FileUploader
 * @version 20130224, zharkov
 */
var bind_qq_uploader = function()
{
    var elementFileUploader     = $('.qq-fileuploader');
    var elementFileUploaderList = $('.qq-fileuploader-filelist');
    
    qq_uploader = new qq.FileUploader({
        element         :   elementFileUploader[0],
        listElement     :   elementFileUploaderList[0],
        params          :   {
            object_alias    : elementFileUploader.data('oalias'),
            object_id       : elementFileUploader.data('oid'),
            outputstyle     :'table-row'
        },
        action          :   '/attachment/tempupload/',
        template        :   '<div class="qq-uploader"><div class="qq-upload-button" style="color: #ffffff; z-index: 3;">Select Files</div></div>',
        onComplete     : function(id, fileName, response){
            
            if (response.error)
            {
                Message(response.error, 'error');
            }
            else
            {
                $('.no-uploaded-files.modalbox').hide();
                
                $(response.content.table_row).appendTo('.table-list.uploaded-files tbody');

                $('.save.btn100b').show();
                
                // 20130304, zharkov: нужно подправить механизм
                //$('.tl-alert-container').show();
            }
        }
    });
    
    bind_prettyphoto();
};

/**
 * Тогглер TLA формы
 * @version 20130227, d10n
 */
var toggle_tla_form = function(){
    $('.tla-form').toggle();    
    bind_biz_autocomplete();
};

/**
 * Send TL Alert
 * @param array att_ids
 * @returns boolean
 * @version 20130227, d10n
 */
var send_tl_alert = function (att_ids)
{
    var form            = new Object();
    var form_elements   = $('.tla-form').find('input, textarea, hidden');
    var obj             = $('.table-list.uploaded-files').find('.uf-container:first[data-id]');
    
    $.each(form_elements, function(key, value){
        var item = $(value);
        var name = item.attr('name');
        
        if (item.attr('type') == 'checkbox')
        {
            if (item.attr('checked') == undefined) return;
            name = name + '[' + key + ']';
        }
        form[name] = item.val();
    });
    
    form.object_alias   = obj.data('oalias');
    form.object_id      = obj.data('oid');
    form.att_ids        = att_ids;
    
    $.ajax({
        url: '/chat/sendtla',
        data : form,
        success: function(json){
            if (json.result != 'okay') 
            {
                Message(json.message, 'error');
                return false;
            }
        }
    });
    
    return true;
}

/**
 * Обработчик изменения Plate Id айтема
 * @version 20130302, zharkov
 */
var item_plateid_change = function(index)
{
    var plateid = $('#guid-' + index).val();

    if (!$.trim(plateid))
    {
        $('.guid-' + index).html('<i style="color: #999;">not defined</i>');
        $('#is_virtual-' + index).attr('checked', true);
    }
    else
    {
        $('.guid-' + index).text(plateid);
        $('#is_virtual-' + index).attr('checked', false);
    }
}

/**
 * Показывает модальное окно
 * @version 20130305, zharkov
 */
var show_modal = function(content, width, height, padding)
{
    var content = content || '';
    var width   = width || ($(window).width() - 100);//800;
    var height  = height || ($(window).height() - 100);//500;
    var padding = padding || 15;

    var content = '<div class="app-modal-close" onclick="remove_modal();">close window</div>' + content;
    var content = '<div style="position: absolute;' + (padding > 0 ? 'top: ' + padding + 'px; right: ' + padding + 'px; bottom: ' + padding + 'px; left: ' + padding + 'px;' : '') + '">' + content + '</div>';
    var content = '<div class="app-modal-inner" style="margin-top: -' + (height / 2) + 'px; margin-left: -' + (width / 2) + 'px; width: ' + (width - padding * 2) + 'px; height: ' + (height - padding * 2) + 'px;">' + content + '</div>';
    var content = '<div class="app-modal" onclick="remove_modal();"></div>' + content;    

    // удаляет предыдущие модальные окна
    remove_modal(true);
    
    // создает текущее
    $(content).appendTo('body');
};

/**
 * remove modal window
 * @version 20130305, zharkov
 * @version 20130426, zharkov - force_remove - remove modla window without any actions
 */
var remove_modal = function(force_remove)
{

    var force_remove = force_remove || false;
    
    if (force_remove)
    {
        $('.app-modal-inner').remove();
        $('.app-modal').remove();
        
        return;        
    }
	$('.app-modal-inner').remove();
    $('.app-modal').remove();    
	
	/*Sasha 30.04.13
    $('.app-modal-inner').each(function(){

        if ($(this).data('content-type') == 'message')
        {
            $(this).hide();

            object_alias    = $(this).data('object-alias');
            object_id       = $(this).data('object-id');
            
            title           = $('#chat-title').val();
            description     = tinyMCE.get('chat-description').getContent();
            recipient       = '';
            cc              = '';

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

            $.ajax({
                url: '/chat/savetemporarymessage',
                data : {
                    object_id       : object_id,
                    object_alias    : object_alias,
                    title           : title,
                    description     : description,
                    recipient       : recipient,
                    cc              : cc,
                    alert           : $('#chat-sa').val(),
                    pending         : $('#chat-p').val(),
                    deadline        : $('#chat-deadline').val()
                }
            });    
            
        }
        
        $(this).remove();
        
    });
    
    return;
	*/
}


/**
 * Инициализирует QQ Uploader
 */
var bind_uploader = function(o, complete_handler)
{
    var template        = o && o.template ? o.template : 'default';
    var url             = o && o.url ? o.url : '/attachment/upload/';
    var title           = o && o.title ? o.title : 'Upload Files';
    var filetype        = o && o.filetype ? o.filetype : 'all';         // {all|pictures}
    var prettyphoto     = o && o.prettyphoto ? o.prettyphoto : false;
    
    var elementFileUploader     = $('.qq-fileuploader');
    var elementFileUploaderList = $('.qq-fileuploader-filelist');
    
    qq_uploader = new qq.FileUploader({
        element     : elementFileUploader[0],
        listElement : elementFileUploaderList[0],
        params      : {
            object_alias    : elementFileUploader.data('oalias'),
            object_id       : elementFileUploader.data('oid'),
            template        : template,
            filetype        : filetype
        },
        action      : url,
        template    : '<div class="qq-uploader"><div class="qq-upload-button" style="color: #ffffff; z-index: 3;">' + title + '</div></div>',
        onComplete  : function(id, fileName, response){
            
            if (response.error)
            {
                Message(response.error, 'error');
            }
            else
            {
                // прячет сообщение об отсутствии объектов в списке
                $('.qq-fileuploader-filelist-empty').hide();
                
                // выполняет дополнительный обработчик если указан
                if (complete_handler) complete_handler();
                
                // инициализирует PrettyPhoto по требованию
                if (prettyphoto) bind_prettyphoto();
            }
        }
    });
};

/**
 * Displays a modal window to chat messages
 * @version 20130323, zharkov
 * @version 22.04.13, Sasha increased the width of the window from 1000 to 1180
 */
var show_chat_modal = function(object_alias, object_id, message_id)
{
    var message_id = (typeof message_id == 'undefined') ? 0 : message_id;

    var h       = 500;  //520
    var w       = 950;
    var left    = Number((screen.width/2)-(w/2));
    var top     = Number((screen.height/2)-(h/2));

    var time    = new Date().getTime();
    var url     = '/newmessage';
    
    if (message_id > 0)
    {
        url += '/answer/' + message_id;
    }
    else if (object_alias != '' && object_id > 0)
    {
        url += '/' + object_alias + '/' + object_id;
    }
    
    new_window = window.open(url, 'new_mesage_' + time, 'toolbar=no, location=no, directories=no, status=no, menubar=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	new_window.focus();
   
   /*Sasha 30.04.13
   $.ajax({
        url     : '/chat/getmodalbox',
        data    : {
            object_alias    : object_alias, 
            object_id       : object_id
        },
        success: function(json)
        {
            if (json.result == 'okay') 
            {
               
				show_modal(json.content); 
				
                // set modal content-type for processing on close
                if ($('.app-modal-inner')) 
                {
                    $('.app-modal-inner').data('content-type',  'message');
                    $('.app-modal-inner').data('object-alias',  object_alias);
                    $('.app-modal-inner').data('object-id',     object_id);
                }

                bind_mce_editors();
                bind_chat_modal_actions();
				
			   
			   if ($('.app-modal-inner')) 
               {
                    $('.app-modal-inner').data('content-type',  'message');
                    $('.app-modal-inner').data('object-alias',  object_alias);
                    $('.app-modal-inner').data('object-id',     object_id);
               }
			   
			   new_window = window.open('/newmessage/' + object_alias + '/' + object_id, 'New message', "width=1400, height=600");
			   new_window.focus();
            }
            else
            {
                Message(json.message, 'error');
            }            
        }
    });    
    
    return false;*/
}

/**
 * Displays a modal window to chat messages
 * @version 20130323, zharkov
 * @version 22.04.13, Sasha increased the width of the window from 1000 to 1180
 */
var show_chat_modal_for_user = function(object_alias, object_id, user_id)
{
    //console.log();
    var user_id = (typeof user_id == 'undefined') ? 0 : parseInt(user_id);

    var h       = 700;  //520
    var w       = 950;
    var left    = Number((screen.width/2)-(w/2));
    var top     = Number((screen.height/2)-(h/2));

    var time    = new Date().getTime();
    var url     = '/newmessage';
    
    if (user_id > 0)
    {
        url += '/to/' + user_id;
    }
    else if (object_alias != '' && object_id > 0)
    {
        url += '/' + object_alias + '/' + object_id;
    }
    
    new_window = window.open(url, 'new_mesage_' + time, 'toolbar=no, location=no, directories=no, status=no, menubar=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    new_window.focus();
}

/**
 * Инициализирует объекты модального окна чата
 * @version 20130324, zharkov
 */
var deprecated_bind_chat_modal_actions = function()
{
    var object_alias    = $('#qq_object_alias').length > 0 ? $('#qq_object_alias').val() : 'newmessage';
    var object_id       = $('#qq_object_id').length > 0 ? $('#qq_object_id').val() : 0;
    
    var uploader = new qq.FileUploader({
        element         :   $('#fileuploader')[0],
        listElement     :   $('#attachments')[0],
        params          :   {object_alias : object_alias, object_id : object_id, template : 'text'},
        action          :   '/attachment/upload/',
        debug           :   false,
        template        :   '<div class="qq-uploader"><div class="qq-upload-button-text">Attach Files</div></div>',
        classes         :   {
            button      : 'qq-upload-button-text',
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
    
    $('#chat-deadline').datepicker({
        showWeek: true
    });
    
    bind_biz_autocomplete('#chat-biz');
    
};

/**
 * Init object tooltips
 * @version: 20130410, zharkov
 */
var bind_tooltips = function()
{
    //20130520, sasha
    //bind_title_data();
    $('.tooltip').tooltip("destroy");
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
 * Показывает окно с сообщением чата
 * @version 20120708, novikov
 */
var show_chat_message = function(message_id)
{
    show_idle();
    
    $.ajax({
        url: '/chat/getmessage',
        data : {
            message_id : message_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                show_modal(json.content);
                
                bind_prettyphoto();
                bind_tooltips();
            }
            else
            {
                Message('Error receiving message content !', 'error');
            }
            
            hide_idle();
        }
    });    
};

/**
 * Показывает окно с сообщением чата
 * @version 20120708, novikov
 */
var show_email_message = function(email_id)
{
    show_idle();
    
    $.ajax({
        url: '/email/getmessage',
        data : {
            email_id : email_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                show_modal(json.content);
                
                bind_prettyphoto();
                bind_tooltips();
            }
            else
            {
                Message('Error receiving email content !', 'error');
            }
            
            hide_idle();
        }
    });    
};

/**
 * Add message to DOM message list
 */
var _chat_message_add_to_list = function(object_alias, message)
{
    if ((object_alias == 'chat') || (object_alias == 'to'))
    {
        if ($('#chat-messages li:first').length > 0)
        {
            $('#chat-messages li:first').before(message);         
        }
        else
        {
            $('#chat-messages').html(message);
        }
        
        $('.timeline-badge img').addClass('img-circle');
        $('.timeline-badge img').width($('.timeline-badge img').width() + 6);
        $('.timeline-badge img').height($('.timeline-badge img').height() + 5);
        $('.timeline-badge').width($('.timeline-badge img').width());
        $('.timeline-badge').height($('.timeline-badge img').height());
        $('.timeline-badge').css('opacity', '1');
        $('.timeline-badge img').css('opacity', '1');           
    }
    else
    {
        if ($('#blog_messages div:first').length > 0)
        {
            $('#blog_messages div:first').before(message);
        }
        else
        {
            $('#blog_messages').html(message);
        }                        
    }
};

var get_pending_counter = function()
{
    $.ajax({
		dataType: "json",
        url     : '/chat/getcountpendings',
        success : function(json){
            if (json.result == 'okay') 
            {
		$(".count-pending").html(json.count);
            }
        }
    });    
};

var rcolumn_width = 0;
var rcolumn_width_small = 0;

var show_hide_column = function() {
    //if(($('.column-side').width() == '50')) {
    if($('.column-side-hidden').length>0) {
        $('.icon-hide').removeClass('btn-default').addClass('btn-primary');
        $('.column-side').show();
        $('.column-main').removeClass('col-md-12');
        $('.column-main').addClass('col-md-9');  
        $('.column-side').removeClass('column-side-hidden');
    }else{
        $('.column-side').hide();  
        $('.icon-hide').removeClass('btn-primary').addClass('btn-default');
        $('.column-main').removeClass('col-md-9');
        $('.column-main').addClass('col-md-12');    
        $('.column-side').addClass('column-side-hidden');
    }
    //console.log($('.column-side-hidden').length);
};