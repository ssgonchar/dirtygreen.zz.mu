/**
 * Обновляет подпись с количеством регионов
 */
var update_qtty_text = function(value)
{
    qtty = parseInt($('#hid-regions-count').val()) + parseInt(value);
    
    if (qtty <= 0)
    {
        $('#lbl-regions-count').text('no regions');
        $('#hid-regions-count').val(0);
    }
    else
    {
        $('#lbl-regions-count').text(qtty == 1 ? '1 region' : qtty + ' regions');
        $('#hid-regions-count').val(qtty);
    }    
};

/**
 * Загружает список регионов
 */
var get_regions_list = function()
{
    var country_id = $('#sel-country').val();
    if (country_id == 0) return;
    
    location.href = '/directory/regions/' + country_id;
};

/**
 * Удаляет регион
 */
var region_remove = function(region_id)
{
    if (!confirm('Remove region ?')) return;
    if (region_id == 0) return;
    
    $.ajax({
        url: '/region/remove',
        data : { 
            region_id  : region_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + region_id).remove();
                update_qtty_text(-1);
                
                Message('Region was removed successfully', 'okay');
            }
            else
            {
                Message('Error deleting region', 'warning');
            }                
        }
    });    
};

/**
 * Переводит строку таблицы регионов в режим просмотра или редактирования
 */
var region_action = function(region_id, mode)
{
    if (region_id == 0) return;
    
    $.ajax({
        url: '/region/action',
        data : { 
            region_id  : region_id,
            mode        : mode
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + region_id).html(json.content);
            }
            else
            {
                Message('Error loading template', 'warning');
            }                
        }
    });
};

/**
 * Сохраняет регион
 */
var region_save = function(region_id)
{
    var country_id  = $('#hid-country-id').val();
    var title       = $('#title-' + region_id).val().trim();
    var title1      = $('#title1-' + region_id).val();
    var title2      = $('#title2-' + region_id).val();

    if (country_id == 0)
    {
        alert('Please select country ');
        return;        
    }
    
    if (title == '') 
    {
        alert('Please specify title ');
        return;
    }
    
    $.ajax({
        url: '/region/save',
        data : { 
            region_id   : region_id,
            country_id  : country_id,
            title       : title,
            title1      : title1,
            title2      : title2
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (region_id == 0)
                {
                    $('#region-list > tbody tr#tr-0').after("<tr id=\"tr-" + json.region_id + "\">" + json.content + "</tr>");
                    
                    $('#title-' + region_id).val('');
                    $('#title1-' + region_id).val('');
                    $('#title2-' + region_id).val('');
                                        
                    update_qtty_text(1);
                    Message('Region was successfully added', 'okay');
                }
                else
                {
                    $('#tr-' + region_id).html(json.content);
                    Message('Region was successfully upadated', 'okay');
                }                
            }
            else
            {
                Message(json.message, 'warning');
            }                
        }
    });    
};
