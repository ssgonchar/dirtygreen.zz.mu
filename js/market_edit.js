/**
 * Удаляет контактные данные
 */
var remove_country = function(index)
{
    if (!confirm('Remove country ?')) return;    
    $('#country-' + index).remove();    
}


/**
 * Добавляет тарифный код к списку
 */
var add_country = function()
{
    var country_index       = $('#country-index').val();
    var new_country         = $('#new-country').val();
    var new_country_text    = $('#new-country > option:selected').text();
    
    // пустой параметр не добавляем
    if (new_country == 0) return;
    
    // не добавляем параметр, который уже есть
    var country_exists  = false;
    var m_countries     = $('.m-countries');
    if (m_countries.length > 0) 
    {
        m_countries.each(function(){ 
            if ($(this).val() == new_country)
            {
                country_exists = true;
                Message('Such country already exists in market !', 'warning');
                
                return;
            }
        });
    }
    
    if (country_exists) return;
    
    country_index   = parseInt(country_index) + 1;
    new_row         = '<td><input type="hidden" class="m-countries" name="countries[' + country_index + '][country_id]" value="' + new_country + '">' + new_country_text + '</td><td><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_country(' + country_index + ');"></td>';

    $('#countries > tbody tr:last').before("<tr id=\"country-" + country_index + "\">" + new_row + "</tr>");        
    
    $('#country-index').val(country_index);
    $("#new-country [value='0']").attr("selected", "selected");
}