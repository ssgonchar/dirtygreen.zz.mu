$(function(){
    
    if ($("#ra_company").attr('id') != undefined)
    {
        $( "#ra_company" ).autocomplete({
            source: function( request, response ) {

                $('#ra_company_id').val(0);

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
                    $('#ra_company').val(ui.item.label);
                    $('#ra_company_id').val(ui.item.value);
                }
                else
                {
                    $('#ra_company_id').val(0);
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

        $("#ra_company").keypress(function(event){
            if(event.keyCode == 13) 
            {
                return false;
            }
        });
    }
    
});

/**
 * Удаляет item/variant RA
 *
 * @var int ra_id
 * @var int item_id ra_item_id
 * @var int parent_id ra_item_parent_id
 * 
 * @version 20121021, d10n: теперь удаляет не только вариант, но и айтем
 * @version 20121018, zharkov
 **/
var remove_item = function(ra_id, item_id, parent_id)
{
    if (!confirm('Remove item' + (parent_id > 0 ? ' variant' : '') + ' ?')) return false;
    
    $.ajax({
        url: '/ra/removeitem',
        data : {
            ra_id   : ra_id, 
            item_id : item_id,
            parent_id : parent_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (parent_id == 0)
                {
                    if ($('.ra-item-primary').length == 1)
                    {
                        $('.ra-item-primary[iid=' + item_id + ']').parents('.list:first').hide();
                        $('.ra-pl-is-empty').show();
                    }
                    
                    $('.ra-item-primary[iid=' + item_id + ']').remove();
                    $('.ra-item-variant[pid=' + item_id + ']').remove();
                }
                else
                {
                    $('.ra-item-variant[iid=' + item_id + ']').remove();
                    if ($('.ra-item-variant').length === 0)
                    {
                        $('.ra-item-manipulation').removeClass('variants-are-exist');
                    }
                }
            }
            else
            {
                alert('Error was occured while deleting item' + (parent_id > 0 ? ' variant' : '') + ' !');
            }                
        }
    });
};

$(function(){
    $('.dest-sholder-select').on('change', function(){
        var dest_stockholder_id = $(this).val();
        
        if (dest_stockholder_id <= 0) return false;
        
        $('.dest-sholder-input').val('');
    });
    $('.dest-sholder-input').on('keyup', function(){
        var dest_stockholder_name = $(this).val();
        
        if (dest_stockholder_name == '') return false;
        
        $('.dest-sholder-select').children('option').removeAttr('selected');
        $('.dest-sholder-select').children('option:first').attr('selected', 'selected');
    });
});