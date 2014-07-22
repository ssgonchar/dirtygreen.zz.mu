$(function(){
       
    // delivery date
    $('.date').datepicker({
        beforeShow: function(input, inst) { },
        showWeek: true
    });
    
    $("#biz_title").keypress(function(event){
        if(event.keyCode == 13) return false;
    });

    $('.inv-status').on('change', function(){
        $('.inv-amount-received').show();
        if ($(this).val() != 2)
        {
            $('.inv-amount-received').hide();
            $('.inv-amount-received input').val('');
        }
    });

    $('.group-checkbox').on('click', function(){
        var rel = $(this).attr('rel');
        var related_HTML_element_set = $('.single-checkbox[rel=' + rel + ']');
        
        if ($(this).attr('checked') == 'checked')
        {
            related_HTML_element_set.attr('checked', 'checked');
        }
        else
        {
            related_HTML_element_set.removeAttr('checked');
        }
    });
    
    $('.single-checkbox').on('click', function(){
        var rel = $(this).attr('rel');
        var related_checkall_HTML_element = $('.group-checkbox[rel=' + rel + ']');
        
        related_checkall_HTML_element.removeAttr('checked');
        
        if ($('.single-checkbox[rel=' + rel + ']:checked').length == $('.single-checkbox[rel=' + rel + ']').length)
        {
            related_checkall_HTML_element.attr('checked', 'checked');
        }
    });
/*
    $('.invoice-type').on('change', function(){
        var type_id = $(this).val();
        
        $('.single-checkbox').attr('disabled', 'disabled').removeAttr('checked');
        $('.single-checkbox.type-id-' + type_id).removeAttr('disabled').attr('checked', 'checked');
    });
*/
    $('.item-delete').on('click', function(){
        if (!confirm('Remove item from Invoice ?')) return false;
        var target_element = $(this);
        var item = target_element.parents('.item:first');
        
        $.ajax({
            url: '/invoice/removeitem',
            data : {
                invoice_id : item.data('invoice_id'),
                steelitem_id : item.data('steelitem_id')
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    var table = item.parents('table:first');
                    
                    if (table.find('.item').length <= 1)
                    {
                        table.hide();
                        $('.packing-list-is-empty').show();
                    }
                
                    item.remove();
                }
                else
                {
                    alert('Error ocured when removing item from Invoice !');
                }
            }
        });
    });
    
    
    $('#invoice-number').on('keyup', function(){
        var invoice_number = $(this).val().trim();

        if (invoice_number == '')
        {
            $('#tr-invoice-status').hide();
            $("#invoice-is-closed [value='0']").attr("selected", "selected");
        }
        else
        {
            $('#tr-invoice-status').show();
        }
        
    });
    
    bind_biz_autocomplete('.biz-autocomplete', get_companies_by_biz)
    
});

var get_companies_by_biz = function(biz_id)
{
    console.log('get_companies_by_biz');
    if (biz_id > 0)
    {
        $('#companies').prepend($('<option selected="" value="0">Loading...</option>'));
        error = false;
        
        $.ajax({
            url: '/biz/getcompanies',
            data : {
                biz_id  : biz_id,
                role    : 'buyer'
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#companies", json.companies, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }
            }
        });
    }
    
    if (biz_id == 0 || error)
    {
        $('#companies').empty();
        $('#companies').prepend($('<option value="0">--</option>'));
    }
};