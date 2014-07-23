/**
 * Изменяет значек валюты в таблице айтемов
 * @version 20130131, zharkov
 */
var supinv_change_currency = function(currency)
{
    $('#supinv-currency').html(get_curreny_sign(currency));
}


/**
 * Показывает / прячет поле для ввода суммы платежа
 * @version 20130130, zharkov
 */
var supinv_toggle_amount = function(status_id, PPAID)
{
    if (status_id == PPAID)
    {
        $('#supinv-amount').show();
    }
    else
    {
        $('#supinv-amount').hide();
    }
};

/**
 * onLoad
 * @version 20130130, zharkov
 */
$(function(){
   
    $( "#supinv_company" ).autocomplete({
        source: function( request, response ) {
            
            $('#supinv_company_id').val(0);
            
            $.ajax({
                url     : "/company/getlistbytitle",
                data    : {
                    maxrows : 25,
                    title   : request.term
                },
                success : function( data ) {
                    response( $.map( data.list, function( item ) {
                        return {
                            label: item.company.title,
                            value: item.company.id
                        }
                    }));
                }
            });
            
        },
        minLength: 3,
        select: function( event, ui ) {
            
            if (ui.item)
            {
                $('#supinv_company').val(ui.item.label);
                $('#supinv_company_id').val(ui.item.value);
            }
            else
            {
                $('#supinv_company_id').val(0);
            }
            
            return false;
            
        },
        open: function() { },
        close: function() { },
        focus: function(event, ui) 
        { 
            return false;
        }        
    });
    
    $("#supinv_company").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
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

    $('.item-delete').on('click', function(){
        if (!confirm('Remove item from Supplier Invoice ?')) return false;
        var target_element = $(this);
        var item = target_element.parents('.item:first');
        
        $.ajax({
            url: '/document/removeitem',
            data : {
                doc_alias   : 'supinvoice',
                doc_id      : item.data('supplier_invoice_id'),
                item_id     : item.data('steelitem_id')
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
                    alert('Error ocured when removing item from Supplier Invoice !');
                }
            }
        });
    });
    
    $(".chosen-select").chosen({
	width: "95%",
	disable_search_threshold: 10,
			       });    
    
});