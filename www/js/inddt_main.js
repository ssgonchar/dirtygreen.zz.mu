/**
 * Удаляет айтем из In DDT
 * 
 * @version 20121218, zharkov
 */
var inddt_item_remove = function(inddt_item_id)
{
    if (!confirm('Remove item from In DDT ?')) return false;

    $.ajax({
        url: '/inddt/removeitem',
        data : {
            inddt_item_id : inddt_item_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#inddt-item-' + inddt_item_id).remove();
            }
            else
            {
                alert('In DDT is closed, items can not be removed !');
            }                
        }
    });
};

$(function(){
    if ($("#inddt_company").attr('id') != undefined)
    {
        $( "#inddt_company" ).autocomplete({
            source: function(request, response)
            {
                $('#inddt_company_id').val(0);

                $.ajax({
                    url     : "/company/getlistbytitle",
                    data    : {
                        maxrows : 25,
                        title   : request.term
                    },
                    success : function(data) {
                        response($.map(data.list, function(item)
                        {
                            return {
                                label: item.company.title,
                                value: item.company.id
                            }
                        }));
                    }
                });

            },
            minLength: 3,
            select: function(event, ui)
            {
                if (ui.item)
                {
                    $('#inddt_company').val(ui.item.label);
                    $('#inddt_company_id').val(ui.item.value);
                }
                else
                {
                    $('#inddt_company_id').val(0);
                }

                return false;

            },
            open: function() { },
            close: function() { },
            focus: function(event, ui) { return false; }
        });
    }
    
    $("#inddt_company").keypress(function(event){
        if(event.keyCode == 13)  return false;
    });
    
    $('.common-stockholder').on('change', function(){
        var common_stockholder = $(this).val();
        
        $('.item-stockholder').each(function(){
           $(this).val(common_stockholder);
        });
    });

    $('#inddt-status').on('change', function(){
        var status_id = $(this).val();
        
        $('.inddt-item-status').each(function(){
           $(this).val(status_id);
        });
    });
    
    $('.item-stockholder').on('change', function(){
        var item_stockholder = $(this),
            common_stockholder = $('.common-stockholder'),
            similar_stockholders = true;
            
        if (item_stockholder.val() != common_stockholder.val())
        {
            common_stockholder.val(0);
        }
        $('.item-stockholder').each(function(){
            if (item_stockholder.val() != $(this).val())
            {
                similar_stockholders = false;
                return;
            }
        });
    
        if (similar_stockholders)
        {
            common_stockholder.val(item_stockholder.val());
        }
    });
});