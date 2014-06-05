$(function(){
    $('.btn-mirror').removeAttr('disabled');
    
    $('.chb').removeAttr('disabled');
    
    $(".chosen-select").chosen({
        width: "95%",
        disable_search_threshold: 10,
    });
    
    $(".location-checkbox").live("click", bind_positions_filter);
    
    $(".mirror-price").live("keyup", function(event)
    {
        if($(this).val() == "") {
            return;
        } 
        
        $(this).removeClass('');
    });
    
    /*       Активирует кнопку Save если нет пустых input price       */
    /*
    $(".mirror-price").live("change", function(event)
    {   
        //$("#button-save").removeAttr('disabled');
        /*
        //проверяем все input price на пустое значение
        $has_empty_price = mirror_check_empty_price();
        //проверяем на наличие класса empty
        $(".mirror-price").each(function()
        {   //если находим пустой input price деактивируем кнопку Save
            if($(this).hasClass("empty"))
            {
                
            }
            $("#button-save").attr('class', 'true');
            $("#button-save").attr('disabled', 'true');
        });*/
       /* console.log($('.mirror-price[value = ""]').parent('tr'));
    });    */  
    /*_____________________________________________________________________*/
    
    $(".select-stock").live("change", function(event){
        
        event.preventDefault();
        
        var stock_tr = $(this).parent().parent(); 
        
        var stock_id = $(this).val();

        //console.log(stock_id);

        var row_data = add_row(stock_id);
        
        $(stock_tr).find('.td-location select').empty();
        $(stock_tr).find('.td-deliverytime select').empty();
        
        for(var i = 0; i < row_data.locations.length; i++)
        {
            var obj = row_data.locations[i];
            var id = obj.location.id;
            var title = obj.location.title;
            
            $(stock_tr).find('.td-location select').append($('<option value="'+id+'">'+title+'</option>'));
        }
       
        for(var i = 0; i < row_data.deliverytimes.length; i++)
        {
            var obj = row_data.deliverytimes[i];
            var id = obj.deliverytime.id;
            var title = obj.deliverytime.title;
            
            $(stock_tr).find('.td-deliverytime select').append($('<option value="'+id+'">'+title+'</option>'));
        }        
    });
    
    
    
    //bind_positions_filter();
    $('.selected-control.btn-default').tooltip();
    
    $('.position-price-td').live("click", function(event){        
        event.preventDefault();
        event.stopPropagation();
        var val=$(this).text();
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
    
    $('.position-price-edit').live("keyup", function(event){
        //event.preventDefault();
        //event.stopPropagation();      
        var id = $(this).parent().data('id');
        var price = $(this).val();
        
        save_position_price(id, price);
        //console.log('blur val:'+id);
    });
    
    //Обработчик события изменения значения select в модальном окне mirror
    //!!! доработать - должно срабатывать только при редактировании !!!
    $("#mirrors-edit select").live("change", function(event)
    {
        //получаем параметры для сохранения:
        //текущая строка-родитель:
        var stock_tr = $(this).parent().parent();
        //параметры для таблицы mirrors:
        var mirror_id = $(stock_tr).find('.mirror-id').text();
        var position_id = $(stock_tr).find('.position-id').text();
        var stock_id = $(stock_tr).find('.select-stock option:selected').val();
        var location_id = $(stock_tr).find('.select-location option:selected').val();
        var deliverytime_id = $(stock_tr).find('.select-deliverytime option:selected').val();
        var price = $(stock_tr).find('input.mirror-price').val();
        
        save_mirror(mirror_id, position_id, stock_id, location_id, deliverytime_id, price);
        //console.log(stockholder_id);
    });
});

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

/* Получает данные о mirror для позиции
 * 
 * @param {type} position_id
 * @returns {undefined}
 */
var get_mirrior_info = function(position_id)
{
    $.ajax ({
        url: '/mirror/getmirror',
        data : {
            position_id : position_id
        },
        type: 'POST',               
        success: function(json)
        {
            console.log(json);
        }
    });    
}

var mirror_check_empty_price = function(){
    /*
    $(".mirror-price").each(function()
    {   
        if($(this).val() == "")
        {   //если находим присваиваем класс empty
            $(this).addClass("empty");
            //обводим input красным
            $(this).css("border", "solid 1px red");
        }
        else
        {   //иначе класс mirror-price
            $(this).removeAttr("class");
            $(this).addClass("mirror-price");
            //обводим input зеленым
            $(this).css("border", "solid 1px green");
        }
    });*/
    if($('.mirror-price[value = ""]').lenth>0) {
        return $result = true;
    }
}

var add_row = function(stock_id){
    var json_result = $.ajax ({
        url: '/mirror/addrow',
        data : {
            stock_id : stock_id
        },
        type: 'POST',               
        success: function(json)
        {
            return json;
        }
    });
    
    var json_data = jQuery.parseJSON(json_result.responseText);
    
    return json_data;
    //return json_result;
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
            //$('#btn-positions-edit').removeAttr('disabled');
            //$('#btn-positions-edit').removeAttr('title');
            //кнопка Edit
            $('#btn-positions-edit.btn-default').removeAttr('title');
            $('#btn-positions-edit.btn-default').tooltip('destroy');
            $('#btn-positions-edit.btn-default').removeClass('btn-default');
            $('#btn-positions-edit').addClass('btn-primary');
        }
        else
        {
            //$('#btn-positions-edit').attr('disabled', 'true');
            //кнопка Edit
            $('#btn-positions-edit.btn-primary').attr('title', 'Please, select item');
            $('#btn-positions-edit.btn-primary').removeClass('btn-primary');
            $('#btn-positions-edit').addClass('btn-default');
            $('#btn-positions-edit.btn-default').tooltip();
        }
        
        //$('.selected-control').removeAttr('disabled');
        //$('.selected-control').removeAttr('title');
        $('.selected-control.btn-default').removeAttr('title');
        $('.selected-control.btn-default').tooltip('destroy');
        $('.selected-control.btn-default').removeClass('btn-default');
        $('.selected-control').addClass('btn-primary');
    }
    else
    {
        //$('#btn-positions-edit').show();
        //$('.selected-control').show(); 
        //$('#btn-positions-edit').attr('disabled', 'true');
        //$('#btn-positions-edit').attr('title', 'Please, select a position');
        //кнопка Edit
        $('#btn-positions-edit.btn-primary').attr('title', 'Please, select position');
        $('#btn-positions-edit.btn-primary').removeClass('btn-primary');
        $('#btn-positions-edit').addClass('btn-default');
        $('#btn-positions-edit.btn-default').tooltip();
        
        //$('.selected-control').attr('disabled', 'true');
        //$('.selected-control').attr('title', 'Please, select a position');
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
 *Зеркало
 *
 *Позволяет создавать и редактировать зеркало (зеркало - отображение позиции на нескольких складах с подменой Location)
 *@param {integer} position_id id позиции
 *@version 20140521
 *@author Gonchar
 *
 *@version 20140605
 *@modified Uskov
 */
var create_mirror_from_selected = function(position_id)
{
    $.ajax({
        url     : '/mirror/getmirror',
        
        data    : {
            position_id : position_id
        },
    
        success: function(json){
            if (json.result === 'new_mirror' || json.result === 'edit_mirror') {
                console.log(json);
                $('body').append(json.content);
                $('#myModal'+position_id).modal();
                //$('#steelgrades-select').html('');
                //$('#steelgrades-select').html(json.content);
            } /*
            if (json.result === 'edit_mirror') {
                console.log(json);
                $('body').append(json.content);
                $('#myModal'+position_id).modal();
            }*/
        }
    });
    $("#button-save").attr('disabled', 'true');
    //по умолчанию input price обводим красным т.к. пустой
    $(".mirror-price").css("border", "solid 1px red");
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
		    console.log(json);
		   //$('#steelgrades-select').html('');
		   //$('#steelgrades-select').html(json.content);
		}
		else
		{
		    console.log(json);
		   //$('#steelgrades-select').html('');
		   //$('#steelgrades-select').html(json.content);
		}
	    }
    });    
    
    console.log(position_id + ': hidden='+hidden);
};



