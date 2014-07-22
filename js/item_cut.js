//document.ready function
$(function()
{
    /**
     * запрещает ввод веса куска итема пользователем 
     * стирает значения веса weight если хоть один параметр не введен
     * @version 20140619, Uskov
     */
    //селекторы
    var i_width = $(".width-input");
    var i_length = $(".length-input");
    var i_unitweight = $(".unitweight-input");
    //делаем неактивным поле weight
    i_unitweight.attr("readonly", true);
    //стираем значения веса weight если хоть один параметр не введен
    //ширина
    i_width.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#unitweight-" + this_id).val(""); }
    });
    //длина
    i_length.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#unitweight-" + this_id).val(""); }
    });
});

/**
 * Удаляет кусок из порезки
 * @version 20130112, zharkov
 */
var cut_remove_piece = function(index)
{
    if (confirm('Remove piece ?')) $('#piece-' + index).remove();
};

/**
 * Добавляет кусок при порезке
 * @version 20130112, zharkov
 */
var cut_add_piece = function(item_id)
{
    var index = parseInt($('#pieces_count').val()) + 1;
    
    $.ajax({
        url: '/item/cutaddpiece',
        data : {
            item_id : item_id,
            index   : index
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#cut-pieces > tbody tr:last').after(json.row);
                $('#pieces_count').val(index);
            }
            else
            {
                alert('Error generating piece for cutting !');
            }
        }
    });    
};

/**
 * Ищет позиции по location, steelgrade, thickness, width, length
 * @version: 20120922, zharkov
 */
var cut_get_positions = function(index, location_id, item_id)
{
    var steelgrade_id   = $('#steelgrade_id').val();
    var thickness       = $('#thickness').val();    
    var width           = $('#width-' + index).val();
    var length          = $('#length-' + index).val();

    $("#position-" + index).prepend($('<option selected="" value="0">Loading...</option>'));        
    
    $.ajax({
        url: '/position/find',
        data : {
            item_id         : item_id,
            location_id     : location_id,
            steelgrade_id   : steelgrade_id,
            thickness       : thickness,
            width           : width,
            length          : length
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                fill_select("#position-" + index, json.positions, {'value' : 0, 'name' : "--"});
            }
        }
    });
};