/**
 * ѕоказывает контекстноем меню айтема
 */
var show_item_context = function(obj, item_id, item_status_id)
{
    var ITEM_STATUS_RELEASED = 4;
    if (item_status_id > ITEM_STATUS_RELEASED)
    {
        location.href = '/item/' + item_id + '/history';
        return;
    }

    var position = $(obj).position();
    $('.js-item-context').remove();

    jQuery('<div/>', {
        id: 'js-item-' + item_id + '-context',
        html: 
            '<ul>'
            + '<li style="padding-top: 7px;"><a class="edit" href="/item/' + item_id + '/edit">edit item</a></li>'
            + '<li style="padding-top: 7px;"><a class="history" href="/item/' + item_id + '/history">view history</a></li>'
            + '<li style="padding-top: 7px;"><a class="pictures" href="/steelitem/' + item_id + '/dropbox">pictures</a></li>'
            + '</ul>',
        'class' : 'js-obj-context',
        mouseleave : function(){
            $(this).remove();
        },
        mouseenter : function(){
            $(this).clearQueue();
        },
        'style': 'top: ' + (position.top - 1) + 'px; left: ' + (position.left - 1) + 'px;'
    }).appendTo('#container');
};

/**
 * ќтображает селект статуса айтема заказа
 */
var order_select_item = function(object, item_id)
{
    if ($(object).is(':checked'))
    {
        $('#status-' + item_id).show();
        $('#status-' + item_id + '-name').hide();
    }
    else
    {
        $('#status-' + item_id).hide();
        $('#status-' + item_id + '-name').show();        
    }
};

