$(function(){
   
    $("#company_title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
    
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
                
                get_persons_list_by_company(ui.item.value);
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
    
});


/**
 * Получает список людей для компании
 */
var get_persons_list_by_company = function(company_id)
{
    if (company_id > 0)
    {
        $('#persons').prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/company/getpersons',
            data : {
                company_id : company_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#persons", json.persons, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (company_id == 0 || error)
    {
        $('#persons').empty();
        $('#persons').prepend($('<option value="0">--</option>'));
    }    
};