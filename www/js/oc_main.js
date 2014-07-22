
/**
 * onLoad
 * @version 20130215, d10n
 */
 //alert();
$(function(){
    if ($('.company-text').hasClass('autocomplete'))
    {
        var autocompleteElement     = $('.company-text.autocomplete');
        var autocompleteIdElement   = $('.company-id.autocomplete');
        autocompleteElement.autocomplete({
            source: function( request, response )
            {
                autocompleteIdElement.val(0);
                $.ajax({
                    url     : "/company/getlistbytitle",
                    data    : {
                        title       : request.term,
                        list_type   : 'oc'
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
            select: function( event, ui )
            {
                if (ui.item)
                {
                    autocompleteElement.val(ui.item.label);
                    autocompleteIdElement.val(ui.item.value);
                }
                else
                {
                    autocompleteIdElement.val(0);
                }
                return false;
            },
            open: function() { },
            close: function() { },
            focus: function(event, ui) { return false; },
            minLength: 3
        });
        autocompleteElement.keypress(function(event){
            if(event.keyCode == 13) return false;
        });
    }
    
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
        if (!confirm('Remove item from Original Certificate ?')) return false;
        var item = $(this).parents('.item:first');
        
        $.ajax({
            url: '/document/removeitem',
            data : {
                doc_alias   : 'oc',
                doc_id      : item.data('oc_id'),
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
                    alert('Error ocured when removing item from Original Certificate !');
                }
            }
        });
    });
    
    // Quality Certificate
    $('#standard_id').change(function(){
        $('#standard_new').val(''); 
    });
    
    $('#standard_new').keyup(function(){
        $("#standard_id [value='0']").attr("selected", "selected");
    });

	//обработчик событий для кнопки поиска
	//oc
    $('#search').click(function(){
		var url="/oc/filter/plate_id:" + $('#search_string').val() + ";";
		$(location).attr('pathname',url);
    });	
	
	$("#search_string").keypress(function(e){
		if(e.keyCode==13){
			//нажата клавиша enter
			var url="/oc/filter/plate_id:" + $('#search_string').val() + ";";
			$(location).attr('pathname',url);
			console.log('keypress');
		}
	});
});