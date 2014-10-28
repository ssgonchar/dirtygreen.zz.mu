//document.ready function
$(function(){
       
    /*
     * Эмулирует нажатие кнопки Find после нажатия Enter при фокусе в input.find-parametr
     * @version 20140619, Uskov
     */
    $(".find-parametr").live("keyup", function()
    {
        $(this).keypress(function(event){
            if(event.keyCode == 13) 
            {
                $("input[value=Find]").click();
                return false;
                //console.log("key press");
            }
        });
    });
    
    /*
    * прячет Toolbox при открытии страницы
    * меняет текст Toolbox на Search Tools
    * оформление текста в параграфах формы поиска 
    */
    toolbox_view_options();
    
    //Меняем текст кнопки Hide/Show Search Tools, показываем/скрываем таблицу Positions
    $("button.icon-hide").live("click", function()
    {
        show_hide_search_tools();
        //выравниваем высоты колонок формы по высоте первой колонки
        set_columns_height();
    });
    
    //выравниваем высоты колонок формы по высоте первой колонки при смене склада
    $("#stock").live("change", function()
    {
        set_columns_height();
    });
    
    $('.btn-mirror').removeAttr('disabled');
    
    $('.chb').removeAttr('disabled');
    
    $(".chosen-select").chosen({
        width: "95%",
        disable_search_threshold: 10,
    });
    
    $(".location-checkbox").live("click", bind_positions_filter);
    
    //bind_positions_filter();
    $('.selected-control.btn-default').tooltip();
    
    $('.position-price-td').live("click", function(event){        
        event.preventDefault();
        event.stopPropagation();
        var val = $(this).text();
        //$(this).html("<input type='text' value='"+val+">");
        var obj = $(this);
        
        var html="<input type='text' class='position-price-edit' value='"+val+"'>";
        
        obj.html(html);
        console.log(html);
    });
    
    $('.position-price-edit').live("click", function(event){
        event.preventDefault();
        event.stopPropagation();        
    });
    //автосохранение 
    $('.position-price-edit').live("keyup", function(event){
        //event.preventDefault();
        //event.stopPropagation();      
        var id = $(this).parent().data('id');
        var price = $(this).val();
        
        save_position_price(id, price);
        //console.log('blur val:'+id);
    });
        
});

//выравнивает высоты колонок формы по высоте первой колонки
//задает верхний margin для кнопки find
var set_columns_height = function()
{
    var first_column_height = $("#first-column").css("height");
    $("#second-column").css("height", first_column_height);
    $("#third-column").css("height", first_column_height);
    //вычисляем margin-top для кнопки Find (кнопка перенесена в контекст - поэтому изменение margin закомментил)
    //var margin_top = parseInt(first_column_height.replace(/\D+/g,"") - 90);
    //задаем margin-top
    //$('input[value=Find]').css("margin-top", margin_top + "px");
    //текст checkbox - обычный
    $(".row label").css("font-weight", "normal");
    //стираем значения всех input
    $(".find-parametr").val("");
}

/*
 * прячет Toolbox при открытии страницы
 * меняет текст Toolbox на Search Tools
 * оформление текста в параграфах формы поиска
 * @version 20140615, Uskov
 */
var toolbox_view_options = function()
{
    //прячет Toolbox при открытии страницы
    $('.column-side').hide();  
    $('.icon-hide').removeClass('btn-primary').addClass('btn-default');
    $('.column-main').removeClass('col-md-9');
    $('.column-main').addClass('col-md-12');
    $('.column-side').addClass('column-side-hidden');

    //меняем текст Toolbox на Search Tools
    $('button.icon-hide').text("");
    $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Show Search Tools");

    //подчеркиваем надписи в параграфах
    $(".row p.name").css("font-weight", "bold");
    //текст checkbox - обычный
    $(".row label").css("font-weight", "normal");
};

/*
 * Меняет текст кнопки Hide/Show Search Tools 
 * Показывает/скрывает таблицу Positions
 * @version 20140615, Uskov
 */
var show_hide_search_tools = function()
{
    if($('.column-side-hidden').length>0){
        //прячем таблицу Positions
        $("#position-table").show();
        //меняем текст кнопки
        $('button.icon-hide').text("");
        $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Show Search Tools");
    }else{
        //показываем таблицу Positions
        $("#position-table").hide();
        //меняем текст кнопки
        $('button.icon-hide').text("");
        $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Hide Search Tools");
    }
};
    
var save_position_price = function(id, price) {
    $.ajax ({
        url: '/position/saveprice',
        data : {
            position_id : id,
            price: price
        },
        type: 'POST',               
        success: function(json)
        {
            console.log(json);
        }
    });    
};

