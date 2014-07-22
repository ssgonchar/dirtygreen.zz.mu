/**
 * Очищает результаты поиска
 */
var email_clear_search_results = function()
{
    $('#keyword').val('');
    $('#email-co-search-result').empty();
};

/**
 * Показывает окно поиска и выбора компаний для бизнеса
 */
var show_email_co_list = function()
{
    $('#email-co-select').show();
};

/**
 * Прячет окно выбора компаний для бизнеса
 */
var close_email_co_list = function()
{
    $('#email-co-select').hide();
};

/**
 * Ищет компании
 */
var find_email_objects = function(title)
{
    if (title.replace(/\s+/g, '').length < 3) return;

    var type_alias = $('#email-co-type-alias').val();
    
    if (type_alias == '') return;

    $('#email-co-search-result').empty();
    $('#email-co-search-result').append($('<option value="0" style="font-style: italic;">loading...</option>'));
    
    $.ajax({
        url: '/email/getobjectslist',
        data : {
            type_alias : type_alias,
            title : title
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#email-co-search-result').empty();
                if (json.list && json.list.length > 0)
                {
                    $.each(json.list, function(key, item){
                        var object  = item[type_alias];
                        var name    = type_alias == 'biz' || type_alias == 'order' ? object.doc_no_full : object.doc_no;
                        $('#email-co-search-result').append($('<option value="' + object.id + '">' + name + '</option>'));
                    });
                }                
                else
                {
                    $('#email-co-search-result').append($('<option value="0" style="font-style: italic;">nothing was found</option>'));
                }
            }
            else
            {
                error = true;
            }                
        }
    });    
};

/**
 * Добавляет найденные компании в бизнес
 */
var add_email_object = function()
{
    var object_alias   = $('#email-co-type-alias').val();
    if (object_alias == '') return;

    $('#email-co-search-result > option:selected').each(function(){        
        var object_id = $(this).val();
        
        if (object_id > 0 && $('#' + object_alias + '-' + object_id).html() == null)
        {           
            object_title    = $(this).text();            
            new_row         = '<span id="' + object_alias + '-' + object_id +'" style="margin-right: 10px;">' + 
                                '<input type="hidden" name="objects[' + object_alias + '-' + object_id + ']" class="' + object_alias + '-object" value="' + object_id + '"><a class="tag-' + object_alias + '" style="vertical-align: top; margin-right: 3px;" href="/' + object_alias + '/' + object_id + '" target="_blank">' + object_title + '</a></td><td width="20px"><img src="/img/icons/cross-small.png" onclick="remove_email_object(\'' + object_alias + '\', ' + object_id + ');"></span>';
            //alert(new_row);return false;
            $(new_row).appendTo('.email-co-objects-list');
            
            $('#email-co-type-alias option').removeAttr('selected');
            $('#email-co-type-alias option:first').attr('selected', 'selected');
            $('#email-co-search-result').html('');
            $('#keyword').val('');
        }
        //close_email_co_list();
    });    
};
/**
 * Удаляет объект из списка компаний 
 */
var remove_email_object = function(object_alias, object_id)
{
    var element = $('#' + object_alias + '-' + object_id);
    
    if (!confirm('Remove ' + element.children('a').html() + ' ?')) return;
    
    element.remove();
};