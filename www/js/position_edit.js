/**
 * onLoad
 */
$(function(){
    bind_biz_autocomplete('.biz-autocomplete-alt', fill_biz_data);
});

/**
 * Заполняет производителей из бизнеса
 * @version 20120722, zharkov
 */
var fill_biz_data = function(biz_id)
{
    bind_biz_companies(biz_id, 'producer');
}

/**
 * Добавляет айтем в позицию
 * перенес из app.js
 * 
 * @version 20130302, zharkov
 */
var position_item_add = function(position_id)
{
    // следующий индекс строки в таблице айтемов
    next_row_index  = 1 + parseInt($("#items_count").val());
        
    // показывает модальное окно loading...
    show_idle();

    // если chrome или safari отправляем с задержкой, чтобы показалось модальное окно
    if ($.browser.webkit) 
    {
        setTimeout("send_position_item_add(" + position_id + "," + next_row_index + ");", webkit_timeout);
    }
    else
    {
        send_position_item_add(position_id, next_row_index);
    }    
};

/**
 * Отправляет запрос на добавление айтема в поззицию
 * перенес из app.js
 * 
 * @version 20130302, zharkov
 */
var send_position_item_add = function(position_id, next_row_index)
{
    $.ajax({
        url: '/position/additem',
        data : {
            position_id : position_id,
            next_row_index : next_row_index
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#t-i > tbody tr:last').after(json.main);
                $('#t-il > tbody tr:last').after(json.location);
                $('#t-is > tbody tr:last').after(json.status);
                $('#t-ic > tbody tr:last').after(json.chemical);
                $('#t-im > tbody tr:last').after(json.mechanical);
                
                // обновляет значение количества в позиции
                qtty = parseInt($('#qtty-1').val()) + 1;    
                $('#qtty-1').val(qtty);
                $('#lbl-qtty-1').html(qtty);
                $("#items_count").val(next_row_index);
                $(".datepicker").datepicker();
                
                // пересчитывает вес и сумму позиции
                calc_weight(1);
                calc_value(1);
                
                // тотал                
                calc_total('position');
                
                // автокомплит
                bind_company_autocomplete();
                
                // показывает колонку для удаления айтема
                toggle_remove_controls();
            }
            else
            {
                alert('Error receiving data !');
            }
            
            // прячет модальное окно loading...
            hide_idle();
        }
    });    
};

/**
 * Удаляет айтем из позиции на странице редактирования
 * перенес из app.js
 * 
 * @version 20130302, zharkov
 */
var position_item_remove = function(index)
{
    if ($('#t-i-id-' + index))
    {        
        item_id = parseInt($('#t-i-id-' + index).val());
        
        if (item_id > 0)
        {
            is_deleted = $('#t-i-deleted-' + index).val();
            if (is_deleted > 0)
            {
                $('#pic-delete-' + index).attr('src', '/img/icons/cross.png');
                $('#t-i-deleted-' + index).val(0);
                
                qtty = parseInt($('#qtty-1').val()) + 1;
            }
            else
            {
                //if (!confirm('Am I sure ?')) return;

                $('#pic-delete-' + index).attr('src', '/img/icons/reload.png');
                $('#t-i-deleted-' + index).val(1);
                
                qtty = parseInt($('#qtty-1').val()) - 1;
            }
            
            $('#t-i-' + index).toggleClass('deleted');            
        }
        else
        {
            if (!confirm('Am I sure ?')) return;
            
            $('#t-i-' + index).remove();
            $('#t-il-' + index).remove();
            $('#t-is-' + index).remove();
            $('#t-ic-' + index).remove();
            $('#t-im-' + index).remove();            
            
            qtty = parseInt($('#qtty-1').val()) - 1;
        }
        

        $('#qtty-1').val(qtty);
        $('#lbl-qtty-1').html(qtty);

        calc_weight(1);
        calc_value(1);        
    }
    
    toggle_remove_controls();
}

/**
 * Показывает или прячет колонку с сылкой на удаление айтема
 * @version 20130302, zharkov
 */
var toggle_remove_controls = function()
{
    if ($('.steelitems').length > $('.stelitems-eternal').length)
    {
        $('.items-action-delete').show();
    }
    else
    {
        $('.items-action-delete').hide();
    }
}