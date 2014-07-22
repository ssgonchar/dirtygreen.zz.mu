
/**
 * OnLoad
 * 
 */
$(function()
{
    if ($('.cb-row-item').length == 0) $('.td-item-checkbox').hide();
    
    $('.selected-control .btn-default').tooltip();
    
    //прячет Toolbox при открытии страницы
    $('.column-side').hide();  
    $('.icon-hide').removeClass('btn-primary').addClass('btn-default');
    $('.column-main').removeClass('col-md-9');
    $('.column-main').addClass('col-md-12');
    $('.column-side').addClass('column-side-hidden');
    //меняем текст Toolbox на Search Tools
    $('button.icon-hide').text("");
    $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Show Search Tools");
    //Меняем текст кнопки Search Tools
    $("button.icon-hide").live("click", function()
    {
        if($('.column-side-hidden').length>0){
            $('button.icon-hide').text("");
            $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Show Search Tools");
        }else{
            $('button.icon-hide').text("");
            $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Hide Search Tools");
        }
         $(".find-parametr").val("");
    });
   
    /**
     * запрещает ввод веса итема (заданного в позиции) пользователем 
     * стирает значения веса weight если хоть один параметр не введен
     * @version 20140619, Uskov
     */
    //селекторы
    var i_thickness = $(".i-thickness-input");
    var i_width = $(".i-width-input");
    var i_length = $(".i-length-input");
    var i_unitweight = $(".i-unitweight-input");
    //делаем неактивным поле weight
    i_unitweight.attr("readonly", true);
    //стираем значения веса weight если хоть один параметр не введен
    //толщина
    i_thickness.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#i-unitweight-" + this_id).val(""); }
    });
    //ширина
    i_width.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#i-unitweight-" + this_id).val(""); }
    });
    //длина
    i_length.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#i-unitweight-" + this_id).val(""); }
    });
    
    /**
     * запрещает ввод веса итема (measured params) пользователем 
     * стирает значения веса weight если хоть один параметр не введен
     * @version 20140619, Uskov
     */
    //селекторы
    var measured_thickness = $(".measured-thickness-input");
    var measured_width = $(".measured-width-input");
    var measured_length = $(".measured-length-input");
    var measured_unitweight = $(".measured-unitweight-input");
    //делаем неактивным поле weight
    measured_unitweight.attr("readonly", true);
    //стираем значения веса weight если хоть один параметр не введен
    //толщина
    measured_thickness.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#measured-unitweight-" + this_id).val(""); }
    });
    //ширина
    measured_width.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#measured-unitweight-" + this_id).val(""); }
    });
    //длина
    measured_length.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#measured-unitweight-" + this_id).val(""); }
    });
});
/**
 * Открывает блок выбора заказа
 * повторяется в positions_index.js
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
 * повторяется в positions_index.js
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
 * Добавляет выбранные айтемы в документ
 * 
 * @version: 20120812, zharkov
 * 
 * @version: 20140515, uskov
 *
 * Работает только если выбран итем (button class='btn-primary')
 */
var add_selected_to_document = function(doc_alias, doc_id, do_redirect)
{
    if ($('.selected-control').hasClass("btn-primary"))
    {
        var do_redirect	= do_redirect || 'no';
         var stock_id    = $('#stock').val();
         var item_ids    = '';
     
         // selected items
         $('.cb-row-item:checked').each(function(){
             item_ids += '' + (item_ids != '' ? ',' : '') + $(this).val();
         });
     
         if (doc_alias == 'createalias')
         {
             location.href = '/item/createalias/' + item_ids;
             return;
         }
         
         $.ajax({
             url: '/document/additems',
             data : { 
                 doc_alias   : doc_alias, 
                 doc_id      : doc_id,
                 stock_id    : stock_id,
                 item_ids    : item_ids
             },
             success: function(json){
                 if (json.result == 'okay') 
                 {                
                     if (do_redirect == 'yes') 
                     {
                         location.href = json.href;
                     }
                     else
                     {
                         Message('Selected items were sucessfully added !', 'okay');
                     }
                 }
                 else if (json.message)
                 {
                     Message(json.message, 'error');
                 }
             }
         });
    }
    else{
        alert('Please, select an item');
    }
};


