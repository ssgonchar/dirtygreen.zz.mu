$(function(){

    $( "#cmr_transporter" ).autocomplete({
        source: function( request, response ) {
            
            $('#cmr_transporter_id').val(0);
            
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
                $('#cmr_transporter').val(ui.item.label);
                $('#cmr_transporter_id').val(ui.item.value);
            }
            else
            {
                $('#cmr_transporter_id').val(0);
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
    
    $("#cmr_transporter").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
    
});