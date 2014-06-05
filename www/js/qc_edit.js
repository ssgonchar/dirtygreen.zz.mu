/**
 * Подсчитывает тотал выделенных элементов
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

    $( "#customer" ).autocomplete({
        source: function( request, response ) {
            
            $('#customer_id').val(0);
            
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
                $('#customer').val(ui.item.label);
                $('#customer_id').val(ui.item.value);
            }
            else
            {
                $('#customer_id').val(0);
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
    
    $("#customer").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
/*
    $( "#biz" ).autocomplete({
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
                $('#biz').val(ui.item.label);
                $('#biz_id').val(ui.item.value);
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
    
    $("#biz").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
*/
});

/**
 * Remove item from QC
 */
var qc_remove_item = function(doc_id, item_id)
{
    if (doc_id <= 0)
    {
        $('.item-' + item_id).remove();
        return;
    }
    
    $.ajax({
        url: '/document/removeitem',
        data : {
            doc_alias   : 'qc',
            doc_id      : doc_id,
            item_id     : item_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('.item-' + item_id).remove();
                
                // recalculate total
                // ...
            }
            else
            {
                alert('Error ocured when removing item from QC !');
            }
        }
    });    
};