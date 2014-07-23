//document.ready function 
$(function()
{
    $("#unitweight-1").attr("readonly", true);
    $("#weight-1").attr("readonly", true);
});
/**
 * Select selected value
 */
var set_similarvalue = function(obj, selector)
{
    if (typeof $(obj) == 'undefined') return;
    
    if ($(selector).length > 0) $(selector).val($(obj).val());
};


/**
 * Fill stock positions list
 */
var bind_stockpositions = function(position_id, items_count)
{
    stock_id        = $('#stocks').val();
    steelgrade_id   = $('#steelgrades').val();
    
    items_thickness = $('#items_thickness').val();
    items_width     = $('#items_width').val();
    items_length    = $('#items_length').val();
    
    if (stock_id > 0)
    {
        $('#positions').html('<span class="spinner">Loading positions ...</span>');

        $.ajax({
            url: '/stock/getpositions',
            data : {
                stock_id        : stock_id,
                steelgrade_id   : steelgrade_id,
                items_count     : items_count,
                position_id     : position_id,
                items_thickness : items_thickness,
                items_width     : items_width,
                items_length    : items_length                
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('#positions').html(json.positions);
                }
            }
        });        
    }
};


/**
 * Clear positions list on stock change
 */
var clear_stockpositions = function()
{
    $('.tr-existing-position').remove();
    $('#new-position').attr('checked', 'checked');
};


/**
 * Get stock params & fill controls
 */
var bind_stockparams = function(stock_id, strict)
{
    strict = (typeof strict == 'undefined') ? true : strict;


    if (stock_id > 0)
    {
        $('#locations').prepend($('<option selected="" value="0">loading...</option>'));
        if ($('#steelgrades')) $('#steelgrades').prepend($('<option selected="" value="0">loading...</option>'));  
        
        error = false;
        
        $.ajax({
            url: '/stock/getstockparams',
            data : {
                stock_id    : stock_id,
                strict      : strict,
                locations   : true,
                steelgrades : true
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    $('#dimension_unit').val(json.stock.dimension_unit);
                    $('#weight_unit').val(json.stock.weight_unit);
                    $('.lbl-dim').html('<br>' + json.stock.dimension_unit);
                    $('.lbl-wgh').html('<br>' + (json.stock.weight_unit == 'mt' ? 'Ton' : json.stock.weight_unit));
                    $('.lbl-price').html('<br>' + json.stock.currency_sign + '/' + (json.stock.price_unit == 'mt' ? 'Ton' : json.stock.price_unit));
                    $('.lbl-value').html('<br>' + json.stock.currency_sign);
                    
                    fill_select("#locations", json.locations, {'value' : 0, 'name' : "--"});
                    if ($('#steelgrades')) fill_select("#steelgrades", json.steelgrades, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (stock_id == 0 || error)
    {
        $('#dimension_unit').val('');
        $('#weight_unit').val('');
        $('.lbl-dim').html('');
        $('.lbl-wgh').html('');
        $('.lbl-price').html('');
        $('.lbl-value').html('');
        
        if ($('#locations'))
        {
            $('#locations').empty();
            $('#locations').prepend($('<option value="0">--</option>'));            
        }
        
        if ($('#steelgrades'))
        {
            $('#steelgrades').empty();
            $('#steelgrades').prepend($('<option value="0">--</option>'));
        }        
    }
};
