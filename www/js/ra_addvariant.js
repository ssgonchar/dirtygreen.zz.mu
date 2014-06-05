$(function(){
    $('.check-all-items').on('click', function(){
        var rel = $(this).attr('rel');
        var related_HTML_element_set = $('.steelitem-id[rel=' + rel + ']');
        
        if ($(this).attr('checked') == 'checked')
        {
            related_HTML_element_set.attr('checked', 'checked');
        }
        else
        {
            related_HTML_element_set.removeAttr('checked');
        }
    });
    
    $('.steelitem-id').on('click', function(){
        var rel = $(this).attr('rel');
        var related_checkall_HTML_element = $('.check-all-items[rel=' + rel + ']');
        
        related_checkall_HTML_element.removeAttr('checked');
        
        if ($('.steelitem-id[rel=' + rel + ']:checked').length == $('.steelitem-id[rel=' + rel + ']').length)
        {
            related_checkall_HTML_element.attr('checked', 'checked');
        }
    });
});