/**
 * OnLoad
 * version 20120711, zharkov
 */
$(window).load(function () {


    $(".chat-user-container > img").bind("click", function (e) {
        var object_alias = 'chat';
        var object_id = '0';
        var user_id = this.id;
        show_chat_modal_for_user(object_alias, object_id, user_id);

        console.log(parseInt(e.id));
    });



    $("#chat_icon_park").bind('affixed.bs.affix', function () {
        $(this).css("right", "200px");
    })

    /* $( ".chat-user-container" ).on( "click", function() {
     console.log('this');*/

    $("#sender-title").autocomplete({
        source: function (request, response) {

            $('#sender-id').val(0);

            $.ajax({
                url: "/user/getlistbytitle",
                data: {
                    maxrows: 25,
                    login: request.term
                },
                success: function (data) {
                    // console.log(data.list);
                    var obj = jQuery.parseJSON(data);
                    console.log(data.list);
                    response($.map(data.list, function (item) {
                        //console.log(item);

                        return {
                            label: item.user.full_login,
                            value: item.user.id
                        }

                    }));
                }
            });

        },
        minLength: 2,
        select: function (event, ui) {

            if (ui.item)
            {
                $('#sender-title').val(ui.item.label);
                $('#sender-id').val(ui.item.value);
            }
            else
            {
                $('#sender-id').val(0);
            }

            return false;

        },
        open: function () {
        },
        close: function () {
        },
        focus: function (event, ui)
        {
            return false;
        }
    });

    $("#sender-title").keypress(function (event) {
        if (event.keyCode == 13)
        {
            return false;
        }
    });


    $("#recipient-title").autocomplete({
        source: function (request, response) {

            $('#recipient-id').val(0);

            $.ajax({
                url: "/user/getlistbytitle",
                data: {
                    maxrows: 25,
                    login: request.term
                },
                success: function (data) {
                    response($.map(data.list, function (item) {
                        return {
                            label: item.user.full_login,
                            value: item.user.id
                        }
                    }));
                }
            });

        },
        minLength: 2,
        select: function (event, ui) {

            if (ui.item)
            {
                $('#recipient-title').val(ui.item.label);
                $('#recipient-id').val(ui.item.value);
            }
            else
            {
                $('#recipient-id').val(0);
            }

            return false;

        },
        open: function () {

        },
        close: function () {

        }
    });

    $("#recipient-title").keypress(function (event) {
        if (event.keyCode == 13)
        {
            return false;
        }
    });
    $('.bc-list-toggle-visibility > .btn').live('click', function () {
        $('.bc-list').toggleClass('expanded', 'collapsed');
        $('.expand, .collapse').toggleClass('on', 'off');

        event.preventDefault();
        event.stopPropagation();
    });

    /**
     * Обработчик .msg-reply
     * Назначение кнопки: открыть окно создания сообщения с заполненным названием
     * телом сообщения и получателем
     */
    $('.msg-reply').live('click', function (e) {
        //console.log(e);
        var user_id = $(this).data('user-id');
        var msg_id = $(this).data('msg-id');
        show_chat_modal_reply('chat', user_id, msg_id);
    });

    var chat_search = {
        limit: 100,
        start: 0,
        count: 0
    };

    $('.find-messages, .find-messages-in').live('click', function (e) {
        $('.chat-search-show-more').data('start', 0).hide();
        var msg_ids = "";
        if ($(this).hasClass('find-messages-in')) {
            msg_ids = $('#msg-ids').val();
        }
        console.log(chat_search);
        $.ajax({
            url: '/chat/searchmsg',
            data: {
                keyword: $('#keyword').val(),
                date_from: $('#date-from').val(),
                date_to: $('#date-to').val(),
                sender_title: $('#sender-title').val(),
                sender_id: $('#sender-id').val(),
                recipient_title: $('#recipient-title').val(),
                recipient_id: $('#recipient-id').val(),
                //search_type : order_last_id,
                is_dialogue: $('#is-dialogue').prop("checked"),
                is_phrase: $('#is-phrase').prop("checked"),
                msg_ids: msg_ids,
                limit: chat_search.limit
            },
            success: function (json) {
                $('#room-search').html(json.html);
                $('.timeline-badge img').addClass('img-circle').width('100%');
                $('#msg-ids').val(json.ids);
                //console.log(json);

                if ($('#msg-ids').val() !== '') {
                    
                    $('.find-messages-in').show();
                    
                }
                $('.search-result-tab').show();
                $('#myTab a[href="#room-search-place"]').tab('show');
                //console.log($('.make-ref-new[zclip=0]').length);
                $('.make-ref-new[zclip="0"]').attr('zclip', '1').zclip({
                    path: "js/ZeroClipboard.swf",
                    copy: function (event) {
                        return $(this).data('ref');
                    }


                });
                //console.log($('.make-ref-new[zclip=0]').length);
                $('#myModal').modal('hide');


                chat_search.count = json.count;
                chat_search.start = json.start;

                if (chat_search.start < chat_search.count) {
                    //var btn = '<span class="btn btn-default" data-start="'+chat_search.start+'">Show more</span>';
                    $('.chat-search-show-more').show();
                    //console.log(chat_search.start);
                }

//                if((chat_search.start + chat_search.limit) < chat_search.count) {
//                    $('.chat-search-show-more').trigger('click');
//                }                
            }
        });
    });

    $('.chat-search-show-more').live('click', function (e) {
        $.ajax({
            url: '/chat/searchmsg',
            data: {
                keyword: $('#keyword').val(),
                date_from: $('#date-from').val(),
                date_to: $('#date-to').val(),
                sender_title: $('#sender-title').val(),
                sender_id: $('#sender-id').val(),
                recipient_title: $('#recipient-title').val(),
                recipient_id: $('#recipient-id').val(),
                //search_type : order_last_id,
                is_dialogue: $('#is-dialogue').prop("checked"),
                is_phrase: $('#is-phrase').prop("checked"),
                //msg_ids: msg_ids,
                limit: chat_search.limit,
                start: chat_search.start
            },
            success: function (json) {
                $('.search-target').append(json.html);
                $('.timeline-badge img').addClass('img-circle').width('100%');
                //$('#msg-ids').val(json.ids);
                //console.log(json);
                /*
                 if ($('#msg-ids').val() !== '') {
                 $('#myTab a[href="#room-search-place"]').tab('show');
                 $('.find-messages-in').show();
                 $('.search-result-tab').show();
                 }*/
                //console.log($('.make-ref-new[zclip=0]').length);


                //console.log($('.make-ref-new[zclip=0]').length);
                //$('#myModal').modal('hide');


                //chat_search.count = json.count;
                chat_search.start = json.start;

                if (chat_search.start < chat_search.count) {
                    //var btn = '<span class="btn btn-default" data-start="'+chat_search.start+'">Show more</span>';
                    $('.chat-search-show-more').show();
                    //console.log(chat_search.start);

                } else {
                    $('.chat-search-show-more').hide();
                }
                var search_page_class = '.search-page-'+json.current_page;
                $(search_page_class).attr('zclip', '1').zclip({
                    path: "js/ZeroClipboard.swf",
                    copy: function (event) {
                        return $(this).data('ref');
                    }


                });
//                if((chat_search.start + chat_search.limit) < chat_search.count) {
//                    $('.chat-search-show-more').trigger('click');
//                }
            }
        });
        //console.log($('.make-ref-new[zclip=0]').length);

        //console.log($('.make-ref-new[zclip=0]').length);
    });
});

var show_chat_modal_reply = function (object_alias, object_id, message_id)
{
    var message_id = (typeof message_id == 'undefined') ? 0 : message_id;

    var h = 500;  //520
    var w = 950;
    var left = Number((screen.width / 2) - (w / 2));
    var top = Number((screen.height / 2) - (h / 2));

    var time = new Date().getTime();
    var url = '/newmessage';

    if (message_id > 0)
    {
        url += '/answer/' + message_id;
    }
    else if (object_alias != '' && object_id > 0)
    {
        url += '/' + object_alias + '/' + object_id;
    }

    new_window = window.open(url, 'new_mesage_' + time, 'toolbar=no, location=no, directories=no, status=no, menubar=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    new_window.focus();
}