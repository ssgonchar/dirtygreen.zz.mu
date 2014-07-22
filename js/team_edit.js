/**
 * Выделяет фотографию пользователя, который участвует в команде
 */
var check_user = function(user_id)
{
    var checked = $('#user-' + user_id).val();
    
    if (checked > 0)
    {
        $('#user-' + user_id).val(0);
        
        $('#picture-' + user_id).removeClass('user-pic-s');
        $('#picture-' + user_id).addClass('user-pic');
    }
    else
    {
        $('#user-' + user_id).val(1);
        
        $('#picture-' + user_id).removeClass('user-pic');
        $('#picture-' + user_id).addClass('user-pic-s');
        
    }
};