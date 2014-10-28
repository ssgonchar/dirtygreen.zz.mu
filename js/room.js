$(function()
{
    room_biz_autocomplete('#add-room-biz-title');

    $('.chat-room-user').live('click', function() {
        var obj_user = this;
        room_add_user(obj_user);
    });

    $('.chat-room-added-user').live('click', function() {
        var obj_user = this;
        room_delete_user(obj_user);
    });

    $('.add-room-submit').live('click', function() {
        if ($('#add-room-biz-title').val() == '') {
            $('#add-room-biz-title').attr('data-biz-id', 0);
        }
        var biz_id = $('#add-room-biz-title').attr('data-biz-id');
        var room_title = $('#add-room-title').val();

        var added_users_arr = new Array();
        var added_users_obj = $('.add-room-added-users img');
        $(added_users_obj).each(function() {
            var user_icon_id = $(this).attr('id');
            var user_id = parseInt(user_icon_id);
            added_users_arr.push(user_id);
        });

        create_room(biz_id, room_title, added_users_arr);
    });
});
/**
 * Назначает элементу вода бизнеса с классом biz-autocomplete функцию автозаполнения
 * для работы должно быть два поля: {text id="biz-title" class="biz-autocomplete"} и {hidden id="biz-title-id"}
 * у hidden поля должен быть id такой же как у text с суффиксом "-id"
 * 
 * @version 20120722, zharkov
 */
var room_biz_autocomplete = function(biz_selector, callback_function)
{
    biz_selector = biz_selector || 'no selector';

    if (biz_selector === 'no selector') {
        return;
    }

    if ($(biz_selector).length == 0)
        return;

    $(biz_selector).each(function() {

        obj_id = $(this).attr('id');
        title_field = $(this).data('titlefield') || 'doc_no_full';

        // предотвращает пост формы при нажатии Enter в поле
        $(this).keypress(function(event) {
            if (event.keyCode == 13)
            {
                return false;
            }
        });

        $(this).autocomplete({
            source: function(request, response) {

//                obj_id = $(this).attr('id');
                $('#' + obj_id + '-id').val(0);

                $.ajax({
                    url: "/biz/getlistbytitle",
                    async: true,
                    data: {
                        maxrows: 250,
                        title: request.term,
                        title_field: title_field
                    },
                    success: function(data) {
                        response($.map(data.list, function(item) {
                            return {
                                label: item.biz.list_title,
                                value: item.biz.id
                            }
                        }));

                    }
                });

            },
            minLength: 3,
            delay: 500,
            select: function(event, ui) {

                obj_id = $(this).attr('id');
                if (ui.item)
                {
                    $(this).val(ui.item.label);
                    $('#' + obj_id + '-id').val(ui.item.value);
                    add_room_select_biz(ui.item.value);
                }
                else
                {
                    $('#' + obj_id + '-id').val(0);
                    add_room_select_biz(0);
                }

                if (callback_function)
                    callback_function(ui.item.value);

                return false;

            },
            open: function() {

                if ($('.ui-autocomplete > li').length > 20)
                {
                    $(this).autocomplete('widget').css('z-index', 9999999).css('height', '300px').css('overflow-y', 'scroll');
                }
                else
                {
                    $(this).autocomplete('widget').css('z-index', 9999999);
                }

                return false;
            },
            close: function() {
            },
            focus: function(event, ui)
            {
                return false;
            }
        });
    });
}

/**
 * Select recipient in chat modal window
 */
var chat_room_select_user = function(user_id)
{
    var relation = $('#user-' + user_id + '-relation').val();

    if (relation == '')
    {
        relation = 'r';

        $('#user-' + user_id + '-cc').hide();
        $('#user-' + user_id + '-re').show();
    }
    else if (relation == 'r')
    {
        relation = 'c';

        $('#user-' + user_id + '-cc').show();
        $('#user-' + user_id + '-re').hide();
    }
    else
    {
        relation = '';

        $('#user-' + user_id + '-cc').hide();
        $('#user-' + user_id + '-re').hide();
    }

    $('#user-' + user_id + '-relation').val(relation);
};

var add_room_select_biz = function(biz_id) {
    $('#add-room-biz-title').attr('data-biz-id', biz_id);
    //console.log($('#add-room-biz-title').data('biz-id'));
}

var room_add_user = function(obj_user) {
    //работа с html
    var html_user = $(obj_user).html();
    var style_user = $(obj_user).attr('style');
    var added_user = '<div style="' + style_user + '" class="chat-room-added-user">' + html_user + '</div>';
    $(obj_user).remove();
    $('.add-room-added-users').append(added_user);

    //работа с данными
}

var room_delete_user = function(obj_user) {
    //работа с html
    var html_user = $(obj_user).html();
    var style_user = $(obj_user).attr('style');
    var removed_user = '<div style="' + style_user + '" class="chat-room-user">' + html_user + '</div>';
    $(obj_user).remove();
    $('.add-room-switch-users').append(removed_user);

    //работа с данными
}

var create_room = function(biz_id, room_title, added_users_arr) {
    $.ajax({
        url: '/chat/createroom',
        data: {
            biz_id: biz_id,
            room_title: room_title,
            users: added_users_arr
        },
        success: function(json) {
            if (json.result == 'okay')
            {
                $('div.errors').remove();
            }
            else if(json.result == 'error')
            {
                $('div.errors').remove();
                var html_errors = '<div class="errors">';
                for( var i = 0; i < $(json.errors).length; i++) {
                    html_errors += '<h5 style="color: red">' + json.errors[i] + '</h5>';
                }
                html_errors += '</div>';
                
                $('#add-room .modal-body').prepend(html_errors);
            }

            console.log(json);
        }
    });
}