/**
 * Обертка для сткндартной процедуры отображения контеста айтема
 * @version 20121219, zharkov
 */
var show_position_item_context = function(event, item_id, is_revision)
{
    if (is_revision > 0)
    {
        location.href = '/item/' + item_id + '/history';
    }
    else
    {
        show_item_context(event, item_id);
    }
};

/**
 * Прячет айтемы позиции
 * @version 20121101, zharkov
 */
var hide_items = function(position_id)
{
    if ($('#position-items-' + position_id).length > 0)
    {
        $('#position-items-' + position_id).hide();
    }
};

/**
 * Показывает айтемы позиции
 * @version 20121101, zharkov
 */
var show_items = function(position_id, is_revision)
{   
    destroy_obj('js-position-' + position_id + '-context');	//destroy_obj() удаляет объект DOM по идентификатору
	
    if ($('#position-items-' + position_id).length > 0)	//проверяем есть ли уже загруженное окно итемов позиции
    {
        $('#position-items-' + position_id).show();	//если да - просто показываем
    }
    else						//если нет:
    {
        show_idle();					//Показывает модальное окно "loading..."
        
        $.ajax({				//делаем ajax запрос
            url     : '/position/getitems',	//URL к запросу
            data    : { 			//данные, которые отсылаются на сервер ( ключ/значение )
                position_id : position_id,
                is_revision : is_revision	//в данном значении передается номер ревизии склада (не использовать ревизии! функционал недоработан!)
            },
            success: function(json){		//Функция, которая исполняется всякий раз после удачного завершения запроса AJAX.
                if (json.result == 'okay') 	
                {
    //после строки позиции вставляем сформированную ниже строку (контейнер), в которую вложится шаблон, заполненый полученными данными
    //контейнер (td) обьединяет по горизонтали 17 ячеек
                    $('#position-' + position_id).after('<tr id="position-items-' + position_id + '"><td colspan="17" style="padding: 0 10px; text-align: left;">' + json.content + '</td></tr>');
                    bind_prettyphoto();		//Инициализирует prettyPhoto
                    
                    hide_idle();		//Прячет модальное окно "loading..."
                }
                else
                {
                    alert(json);
                }
            }
        });
    }
}

/**
 * Показывает блок с действиями над позицией или перенаправляет на страницу истории позиции
 * @version 20120916, zharkov
 */
var position_click = function(event, position_id, is_revision)
{
    if (is_revision > 0)
    {
        location.href = '/position/' + position_id + '/history';
    }
    else
    {	//если блок просмотра итемов позиции открыт - прячем
        if ($('#position-items-' + position_id).length > 0 && $('#position-items-' + position_id).is(':visible'))
        {
            $('#position-items-' + position_id).hide();
        }
        else	//иначе функция - показать итемы позиции
        {
            show_items(position_id, is_revision);
        }
    }
    
/* 20130824, zharkov: old version
    if (is_revision > 0)
    {
        location.href = '/position/' + position_id + '/history';
    }
    else
    {
        var width   = 300;
        var height  = 150;
        var coords  = getMouseCoords(event);
        var size    = getClientSize();

        if (coords.x + (width + 20) > size.width) coords.x -= (width);
        if (coords.x < 0) coords.x = 0;

        if (coords.y + height > size.height) coords.y -= (height + 10); else coords.y += 10;
        if (coords.y < 0) coords.y = 0;
        
        
        $.ajax({
            url     : '/position/getcontext',
            data    : { 
                position_id : position_id,
                is_revision : is_revision
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('.js-position-context').remove();

                    jQuery('<div/>', {
                        id      : 'js-position-' + position_id + '-context',
                        html    : json.content,
                        'class' : 'js-position-context context',
                        'style' : 'position:absolute; top: ' + coords.y + 'px; left: ' + coords.x + 'px; width: ' + width + 'px; height: ' + height + 'px;'
                    }).appendTo('body');  // '#container'

		            bind_prettyphoto();
                }
            }
        });    
    }
*/    
}

/**
 * Добавляет выбранные позиции и айтемы в документ
 * 
 * @version: 20130228, d10n: расширена применения к StockOffer
 * @version: 20121023, d10n: расширена применения к RA
 * @version: 20120822, zharkov
 */
