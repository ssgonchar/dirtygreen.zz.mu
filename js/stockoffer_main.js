/**
 * Убирает изображение
 * @version 20130306, zharkov
 */
var remove_picture = function(object_alias, object_id)
{
    $('.' + object_alias + '-' + object_id + '-preview').html('');
    $('.' + object_alias + '-' + object_id + '-id').val(0);
    $('.' + object_alias + '-' + object_id + '-remove').hide();
}

/**
 * Выполняет действия с выбранным изображением
 * @version 20130306, zharkov
 */
var select_picture = function(object_alias, object_id, id, secret_name, original_name)
{
    $('.' + object_alias + '-' + object_id + '-preview').html('<img src="/picture/album/' + secret_name + '/m/' + original_name + '" title="' + original_name + '" alt="' + original_name + '">');
    $('.' + object_alias + '-' + object_id + '-id').val(id);
    $('.' + object_alias + '-' + object_id + '-remove').show();
    
    remove_modal();
};

/**
 * Отображает модальное окно с набором картинок для альбома с алиасом alias
 * @version 20130305, zharkov
 */
var show_pictures = function(object_alias, object_id)
{
    show_idle();
    
    $.ajax({
        url     : '/attachment/getimages',
        data    : { 
            object_alias : object_alias, 
            object_id : object_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                show_modal(json.content);
                bind_uploader({
                    template    : 'picture',
                    title       : 'Add Pictures',
                    url         : '/attachment/upload/',
                    filetype    : 'pictures'
                });
            }
            else if (json.message) 
            {
                Message(json.message, 'error');
            }
            
            hide_idle();
        }
    });    
};


var position_remove = function(event)
{
    if (!confirm('Remove position from Stock Offer ?')) return false;
    var targetElement = event.target;
    var steelposition = $(targetElement).parents('.position-container:first');

    $.ajax({
        url: '/document/removeposition',
        data : {
            doc_alias   : 'stockoffer',
            doc_id      : steelposition.data('doc_id'),
            position_id     : steelposition.data('steelposition_id')
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                var table = steelposition.parents('table:first');
                
                if (table.find('.position-container').length <= 1)
                {
                    table.hide();
                    $('.packing-list-is-empty').show();
                }
                steelposition.remove();
            }
            else
            {
                alert('Error ocured when removing item from Stock Offer !');
            }
        }
    });
};

$(function(){
    $('.position-delete').on('click', position_remove);
});