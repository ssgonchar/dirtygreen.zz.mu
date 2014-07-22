/**
 * Загружает список activities
 */
var get_activities_list = function()
{
    var parent_id = $('#sel-activity').val();
    if (parent_id == 0) return;
    
    location.href = '/directory/activities/' + parent_id;
};

/**
 * Обновляет подпись с количеством регионов
 */
var update_qtty_text = function(value)
{
    qtty = parseInt($('#hid-activities-count').val()) + parseInt(value);
    
    if (qtty <= 0)
    {
        $('#lbl-activities-count').text('no activities');
        $('#hid-activities-count').val(0);
    }
    else
    {
        $('#lbl-activities-count').text(qtty == 1 ? '1 activity' : qtty + ' activities');
        $('#hid-activities-count').val(qtty);
    }    
};

/**
 * Удаляет страну
 */
var activity_remove = function(activity_id)
{
    if (!confirm('Remove activity ?')) return;
    if (activity_id == 0) return;
    
    $.ajax({
        url: '/activity/remove',
        data : { 
            activity_id  : activity_id
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + activity_id).remove();
                update_qtty_text(-1);
                
                Message('Activity was removed successfully', 'okay');
            }
            else
            {
                Message('Error deleting activity', 'warning');
            }                
        }
    });    
};

/**
 * Переводит строку таблицы стран в режим просмотра или редактирования
 */
var activity_action = function(activity_id, mode)
{
    if (activity_id == 0) return;
    
    $.ajax({
        url: '/activity/action',
        data : { 
            activity_id : activity_id,
            mode        : mode
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#tr-' + activity_id).html(json.content);
            }
            else
            {
                Message('Error editing activity', 'warning');
            }                
        }
    });
};

/**
 * Сохраняет страну
 */
var activity_save = function(activity_id)
{
    var parent_id   = $('#hid-parent-id').val();
    var title       = $('#title-' + activity_id).val().trim();
    
    if (title == '') 
    {
        alert('Please specify title ');
        return;
    }
    
    $.ajax({
        url: '/activity/save',
        data : { 
            activity_id : activity_id,
            parent_id   : parent_id,
            title       : title,
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                if (activity_id == 0)
                {
                    $('#activities-list > tbody tr#tr-0').after("<tr id=\"tr-" + json.activity_id + "\">" + json.content + "</tr>");
                    
                    $('#title-' + activity_id).val('');
                    
                    update_qtty_text(1);
                    Message('Activity successfully added', 'okay');
                }
                else
                {
                    $('#tr-' + activity_id).html(json.content);
                    Message('Activity successfully upadated', 'okay');                    
                }                                
            }
            else
            {
                Message(json.message, 'warning');
            }                
        }
    });    
};
