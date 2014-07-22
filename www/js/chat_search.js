/**
 * OnLoad
 * version 20120711, zharkov
 */
$( window ).load(function(){
   

    $( ".chat-user-container > img" ).bind("click", function(e) {
      /*  
    var object_alias = 'chat';
    var object_id = '0';
    var user_id = this.id;
    
    show_chat_modal_for_user(object_alias, object_id, user_id);
        */
       var object_alias = 'chat';
    var object_id = '0';
    var user_id = this.id;
       show_chat_modal_for_user(object_alias, object_id, user_id);
        
       console.log(parseInt(e.id));
    });


  
  $("#chat_icon_park").bind('affixed.bs.affix', function(){
      $(this).css("right", "200px");
  })

   /* $( ".chat-user-container" ).on( "click", function() {
        console.log('this');*/

    $( "#sender-title" ).autocomplete({
        source: function( request, response ) {
            
            $('#sender-id').val(0);
            
            $.ajax({
                url     : "/user/getlistbytitle",
                data    : {
                    maxrows : 25,
                    login   : request.term
                },
                success : function( data ) {
                   // console.log(data.list);
                    var obj = jQuery.parseJSON(data);
                    console.log(data.list);
                    response( $.map(data.list, function( item ) {
                        //console.log(item);
                        
                        return {
                            label: item.user.full_login,
                            value: item.user.id
                        }
                        
                    }));
                }
            });
            
        },
        minLength: 2,
        select: function( event, ui ) {
            
            if (ui.item)
            {
                $('#sender-title').val(ui.item.label);
                $('#sender-id').val(ui.item.value);
            }
            else
            {
                $('#sender-id').val(0);
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
    
    $("#sender-title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
    

    $( "#recipient-title" ).autocomplete({
        source: function( request, response ) {
            
            $('#recipient-id').val(0);
            
            $.ajax({
                url     : "/user/getlistbytitle",
                data    : {
                    maxrows : 25,
                    login   : request.term
                },
                success : function( data ) {
                    response( $.map( data.list, function( item ) {
                        return {
                            label: item.user.full_login,
                            value: item.user.id
                        }
                    }));
                }
            });
            
        },
        minLength: 2,
        select: function( event, ui ) {
            
            if (ui.item)
            {
                $('#recipient-title').val(ui.item.label);
                $('#recipient-id').val(ui.item.value);
            }
            else
            {
                $('#recipient-id').val(0);
            }
            
            return false;
            
        },
        open: function() {
            
        },
        close: function() {

        }
    });
    
    $("#recipient-title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
    
});