var add_selected_to_document = function(doc_alias, doc_id)
{
    var stock_id        = $('#stock').val();
    var position_ids    = '';
    var item_ids        = '';

    // selected positions
    $('.cb-row-position:checked').each(function(){
        position_ids += '' + (position_ids != '' ? ',' : '') + $(this).val();
    });

    // selected items
    $('.cb-row-item:checked').each(function(){
        item_ids += '' + (item_ids != '' ? ',' : '') + $(this).val();
    });    
    
    if (position_ids == '' && item_ids == '')
    {
        var doc_name = '';
        switch(doc_alias)
        {
            case 'qc' :
            case 'newqc' :
                doc_name = 'Quality Certificate';
                break;

            case 'ra' :
            case 'newra' :
                doc_name = 'Release Advice';
                break;
                
           case 'stockoffer' :
                doc_name = 'Stock Offer';
                break;
                
            default :
                doc_name = 'Order';
        }
        //alert('Please select Positions or Items for ' + ((doc_alias == 'ra' || doc_alias == 'newra') ? 'Release Advice' : 'Order') + ' !');
        alert('Please select Positions or Items for ' + doc_name + ' !');
        return;
    }

    // hide order select block
    $('#pos-orders-list-container').hide();
    
    $.ajax({
        url     : '/document/addpositions',
        data    : { 
            doc_alias       : doc_alias, 
            doc_id          : doc_id,
            stock_id        : stock_id,
            position_ids    : position_ids,
            item_ids        : item_ids
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                location.href = json.href;
            }
            else if (json.message)
            {
                Message(json.message, 'error');
            }
        }
    });    
};


/**
* Обрабатывает ответ аякс-контроллера при удалении айтема.
* Вызывается из app.js: send_item_remove
* 
* @version 20120429, zharkov
*/
var item_remove_hadler = function(json)
{
    if (json.result == 'okay') 
    {
        var_positions_length = 0;
        $.each(json.positions, function(position_id, params){
            if (position_id > 0) var_positions_length++;
        });
        
        // если обновляются несколько позиций, страница перегружается
        if (var_positions_length > 1)
        {
            location.href = location.href;
        }
        else
        {        
            // removing items
            var_position_id = 0;
            $.each(json.items, function(item_id, position_id){
                $('#position-' + position_id + '-item-' + item_id).remove();
                var_position_id = position_id;
            });
                            
            // updating position qtty
            $.each(json.positions, function(position_id, params){                        
                
                if (params.qtty == 0)
                {
                    $('#position-' + position_id).remove();
                    if ($('#position-items-' + position_id)) $('#position-items-' + position_id).remove();                            
                }
                else
                {
                    $('#position-qtty-' + position_id).html(params.qtty);
                    $('#position-weight-' + position_id).html(numberRound(params.weight, 2));
                    $('#position-value-' + position_id).html(numberRound(params.value, 2));
                }                        
            });
            
            calc_total('position');
            if ($('selected-actions-item-position-' + var_position_id)) $('selected-actions-item-position-' + var_position_id).hide();
        }
    }
    else
    {
        alert('Error receiving data !');
    }    
};

/**
 * Показывает или прячет блок управления выбранными айтемами
 * 
 * @version 20120428, zharkov
 */
var show_selected_items_actions = function(position_id)
{
    // 20130226, zharkov: если есть неудаляемые айтемы, ссылка на удаление прячется
    if ($('.position-' + position_id + '-item-eternal:checked').length > 0)
    {
        $('.position-' + position_id + '-removeitems').hide();
    }
    else
    {
        $('.position-' + position_id + '-removeitems').show();
    }
    
    // показывает действия над группой айтемов
    if ($('.cb-row-item-position-' + position_id + ':checked').length > 0)
    {
        $('#selected-actions-item-position-' + position_id).show();
    }
    else
    {
        $('#selected-actions-item-position-' + position_id).hide();
    }    
};

/**
 * Подсчитывает количество вес и сумму по выделенным позициям и айтемам
 * 
 * @version 20120424, zharkov
 */
var calc_selected = function()
{

    var position_ids    = [];
    var item_ids        = [];

    // selected positions
    $('.cb-row-position:checked').each(function(){
        position_ids.push($(this).val())
    });

    // selected items
    $('.cb-row-item:checked').each(function(){
        position_id = $('#item-' + $(this).val() + '-position').val();
        index       = jQuery.inArray(position_id, position_ids);
        
        // remove position from positions array if item was selected
        if (index > -1) position_ids.splice(index, 1);
        
        // add position_id in items array
        item_ids.push(position_id);
    });    
    
    var qtty    = 0;
    var weight  = 0;
    var sum     = 0;
    
    $.each(position_ids, function(index, value) { 
        qtty    += parseNumber(getVal($('#position-qtty-' + value)));
        weight  += parseNumber(getVal($('#position-weight-' + value)));
        sum     += parseNumber(getVal($('#position-value-' + value)));
    });
    
    $.each(item_ids, function(index, value) { 
        uweight = parseNumber(getVal($('#position-unitweight-' + value))); 
        qtty    += 1;
        weight  += uweight
        sum     += uweight * parseNumber(getVal($('#position-price-' + value)));
    });    
    
    $('#lbl-selected-qtty').html(qtty);    
    $('#lbl-selected-weight').html(numberRound(weight, 2));    
    $('#lbl-selected-value').html(numberRound(sum, 2));    
};

