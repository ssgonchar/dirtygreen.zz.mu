/**
 * Раскрывает подпродукты
 */
var expand_node = function(product_id)
{
    if ($('#children-' + product_id).is(':hidden'))
    {
        $('#children-' + product_id).show();
        $('#img-' + product_id).attr('src', '/img/icons/minus-white.png');
    }
    else
    {
        $('#children-' + product_id).hide();
        $('#img-' + product_id).attr('src', '/img/icons/plus-white.png');
        
    }
}