var inout_select_items = function(obj, alias)
{
    if ($(obj).hasClass('inout-selected'))
    {
        $('.' + alias + '-items').removeClass('inout-selected');
    }
    else
    {
        $('.' + alias + '-items').addClass('inout-selected');
    }
};

var toggle_sold_controls = function(value, type_sold)
{
    if (value == type_sold)
    {
        $('.sold-controls').show();
        $('#location-title').removeClass('form-td-title-b');
        $('#location-title').addClass('form-td-title');
    }
    else
    {
        $('.sold-controls').hide();
        $('#country option:first').attr('selected', 'selected');
        $('#buyer').val('');
        $('#buyer_id').val(0);
        
        $('#location-title').removeClass('form-td-title');
        $('#location-title').addClass('form-td-title-b');        
    }    
};

var clear_fields = function()
{
    $('#owner option:first').attr('selected', 'selected');
    $('#type option:first').attr('selected', 'selected');
    $('#dimensions option:first').attr('selected', 'selected');
    
    $('#stockholder').prepend($('<option selected="" value="0">--</option>'));
    $('#steelgrade').prepend($('<option selected="" value="0">--</option>'));
    $('#country').prepend($('<option selected="" value="0">--</option>'));
    $('#supplier').prepend($('<option selected="" value="0">--</option>'));    
            
    $('#datefrom').val('');
    $('#dateto').val('');
    $('#thickness').val('');
    $('#width').val('');
    $('#buyer').val('');
    $('#buyer_id').val(0);
    
    $('.sold-controls').hide();    
};

/**
 * get owner data for controls
 */
var get_inout_data = function(obj)
{
    var owner               = $('#owner').val();
    var stockholder_id      = $('#stockholder').val();
    var stockholder_change  = ($(obj).attr('id') == 'stockholder');
    
    if (!stockholder_change) $('#stockholder').prepend($('<option selected="" value="0">loading...</option>'));
    $('#country').prepend($('<option selected="" value="0">loading...</option>'));
    $('#steelgrade').prepend($('<option selected="" value="0">loading...</option>'));
    $('#supplier').prepend($('<option selected="" value="0">loading...</option>'));
    
    $.ajax({
        url     : '/report/getinoutdata',
        data    : { 
            owner           : owner,
            stockholder_id  : stockholder_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (!stockholder_change) fill_select("#stockholder", json.stockholders, {'value' : 0, 'name' : "--"});
                fill_select("#steelgrade", json.steelgrades, {'value' : 0, 'name' : "--"});
                fill_select("#country", json.countries, {'value' : 0, 'name' : "--"});
                fill_select("#supplier", json.suppliers, {'value' : 0, 'name' : "--"});
                
                $('#dimensions option[value="' + json.dimension_unit + '"]').attr("selected", "selected");
            }
            else if (json.message) 
            {
                Message(json.message, 'error');
            }
        }
    });    
}