/**
 * Перенаправляет пользователя на страницу резервации выбранных позиций и айтемов
 * 
 * @version 20120420, zharkov
 * @version: 20140515, uskov
 *
 * Работает только если выбран итем (button class='btn-primary')
 */
var reserve_selected = function()
{
    if ($('.selected-control').hasClass("btn-primary"))
    {
        var position_ids    = '';
        var items_ids       = '';
    
        // selected positions
        $('.cb-row-position:checked').each(function(){
            position_ids += '' + (position_ids != '' ? ',' : '') + $(this).val();
        });
    
        // selected items
        $('.cb-row-item:checked').each(function(){
            items_ids += '' + (items_ids != '' ? ',' : '') + $(this).val();
        });    
    
        location.href = '/position/reservation/positions:' + position_ids + ';items:' + items_ids;
    }
    else{
        alert('Please, select positions or items!');
    }
};

/**
 * Активирует/дезактивирует блок с групповыми действиями над выбранными позициями или айтемами
 * 
 * @version: 20120420, zharkov
 * @version: 20140514, uskov
 * 
*/
var show_group_actions = function()
{
    var positions_length    = $('.cb-row-position:checked').length;
    var items_length        = $('.cb-row-item:checked').length;

    if (positions_length > 0 || items_length > 0)
    {
        if (items_length == 0) //если выбрана только позиция, делаем активной кнопку Edit
        {
            //кнопка Edit
            $('#btn-positions-edit.btn-default').removeAttr('title');
            $('#btn-positions-edit.btn-default').tooltip('destroy');
            $('#btn-positions-edit.btn-default').removeClass('btn-default');
            $('#btn-positions-edit').addClass('btn-primary');
        }
        else
        {
            //кнопка Edit
            $('#btn-positions-edit.btn-primary').attr('title', 'Please, select item');
            $('#btn-positions-edit.btn-primary').removeClass('btn-primary');
            $('#btn-positions-edit').addClass('btn-default');
            $('#btn-positions-edit.btn-default').tooltip();
        }
        $('.selected-control.btn-default').removeAttr('title');
        $('.selected-control.btn-default').tooltip('destroy');
        $('.selected-control.btn-default').removeClass('btn-default');
        $('.selected-control').addClass('btn-primary');
    }
    else
    {
        //кнопка Edit
        $('#btn-positions-edit.btn-primary').attr('title', 'Please, select position');
        $('#btn-positions-edit.btn-primary').removeClass('btn-primary');
        $('#btn-positions-edit').addClass('btn-default');
        $('#btn-positions-edit.btn-default').tooltip();
        
        $('.selected-control.btn-primary').attr('title', 'Please, select item');
        $('.selected-control.btn-primary').removeClass('btn-primary');
        $('.selected-control').addClass('btn-default');
        $('.selected-control.btn-default').tooltip();
    }
};

/**
 * Открывает блок выбора заказа
 */
