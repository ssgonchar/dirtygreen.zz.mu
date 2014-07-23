/**
 * Обновляет подпись с количеством регионов
 */
var update_qtty_text = function(value)
{
    qtty = parseInt($('#hid-cities-count').val()) + parseInt(value);
    
    if (qtty <= 0)
    {
        $('#lbl-cities-count').text('no cities');
        $('#hid-cities-count').val(0);
    }
    else
    {
        $('#lbl-cities-count').text(qtty == 1 ? '1 city' : qtty + ' cities');
        $('#hid-cities-count').val(qtty);
    }    
};

/**
 * Загружает список городов
 */
var get_cities_list = function()
{
    var region_id = $('#sel-region').val();
    if (region_id == 0) return;
    
    location.href = '/directory/cities/' + region_id;
};

/**
 * Удаляет город
 */
var city_remove = function(city_id)
{
    if (!confirm('Remove city ?')) return;
    if (city_id == 0) return;
    
    $.ajax({
        url: '/city/remove',
        data : { 
            city_id : city_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + city_id).remove();
                update_qtty_text(-1);
                
                Message('City was removed successfully', 'okay');
            }
            else
            {
                Message('Error deleting city', 'warning');
            }                
        }
    });    
};

/**
 * Переводит строку таблицы городов в режим просмотра или редактирования
 */
var city_action = function(city_id, mode)
{
    if (city_id == 0) return;
    
    $.ajax({
        url: '/city/action',
        data : { 
            city_id : city_id,
            mode    : mode
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + city_id).html(json.content);
            }
            else
            {
                Message('Error loading template', 'warning');
            }                
        }
    });
};

/**
 * Сохраняет город
 */
var city_save = function(city_id)
{
    var country_id  = $('#hid-country-id').val();
    var region_id   = $('#hid-region-id').val();
    
    var title       = $('#title-' + city_id).val().trim();
    var title1      = $('#title1-' + city_id).val();
    var title2      = $('#title2-' + city_id).val();
    var dialcode    = $('#dialcode-' + city_id).val();

    if (country_id == 0 || region_id == 0) return;
    
    if (title == '') 
    {
        alert('Please specify title ');
        return;
    }
    
    $.ajax({
        url: '/city/save',
        data : { 
            city_id     : city_id,
            country_id  : country_id,
            region_id   : region_id,
            title       : title,
            title1      : title1,
            title2      : title2,
            title2      : title2,
            dialcode    : dialcode
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (city_id == 0)
                {
                    $('#cities-list > tbody tr#tr-0').after("<tr id=\"tr-" + json.city_id + "\">" + json.content + "</tr>");
                    
                    $('#title-'     + city_id).val('');
                    $('#title1-'    + city_id).val('');
                    $('#title2-'    + city_id).val('');
                    $('#dialcode-'  + city_id).val('');
                    
                    update_qtty_text(1);
                    Message('City was successfully added', 'okay');
                }
                else
                {
                    $('#tr-' + city_id).html(json.content);
                    Message('City was successfully upadated', 'okay');
                }                
            }
            else
            {
                Message(json.message, 'warning');
            }                
        }
    });    
};