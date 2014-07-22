/**
 * Конвертирует данные в соответствием с параметрами склада
 * @version 20130215, zharkov
 */
var itemalias_resetparams = function(stock_id)
{
    d_stock_id  = $('#default_stock_id').val();
    dunit       = $('#dimension_unit').val();
    wunit       = $('#weight_unit').val();
    
    $('.item_id').each(function(){

        id              = $(this).val();
        d_thickness     = $('#default-thickness-' + id).val(); 
        d_width         = $('#default-width-' + id).val();
        d_length        = $('#default-length-' + id).val();
        d_unitweight    = $('#default-unitweight-' + id).val();
        d_price         = $('#default-price-' + id).val();
        d_delivery_time = $('#default-delivery_time-' + id).val();

        
        if (stock_id == d_stock_id)
        {
            thickness       = d_thickness;
            width           = d_width;
            length          = d_length;
            unitweight      = d_unitweight;
            price           = d_price;
            delivery_time   = d_delivery_time;
        }
        else
        {
            thickness       = dunit == 'mm' ? numberRound(d_thickness * 25.4, 0) : numberRound(d_thickness / 25.4, 1); 
            width           = dunit == 'mm' ? numberRound(d_width * 25.4, 0) : numberRound(d_width / 25.4, 1); 
            length          = dunit == 'mm' ? numberRound(d_length * 25.4, 0) : numberRound(d_length / 25.4, 1);             
            unitweight      = wunit == 'mt' ? numberRound(d_unitweight / 2204, 2) : numberRound(d_unitweight * 2204, 0);
            price           = '';
            delivery_time   = '';
        }
                
        $('#thickness-' + id).val(thickness);
        $('#width-' + id).val(width);
        $('#length-' + id).val(length);
        $('#unitweight-' + id).val(unitweight);
        $('#price-' + id).val(price);
        $('#delivery_time-' + id).val(delivery_time);
        
        itemalias_resetpositions(id);

    });
}

/**
 * Ререзапролняет подходящие позиции для сейтемов
 * @version 20130215, zharkov
 */
var itemalias_resetallpositions = function()
{
    
    $('.item_id').each(function(){

        id = $(this).val();
        itemalias_resetpositions(id);

    });
    
}

/**
 * Заменяет элемент выбора позиции на ссылку
 * @version 20130215, zharkov
 */
var itemalias_resetpositions = function(item_id)
{
    $('#position-' + item_id).hide();
    $('#span-position-' + item_id).hide();
    $("#position-" + item_id).prepend($('<option selected="" value="0">Loading...</option>'));
    
    $('#a-position-' + item_id).show();
}

/**
 * Заполняет вып. список позиций для айтема
 * @version 20130215, zharkov
 */
var itemalias_fillpositions = function(item_id)
{
    var stock_id        = $('#stocks').val();
    var stockholder_id  = $('#locations').val();
    var steelgrade_id   = $('#steelgrade-' + item_id).val();
    var thickness       = $('#thickness-' + item_id).val();
    var width           = $('#width-' + item_id).val();
    var length          = $('#length-' + item_id).val();
    var deliverytime    = $('#delivery_time-' + item_id).val();
    
    //if (stockholder_id == 0) return;
    
    $("#position-" + item_id).prepend($('<option selected="" value="0">Loading...</option>'));
    $('#span-position-' + item_id).show();
    $('#a-position-' + item_id).hide();
    
    $.ajax({
        url: '/position/getsuitable',
        data : { 
            stock_id        : stock_id,
            stockholder_id  : stockholder_id,
            steelgrade_id   : steelgrade_id,
            thickness       : thickness,
            width           : width,
            length          : length,
            deliverytime    : deliverytime
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                fill_select("#position-" + item_id, json.positions, {'value' : 0, 'name' : "Auto Assign"});
                
                $('#a-position-' + item_id).hide();
                $('#span-position-' + item_id).hide();
                $('#position-' + item_id).show();
            }
            else
            {
                error = true;
            }                
        }
    });    
}