var show_order_select = function()
{
    var stock_id = $('#stock').val();
    if (stock_id == 0) 
    {
        alert('Stock must be specified !');
        return false;
    }
    
    $.ajax({
        url: '/order/getactivelist',
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
 * Добавляет позиции к заказу
 */
var put_positions_to_order = function(order_id)
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
    
    if (position_ids == '' && item_ids == '')
    {
        alert('Please select Positions or Items for Order !');
        return;
    }
    
    
    // hide order select block
    $('#pos-orders-list-container').hide();
    
    $.ajax({
        url: '/order/putpositionstoorder',
        data : { 
            order_id        : order_id,
            stock_id        : stock_id,
            position_ids    : position_ids,
            item_ids        : item_ids
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                location.href = json.href;
            }
        }
    });    
};

/**
 * Показывает блок с действиями над позицией
 */
var show_position_actions = function(obj, position_id)
{
    var parent      = $(obj).parent();
    var position    = $(parent).position();

    $('.js-pos-actions').remove();
    
    jQuery('<div/>', {
        id: 'js-pos-' + position_id + '-actions',
        html: 
            '<ul>'
            +'<li style="padding-top: 7px;"><a class="edit" href="/position/edit/' + position_id + '">edit position</a></li>'
            +'<li style="padding-top: 7px;"><a class="history" href="/position/history/' + position_id + '">view history</a></li>'
            +'</ul>',
        'class' : 'js-obj-actions',
        mouseleave : function(){
            $(this).remove();
        },
        mouseenter : function(){
            $(this).clearQueue();
        },
        'style': 'top: ' + position.top + 'px; left: ' + (position.left - 1) + 'px;'
    }).appendTo('#container');

    $(obj).mouseleave(function(){
        $('#js-pos-' + position_id + '-actions').delay(300).fadeOut(50);
    });        
    
};

/**
 * Показывает блок с действиями над айтемом
 * @deprecated 20121219, zharkov: запущена новая версия контекстного меню айтема
 */
var deprecated_show_item_block = function(obj, item_id, position_id, is_revision, use_parent)
{
    if (is_revision > 0)
    {
        location.href = '/item/' + item_id + '/history';
    }
    else
    {
        if (use_parent > 0)
        {
            var parent      = $(obj).parent();
            var position    = $(parent).position();
        }
        else
        {
            var position = $(obj).position();        
        }

        $('.js-item-actions').remove();

        jQuery('<div/>', {
            id: 'js-item-' + item_id + '-actions',
            html: 
                '<ul>'
                + '<li style="padding-top: 7px;"><a class="edit" href="/position/' + position_id + '/item/edit/' + item_id + '">edit item</a></li>'
                + '<li style="padding-top: 7px;"><a class="move" href="/position/' + position_id + '/item/move/' + item_id + '">move item</a></li>'
                + '<li style="padding-top: 7px;"><a class="twin" href="/position/' + position_id + '/item/twin/' + item_id + '">twin item</a></li>'
                + '<li style="padding-top: 7px;"><a class="cut" href="/position/' + position_id + '/item/cut/' + item_id + '">cut item</a></li>'
                + '<li style="padding-top: 7px;"><a class="delete" href="javascript: void(0);" onclick="item_remove(' + item_id + ', ' + position_id + ')">delete item</a></li>'
                + '<li style="padding-top: 7px;"><a class="history" href="/position/' + position_id + '/item/history/' + item_id + '">view history</a></li>'
                + '<li style="padding-top: 7px;"><a class="pictures" href="/steelitem/' + item_id + '/dropbox">pictures</a></li>'
                + '</ul>',
            'class' : 'js-obj-actions',
            mouseleave : function(){
                $(this).remove();
            },
            mouseenter : function(){
                $(this).clearQueue();
            },
            'style': 'top: ' + position.top + 'px; left: ' + (position.left - 1) + 'px;'
        }).appendTo('#container');

        $(obj).mouseleave(function(){
            $('#js-item-' + item_id + '-actions').delay(300).fadeOut(50);
        });    
        
    }   
};

var deprecated_get_steelgrades = function()
{
    var stock_id = parseInt($('#stock').val());
    
    if (stock_id <=0)
    {
        $('#steelgrade-list').html('');
        $('#steelgrade-list').html('<option value="0">--</option>');
        return; 
    }
    
    $.ajax({
            url     : '/position/getsteelgrades',
            data    : { 
                stock_id : stock_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                   $('#steelgrades-select').html('');
                   $('#steelgrades-select').html(json.content);
                }
                else
                {
                   $('#steelgrades-select').html('');
                   $('#steelgrades-select').html(json.content);
                }
            }
    });
};


/**
 *Изменить видимость на складе
 *
 *Позволяет показывать и прятать позиции на складе. Под складом здесь следует понимать пользовательскую часть системы (myroom.platesahead.com/stock, myroom.steelemotion.com/stock)
 *
 *@param object obj объект, который вызвал событие
 *@param int position_id id позиции
 *@param bool hidden скрыть, если true
 *
 *@version 20140521
 *@author Gonchar
 */
var change_visibility_in_stock = function(obj, position_id, hidden)
{
    $.ajax({
	    url     : '/position/changevisibility',
	    data    : { 
		position_id : position_id,
		hidden : hidden
	    },
	    success: function(json){
		if (json.result == 'okay') 
		{
		    if(hidden == true) Message('Position was hidden from external stock!', 'okay');
		    if(hidden == false) Message('Now the position is displayed on the external stock!', 'okay');
		   //$('#steelgrades-select').html('');
		   //$('#steelgrades-select').html(json.content);
		}
		else
		{
		    //console.log(json);
		   //$('#steelgrades-select').html('');
		   //$('#steelgrades-select').html(json.content);
		}
	    }
    });    
    
    console.log(position_id + ': hidden='+hidden);
};
