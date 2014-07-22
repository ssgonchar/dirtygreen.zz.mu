/**
 * ������������ ����� ���������� ���������
 * @version 20120818, zharkov
 */
var calc_selected = function()
{
    var qtty        = 0;
    var weight      = 0;
//    var weight_ton  = 0;


    $('.cb-row-item:checked').each(function(){
        item_id = $(this).val();

        qtty        += 1;
        weight      += parseNumber(getVal($('#item-weight-' + item_id))); 
//        weight_ton  += parseNumber(getVal($('#item-weight-ton-' + item_id))); 
    });    

    
    $('#lbl-selected-qtty').html(qtty);
    $('#lbl-selected-weight').html(numberRound(weight, 2));
//    $('#lbl-selected-weight-ton').html(numberRound(weight_ton, 2));
    
    if (qtty == 0) 
    {
        $('.selected-control').hide();
    }
    else
    {
        $('.selected-control').show();
    }
};

$(function(){
   
    $( "#biz_title" ).autocomplete({
        source: function( request, response ) {
            
            $('#biz_id').val(0);
            
            $.ajax({
                url     : "/biz/getlistbytitle",
                data    : {
                    maxrows : 25,
                    title   : request.term
                },
                success : function( data ) {
                    response( $.map( data.list, function( item ) {
                        return {
                            label: item.biz.doc_no_full,
                            value: item.biz.id
                        }
                    }));
                }
            });
            
        },
        minLength: 3,
        select: function( event, ui ) {
            
            if (ui.item)
            {
                $('#biz_title').val(ui.item.label);
                $('#biz_id').val(ui.item.value);
                
                get_companies_by_biz(ui.item.value);
            }
            else
            {
                $('#biz_id').val(0);
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
    
    // delivery date
    $('#date, .date').datepicker({
        beforeShow: function(input, inst) 
        { 

        },
        showWeek: true
    });

    $("#biz_title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });

});


var get_companies_by_biz = function(biz_id)
{

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

$(function(){
    $('.inv-status').on('change', function(){
        $('.inv-amount-received').show();
        if ($(this).val() != 2)
        {
            $('.inv-amount-received').hide();
            $('.inv-amount-received input').val('');
        }
    });
});

/**
 * Удаляет айтем из In DDT
 * 
 * @version 20121218, zharkov
 */
var item_remove = function(invoice_item_id)
{
    if (!confirm('Remove item from Invoice ?')) return false;

    $.ajax({
        url: '/invoice/removeitem',
        data : {
            invoice_item_id : invoice_item_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                var item = $('#invoice_item_' + invoice_item_id);
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
};