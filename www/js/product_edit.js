/**
 * Удаляет тарифный код
 */
var remove_tariff_code = function(index)
{
    if (!confirm('Remove tariff code ?')) return;    
    $('#tc-' + index).remove();    
}


/**
 * Добавляет тарифный код к списку
 */
var add_tariff_code = function()
{
    var tc_index        = $('#tc-index').val();
    var tc_code         = $('#tc-code').val();
    var tc_description  = $('#tc-description').val();
    
    var tc_code_cleared = tc_code.replace(/\s+/g, '');

    
    // пустой параметр не добавляем
    if (tc_code_cleared == '') return;
    
    // не добавляем параметр, который уже есть
    var code_exists = false;
    var tc_codes    = $('.tc-codes');
    if (tc_codes.length > 0) 
    {
        tc_codes.each(function(){ 
            if ($(this).val().replace(/\s+/g, '') == tc_code_cleared) 
            {
                code_exists = true;
                Message('Such tarff code already exists!', 'warning');
                
                return;
            }
        });
    }
    
    if (code_exists) return;
    
    tc_index    = 1 + parseInt(tc_index);
    new_row     = '<td><input type="hidden" name="tariff_code[' + tc_index + '][id]" value="0"><input type="text" class="tc-codes max" name="tariff_code[' + tc_index + '][code]" value="' + tc_code + '"></td><td><input type="text" class="max" name="tariff_code[' + tc_index + '][description]" value="' + tc_description + '"></td><td style="text-align:center;"><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_tariff_code(' + tc_index + ');"></td>';
    
    $('#tc-list > tbody tr:last').before("<tr id=\"tc-" + tc_index + "\">" + new_row + "</tr>");    
    
    $('#tc-description').val('');
    $('#tc-code').val('');
    $('#tc-index').val(tc_index);    
}

/**
 * Заполняет выпадающий список товаров
 */
var bind_products = function(product_id, team_id)
{
    if (team_id > 0)
    {
        $('#products').prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/team/getproducts',
            data : {
                product_id  : product_id,
                team_id     : team_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#products", json.products, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (team_id == 0 || error)
    {
        $('#products').empty();
        $('#products').prepend($('<option value="0">--</option>'));
    }    
}