/**
 * Подсчитывает тотал выбранных айтемов
 * @version: 20120812, zharkov
 */
var calc_selected = function()
{
    var qtty    = 0;
    var weight  = 0;
    var value   = 0;
    var pvalue  = 0;
    var inorder = 0;

    $('.cb-row-item:checked').each(function(){
        item_id = $(this).val();

        qtty    += 1;
        weight  += parseNumber(getVal($('#item-weight-' + item_id))); 
        value   += parseNumber(getVal($('#item-value-' + item_id)));
        pvalue  += parseNumber(getVal($('#item-purchasevalue-' + item_id)));
        inorder += parseNumber(getVal($('#item-orderid-' + item_id)));
    });    

    
    $('#lbl-selected-qtty').html(qtty);    
    $('#lbl-selected-weight').html(numberRound(weight, 2));    
    $('#lbl-selected-value').html(numberRound(value, 2));    
    $('#lbl-selected-purchasevalue').html(numberRound(pvalue, 2));    
    
    if (qtty == 0) 
    {
        $('.selected-control').hide();
    }
    else
    {
        $('.selected-control').show();
        if (inorder > 0) $('#btn_to_order').hide();        
    }
};

/**
 * Активирует/дезактивирует блок с групповыми действиями над выбранными итемами
 * 
 * @version: 20140514, uskov
 * 
*/
var show_group_actions = function()
{
    var items_length = $('.cb-row-item:checked').length;

    if (items_length > 0)
    {
        $('.selected-control.btn-default').removeAttr('title');
        $('.selected-control.btn-default').tooltip('destroy');
        $('.selected-control.btn-default').removeClass('btn-default');
        $('.selected-control').addClass('btn-primary');
    }
    else
    {
        $('.selected-control.btn-primary').attr('title', 'Please, select item');
        $('.selected-control.btn-primary').removeClass('btn-primary');
        $('.selected-control').addClass('btn-default');
        $('.selected-control.btn-default').tooltip();
    }
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
        // removing items
        $.each(json.items, function(item_id, position_id){
            $('#item-' + item_id).remove();
        });
    }
    else
    {
        alert('Error receiving data !');
    }    
};

/**
 * Показывает блок с действиями над айтемом
 * 
 * @version 20120429, zharkov: добавлена проверка доступности айтема
 * @version 20121219, zharkov: перешли на новый контекст
 */
var deprecated_show_item_block = function(obj, item_id, is_available, is_revision, use_parent)
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
                + '<li style="padding-top: 7px;"><a class="edit" href="/item/edit/' + item_id + '">edit item</a></li>'
                + (is_available > 0 ? '<li style="padding-top: 7px;"><a class="twin" href="/item/twin/' + item_id + '">twin item</a></li>' : '')
                + (is_available > 0 ? '<li style="padding-top: 7px;"><a class="cut" href="/item/cut/' + item_id + '">cut item</a></li>' : '')
                + (is_available > 0 ? '<li style="padding-top: 7px;"><a class="delete" href="javascript: void(0);" onclick="item_remove(' + item_id + ')">delete item</a></li>' : '')
                + '<li style="padding-top: 7px;"><a class="history" href="/item/history/' + item_id + '">view history</a></li>'
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

var remove_item = function(){
    var ids     = '';
    $('.cb-row-item:checked').each(function(){
        ids += (ids == '' ? '' : ',') + $(this).val();
    });    

    $.ajax({
        url: '/item/remove',
        data : { 
            item_ids   : ids
        },
        success: function(json){
            if (json.result == 'okay') {                
                //удаляем ряды в таблице
                $('.cb-row-item:checked').each(function(){
                    $(this).parent().parent().remove();
                });
                //деактивируем кнопки
                show_group_actions();
                console.log(json.items, "removed");
                console.log(json.positions);
            }
            if (json.result == 'error'){
                alert("You do not have enough rights to delete items !");
            }
        }
     });
};

$(".chosen-select").chosen({
    no_results_text: "Oops, nothing found!",    
    disable_search_threshold: 10
    
      
}); 


 
 