/**
 *Добавляет строку в таблицу редактирования Mirror
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
var mirror_add_row = function()
{
    
    var number_rows = $("#mirrors-edit tr").size();
    var index_prev_row = number_rows-1;
    var prev_row_html = $("#mirrors-edit tr:eq("+index_prev_row+")").html();
    var prev_stock_val = $(".select-stock:eq("+index_prev_row+")").val();
    var css = $("#mirrors-edit tr:first").attr('style');
    
    console.log(prev_stock_val);
    //$(".add-row").before("<tr>"+row+"</tr><tr><td colspan='2'><hr/></td></tr>");
    $("#mirrors-edit tbody").append("<tr>"+prev_row_html+"</tr>");
    
   
    if(number_rows > 1) {
        $("#mirrors-edit .btn").removeAttr('disabled');
    }
    
    $("#mirrors-edit tr:last").html();
    
    $(".select-stock:last [value='"+prev_stock_val+"']").attr("selected", "selected");
    
    //при добавлении новой строки деактивируем кнопку Save (защита от пустого price)
    $("#button-save").attr('disabled', 'true');
    $(".mirror-price:last").css("border", "solid 1px red");
};

/**
 *Удаляет строку из таблицы редактирования Mirror
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
var mirror_del_row = function(obj)
{

    //var hr = $(obj).parent().parent().next().remove();
    var row = $(obj).parent().parent().remove();
    var number_rows = $("#mirrors-edit tr").size();
    //$(".add-row").before("<tr>"+row+"</tr><tr><td colspan='3'><hr/></td></tr>");
    if(number_rows === 1) {
        $("#mirrors-edit .btn").attr('disabled', 'true');
    }
    console.log(number_rows);
};

var mirror_del_all = function()
{
    $("#mirrors-edit tr:gt(0)").each(function(){
        $(this).remove();
    })
    $("#mirrors-edit .btn").attr('disabled', 'true');
};

/*
 * Сохранение mirror при изменении в любом поле формы. Сохраняет 1 mirror, select которого изменен
 * Поле input price должно быть заполнено.
 * Вызывается при изменении в select и price
 * 
 * 
 */
var save_mirror = function(mirror_id, position_id, stock_id, location_id, deliverytime_id, price)
{
    $.ajax ({
        url: '/mirror/savemirror',
        data : {
            mirror_id : mirror_id,
            position_id : position_id,
            stock_id : stock_id,
            location_id : location_id,
            deliverytime_id : deliverytime_id,
            price: price
        },
        type: 'POST',               
        success: function(json)
        {
            console.log(json);
        }
    });    
};