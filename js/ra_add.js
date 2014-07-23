/**
 * OnLoad
 * @version 20130112, zharkov
 */
$(function(){

    $('.ra-check-all').each(function(){

        var rel     = $(this).attr('rel');
        var rel_obj = $('.ra-steelitem[rel=' + rel + ']');
        
        if (rel_obj.length == 0)
        {
            $(this).hide();
        }
    });
    
    if ($('.ra-steelitem').length == 0)
    {
        $('.ra-td-cb').hide();
        $('#btn_create').hide();        
    }

});

/**
 * Выбирает все чекбоксы
 * @version 20130112, zharkov
 */
var ra_check_all_items = function(stockholder_id)
{
    $('.ra-steelitem[rel="shid_' + stockholder_id + '"]').attr('checked', $('.ra-check-all[rel="shid_' + stockholder_id + '"]').is(':checked'));
};



/*
$(function(){
    function check_all_children(selector)
    {
        var parent = $(selector);
        var rel = parent.attr('rel');
        
        var related_HTML_element_set = $('.steelitem-id[rel=' + rel + ']');
        
        if (parent.attr('checked') === 'checked')
        {
            related_HTML_element_set.attr('checked', 'checked');
        }
        else
        {
            related_HTML_element_set.removeAttr('checked');
        }
    }
    
    $('.check-all-items').on('click', function(){ check_all_children(this); });
    
    function check_all_items()
    {
        $('.check-all-items').each(function(){
            if ($(this).attr('disabled') === 'disabled') return;
            
            $(this).attr('checked', 'checked');
            check_all_children(this);
        });
    }
    
    $('.steelitem-id').on('click', function(){
        var rel = $(this).attr('rel');
        var related_checkall_HTML_element = $('.check-all-items[rel=' + rel + ']');
        
        related_checkall_HTML_element.removeAttr('checked');
        
        if ($('.steelitem-id[rel=' + rel + ']:checked').length == $('.steelitem-id[rel=' + rel + ']').length)
        {
            related_checkall_HTML_element.attr('checked', 'checked');
        }
    });
    
    check_all_items();
});
*/