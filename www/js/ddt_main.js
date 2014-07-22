$(function(){

    $( "#ddt_transporter" ).autocomplete({
        source: function( request, response ) {
            
            $('#ddt_transporter_id').val(0);
            
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
                $('#ddt_transporter').val(ui.item.label);
                $('#ddt_transporter_id').val(ui.item.value);
            }
            else
            {
                $('#ddt_transporter_id').val(0);
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
    
    $("#ddt_transporter").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
    
});