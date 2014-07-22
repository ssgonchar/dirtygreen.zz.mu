/**
 * Обновляет подпись с количеством регионов
 */
var update_qtty_text = function(value)
{
    qtty = parseInt($('#hid-countries-count').val()) + parseInt(value);
    
    if (qtty <= 0)
    {
        $('#lbl-countries-count').text('no countries');
        $('#hid-countries-count').val(0);
    }
    else
    {
        $('#lbl-countries-count').text(qtty == 1 ? '1 country' : qtty + ' countries');
        $('#hid-countries-count').val(qtty);
    }    
};

/**
 * Удаляет страну
 */
var country_remove = function(country_id)
{
    if (!confirm('Remove country ?')) return;
    if (country_id == 0) return;
    
    $.ajax({
        url: '/country/remove',
        data : { 
            country_id  : country_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + country_id).remove();
                update_qtty_text(-1);
                
                Message('Country was removed successfully', 'okay');
            }
            else
            {
                Message('Error deleting country', 'warning');
            }                
        }
    });    
};

/**
 * Переводит строку таблицы стран в режим просмотра или редактирования
 */
var country_action = function(country_id, mode)
{
    if (country_id == 0) return;
    
    $.ajax({
        url: '/country/action',
        data : { 
            country_id  : country_id,
            mode        : mode
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + country_id).html(json.content);
            }
            else
            {
                Message('Error editing country', 'warning');
            }                
        }
    });
};

/**
 * Сохраняет страну
 */
var country_save = function(country_id)
{
    var title       = $('#title-' + country_id).val().trim();
    var title1      = $('#title1-' + country_id).val();
    var title2      = $('#title2-' + country_id).val();
    var alpha2      = $('#alpha2-' + country_id).val();
    var alpha3      = $('#alpha3-' + country_id).val();
    var code        = $('#code-' + country_id).val();
    var dialcode    = $('#dialcode-' + country_id).val();
    var is_primary  = $('#is_primary-' + country_id).is(':checked') ? 1 : 0;

    if (title == '') 
    {
        alert('Please specify title ');
        return;
    }
    
    $.ajax({
        url: '/country/save',
        data : { 
            country_id : country_id,
            title       : title,
            title1      : title1,
            title2      : title2,
            alpha2      : alpha2,
            alpha3      : alpha3,
            code        : code,
            dialcode    : dialcode,
            is_primary  : is_primary            
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (country_id == 0)
                {
                    $('#country-list > tbody tr#tr-0').after("<tr id=\"tr-" + json.country_id + "\">" + json.content + "</tr>");
                    
                    $('#title-' + country_id).val('');
                    $('#title1-' + country_id).val('');
                    $('#title2-' + country_id).val('');
                    $('#alpha2-' + country_id).val('');
                    $('#alpha3-' + country_id).val('');
                    $('#code-' + country_id).val('');
                    $('#dialcode-' + country_id).val('');
                    $('#is_primary-' + country_id).attr('checked', '');
                    
                    update_qtty_text(1);
                    Message('Country successfully added', 'okay');
                }
                else
                {
                    $('#tr-' + country_id).html(json.content);
                    Message('Country successfully upadated', 'okay');                    
                }                                
            }
            else
            {
                Message(json.message, 'warning');
            }                
        }
    });    
};
