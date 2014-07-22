$(function(){
   
    $( "#company_title" ).autocomplete({
        source: function( request, response ) {
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
                $('#company_title').val(ui.item.label);
                $('#company_id').val(ui.item.value);                
            }
            else
            {
                $('#company_id').val(0);                
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
    
    $("#company_title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });    
    
});

/**
 * добавляет новый локэйшен на склад
 */
var stock_add_location = function()
{
    last_id             = 1 + parseInt($('#stock-location-last-id').val());
    company_id          = $('#company_id').val();
    company_title       = $('#company_title').val();
    location_title      = $('#location_title').val();
    int_location_title  = $('#int_location_title').val();
    
    if (company_id == 0 || company_title == '') return;
    
    new_tr = '<tr id="s-l-' + last_id + '">' 
                + '<td><input type="hidden" id="s-l-deleted-' + company_id + '" name="location[' + last_id + '][deleted]" value="0"><a href="/company/view/' + company_id + '">' 
                + company_title + '</a><input class="stock-company-id" type="hidden" name="location[' 
                + last_id + '][company_id]" value="' + company_id + '"><input type="hidden" id="s-l-id-' + last_id + '" value="0"></td><td><input type="text" name="location[' 
                + last_id + '][location]" clas="max" value="' + location_title + '"></td><td><input type="text" name="location[' 
                + last_id + '][int_location_title]" clas="max" value="' + int_location_title + '"></td><td><img id="s-l-pic-' 
                + last_id + '" src="/img/icons/cross.png" style="cursor: pointer" onclick="stock_remove_location(' 
                + last_id + ');"></td></tr>';
            
    $('#companies > tbody tr:last').before(new_tr);
    
    $('#company_id').val(0);
    $('#company_title').val('');
    $('#location_title').val('');    
    $('#int_location_title').val('');    
    $('#stock-location-last-id').val(last_id);
};

/**
 * Удаляет location
 */
var stock_remove_location = function(id)
{
    item_id = parseInt($('#s-l-id-' + id).val());
    
    if (item_id > 0)
    {
        is_deleted = $('#s-l-deleted-' + id).val();
        if (is_deleted > 0)
        {
            $('#s-l-pic-' + id).attr('src', '/img/icons/cross.png');
            $('#s-l-deleted-' + id).val(0);
        }
        else
        {
            $('#s-l-pic-' + id).attr('src', '/img/icons/reload.png');
            $('#s-l-deleted-' + id).val(1);            
        }
        
        $('#s-l-' + id).toggleClass('deleted');        
    }
    else
    {
        if (!confirm('Am I sure ?')) return;
        $('#s-l-' + id).remove();
    }    
}