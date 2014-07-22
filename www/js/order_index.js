$(function(){
    
    // company
    $( "#company_title" ).autocomplete({
        source: function( request, response ) {
            
            $('#company_id').val(0);
            
            $.ajax({
                url     : "/company/getlistbytitle",
                data    : {
                    maxrows : 25,
                    title   : request.term
                },
                success : function( data ) {
                    console.log(data);
		    response( $.map( data.list, function( item ) {
                        //var list_title = item.company.title+'('+item.company.city.title+')';
			var list_title = item.company.title;
			if (typeof item.company.city !== 'undefined' ) {
			    list_title += ' ('+item.company.city.title+')';
			}
			//console.log(item);
			//console.log(typeof item.company.city);
			return {
                            label: list_title,
                            value: item.company.id
                        }
                    }));
                }
            });
            
        },
        minLength: 3,
        select: function( event, ui ) {
            
            if (ui.item)
            {
                $('#company_title').val(ui.item.label);
                $('#company_id').val(ui.item.value);
            }
            else
            {
                $('#company_id').val(0);
            }
            
            return false;
            
        },
        open: function() { },
        close: function() { }
    });
    
    
    // period
    $('#period_from').datepicker({showWeek: true});
    $('#period_to').datepicker({showWeek: true});


    // start create RA
    $('.check-all-orders-in-process').on('click', function(){
        if ($(this).attr('checked') == 'checked')
        {
            $('.order-in-process').attr('checked', 'checked');
            $('.ra-create').show();
        }
        else
        {
            $('.order-in-process').removeAttr('checked');
            $('.ra-create').hide();
        }
    });
    
    $('.order-in-process').on('click', function(){
        var checkall_HTML_element = $('.check-all-orders-in-process');
        
        $('.ra-create').hide();
        checkall_HTML_element.removeAttr('checked');
        
        if ($('.order-in-process:checked').length == $('.order-in-process').length)
        {
            checkall_HTML_element.attr('checked', 'checked');
        }
        
        if ($('.order-in-process:checked').length > 0)
        {
            $('.ra-create').show();
        }
    });    
});

/**
 * Показывает блок с действиями для объекта
 */
var show_object_block = function(obj, obj_id, order_status)
{
    var parent      = $(obj).parent();
    var position    = $(parent).position();

    $('.js-obj-actions').remove();

    jQuery('<div/>', {
        id: 'js-obj-' + obj_id + '-actions',
        html: 
            '<ul>'
            + '<li><a class="edit" href="/order/edit/' + obj_id + '">edit</a></li>'
            + '<li><a class="view" href="/order/view/' + obj_id + '">view</a></li>'
            + (order_status == '' ? '<li><a class="delete" href="javascript: void(0);" onclick="order_remove(' + obj_id + ')">delete</a></li>' : '')
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
        $('#js-obj-' + obj_id + '-actions').delay(300).fadeOut(50);
    });    
};

/**
 * Удаляет заказ
 */
var order_remove = function(order_id)
{
    if (!confirm('Am I sure that I want to remove the order ?')) return false;
    
    $.ajax({
        url     : "/order/remove",
        data    : {
            order_id : order_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#order-' + order_id).remove();
                Message('Order was successfully removed !', 'okay');
            }
            else
            {
                Message('Error removing order !', 'error');
            }                
        }
    });    
};

/**
 * Показывает / Скрывает Итемы в таблице заказов
 */
var itemsToogle = function(view_mod)
{
	//console.log();
	
	if(view_mod) {
		$('.fl:visible + tr').show();
        
		$('td.view_item').attr('rowspan', 2);
		//$('tr.view_item').show('fast');
	} else {
		$('tr.view_item').hide('fast');
		$('td.view_item').attr('rowspan', 1);
		//$('tr.view_item').hide('fast');
	}
};

/**
 * Показывает айтемы заказа
 * @version 20121101, zharkov
 */
var show_items = function(obj, order_id, is_revision)
{
    
   var display=$('#order-items-' + order_id).css('display');
   
   if(display=='table-row'){
        //console.log(display);
        $(obj).html('Show items');
        hide_items(order_id);
        return false;
   }
    
    $(obj).html('Hide items');
    destroy_obj('js-order-' + order_id + '-context');	//destroy_obj() удаляет объект DOM по идентификатору
    
    if ($('#order-items-' + order_id).length > 0)	//проверяем есть ли уже загруженное окно итемов позиции
    {
        $('#order-items-' + order_id).show();	//если да - просто показываем
    }
    else						//если нет:
    {
        show_idle();					//Показывает модальное окно "loading..."
        
        $.ajax({				//делаем ajax запрос
            url     : '/order/getitems',	//URL к запросу
            data    : { 			//данные, которые отсылаются на сервер ( ключ/значение )
                order_id : order_id,
                is_revision : is_revision	//в данном значении передается номер ревизии склада (не использовать ревизии! функционал недоработан!)
            },
            success: function(json){		//Функция, которая исполняется всякий раз после удачного завершения запроса AJAX.
                if (json.result == 'okay') 	
                {
    //после строки позиции вставляем сформированную ниже строку (контейнер), в которую вложится шаблон, заполненый полученными данными
    //контейнер (td) обьединяет по горизонтали 12 ячеек
                    $('#order-' + order_id).after('<tr id="order-items-' + order_id + '"><td colspan="13" style="padding: 0 10px; text-align: left;">' + json.content + '</td></tr>');
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
    
    
};

var hide_items = function(order_id)
{
    if ($('#order-items-' + order_id).length > 0)
    {
        $('#order-items-' + order_id).hide();
    }
};
