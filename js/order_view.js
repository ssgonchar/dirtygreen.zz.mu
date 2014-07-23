/**
 * Показывает блок с действиями для позиции
 */
var show_object_block = function(obj, obj_id, order_id)
{
    var parent      = $(obj).parent();
    var position    = $(parent).position();

    $('.js-obj-actions').remove();

    jQuery('<div/>', {
        id: 'js-obj-' + obj_id + '-actions',
        html: 
            '<ul>'
            + '<li><a class="check" href="/order/' + order_id + '/positionitems/' + obj_id + '">select items</a></li>'
            + '<li><a class="history" href="/position/history/' + obj_id + '">view history</a></li>'
            + '</ul>',
        'class' : 'js-obj-actions',
        mouseleave : function(){
            $(this).remove();
        },
        mouseenter : function(){
            $(this).clearQueue();
        },
        'style': 'top: ' + position.top + 'px; left: ' + position.left + 'px;'
    }).appendTo('#container');

    $(obj).mouseleave(function(){
        $('#js-obj-' + obj_id + '-actions').delay(300).fadeOut(50);
    });    
};


