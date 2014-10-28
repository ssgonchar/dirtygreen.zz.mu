//AJAX. получаем объект письма
var getEmail = function(email_id) {
    var email = $.ajax({
        url: '/email/getemailobj',
        data: {
            email_id: email_id,
        },
        success: function(json) {
            (function() {
                return json;
            })();
        },
    });
    return JSON.parse(email.responseText);
};

var setStarred = function(email_id, starred) {
    var email = $.ajax({
        url: '/email/setstarred',
        data: {
            email_id: email_id,
            starred: starred
        },
        success: function(json) {
            (function() {
                return json;
            })();
        },
    });
    return JSON.parse(email.responseText);
};

$(function() {
    
    //обработчик нажатия кнопки "Write email"
    $('#new-email').live('click', function()
    {
        delete_new_email_objects();
    });
    
    $('.mbox-toggler').on('click', function() {
        var toggler_element = $(this),
                rel = toggler_element.attr('rel');
        //$('.mbox-spoiler').hide('slow');
        $('.mbox-spoiler[rel="' + rel + '"]').toggle('slow');
    });

    $('.submit-filter').on('click', function() {
        var url = location.href;
        var keyword = $('.filter-keyword').val();

        if (url.match("\/~[0-9]+") != null)
        {
            url = url.replace(/\/~[0-9]+/, '');
        }

        var is_set_filter = url.match("filter\/") == null ? false : true;

        if (!is_set_filter)
        {
            location.href = url + '/filter/keyword:' + keyword;
            return false;
        }

        var is_set_keyword = url.match("keyword:") == null ? false : true;

        if (!is_set_keyword)
        {
            location.href = url + ';keyword:' + keyword;
            return false;
        }
        else
        {
            location.href = url.replace(/keyword:.+\/?/, "keyword:" + keyword);
            return false;
        }

        return false;
    });

    $('.single-checkbox').on('click', function() {
        //console.log($(this));
        //event.preventDefault();
        event.stopPropagation();
    });

    $('.delete-emails').live('click', function() {
        delete_email($(this));
    });

    $('.group-checkbox').on('click', function() {
        var _this = $(this),
                HTML_single_checkboxes = $('.single-checkbox');

        $('.manage-buttons').hide();
        HTML_single_checkboxes.removeAttr('checked');

        if (_this.hasClass('gc-all'))
        {
            HTML_single_checkboxes.attr('checked', 'checked');
        }
        if (_this.hasClass('gc-read'))
        {
            $('.single-checkbox.et-read').attr('checked', 'checked');
        }
        if (_this.hasClass('gc-unread'))
        {
            $('.single-checkbox.et-unread').attr('checked', 'checked');
        }
        if (_this.hasClass('gc-unselect'))
        {
            $('.choosen-items-stats').children('.cis-checked').html(0);
            $('.choosen-items-stats').hide();
            return true;
        }

        var checked_items = $('.single-checkbox:checked');

        if (checked_items.hasClass('et-notspam'))
        {
            if (checked_items.hasClass('et-unread'))
            {
                $('.manage-buttons.mb-type-read').show();
            }
            if (checked_items.hasClass('et-read'))
            {
                $('.manage-buttons.mb-type-unread').show();
            }
            $('.manage-buttons.mb-type-spam').show();
            $('.manage-buttons.mb-type-delete').show();
            $('.manage-buttons.mb-type-restore').show();
        }
        else
        {
            if (checked_items.length > 0)
            {
                $('.manage-buttons.mb-type-notspam').show();
                $('.manage-buttons.mb-type-delete').show();
                $('.manage-buttons.mb-type-restore').show();
            }
        }

        $('.choosen-items-stats').children('.cis-checked').html(checked_items.length);

        checked_items.length > 0 ? $('.choosen-items-stats').show() : $('.choosen-items-stats').hide();
    });

    $('.single-checkbox').on('click', function() {
        var HTML_checkall = $('.choose-all-checkboxes'),
                checked_items = $('.single-checkbox:checked');

        $('.manage-buttons').hide();
        HTML_checkall.removeAttr('checked');

        if (checked_items.length === $('.single-checkbox').length)
        {
            HTML_checkall.attr('checked', 'checked');
        }

        if (checked_items.hasClass('et-notspam'))
        {
            if (checked_items.hasClass('et-unread'))
            {
                $('.manage-buttons.mb-type-read').show();
            }
            if (checked_items.hasClass('et-read'))
            {
                $('.manage-buttons.mb-type-unread').show();
            }
            $('.manage-buttons.mb-type-spam').show();
            $('.manage-buttons.mb-type-delete').show();
            $('.manage-buttons.mb-type-restore').show();
        }
        else
        {
            if (checked_items.length > 0)
            {
                $('.manage-buttons.mb-type-notspam').show();
                $('.manage-buttons.mb-type-delete').show();
                $('.manage-buttons.mb-type-restore').show();
            }
        }

        $('.choosen-items-stats').children('.cis-checked').html(checked_items.length);

        checked_items.length > 0 ? $('.choosen-items-stats').show() : $('.choosen-items-stats').hide();
    });


    $('.bc-list-toggle-visibility').on('click', function() {
        $('.bc-list').toggleClass('expanded', 'collapsed');
        $('.expand, .collapse').toggleClass('on', 'off');

        event.preventDefault();
        event.stopPropagation();
    });

    if ($.prettyPhoto != undefined)
    {
        try {
            $("a[rel^='pp_attachments']").prettyPhoto({gallery_markup: '', social_tools: ''});
        } catch (e) {
        }
    }

    $('.panel-heading').on('click', function() {
        var panel_id = $(this).find("a").attr('href');
        $(panel_id).collapse('toggle');
        var destination = $(this).offset().top - $('.panel-heading').height() - $('.navbar-header').height();
//jQuery.fx.interval = 5;
        if ($.browser.safari) {
            //$('body').css('position','relative');
            $('body').animate({scrollTop: destination}, 800, 'easeOutQuad');
        } else {
            //$('html').css('position','relative');
            //$('html').animate({ scrollTop: destination }, 'slow');
            //$('html').slide('up','hide', destination,'slow');
            $('html').animate({scrollTop: destination}, 800, 'easeOutQuad');
        }

        //console.log($(this));
        event.preventDefault();
        event.stopPropagation();
    });

    $('.btn-hide-text').live('click', function(event) {
        var email_id = $(this).data('emailId');
        var result = getEmail(email_id);
        var $email_description_block = $(this).parent().parent().find('.email-description');
        //var email_description = $email_description_block.html();
        $email_description_block.html(result.email.email.description.substring(0, 301) + ' ...');
        $email_description_block.append("<br/><button class='btn btn-default btn-read-more' data-email-id='" + email_id + "'>Full view</button>");
        event.preventDefault();
        event.stopPropagation();
    });

    $('.btn-read-more').live('click', function(event) {
        var email_id = $(this).data('emailId');
        var result = getEmail(email_id);
        var $email_description = $(this).parent().parent().find('.email-description');
        //console.log(result);
        $email_description.html(result.email.email.description_html);
        $email_description.append("<br/><button class='btn btn-default btn-hide-text' data-email-id='" + email_id + "'>Short view</button>");
        event.preventDefault();
        event.stopPropagation();
    });

    $('.btn').live('click', function(event) {
        if ($(this).data('toggle') == 'modal') {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    $('.email-starr-empty, .email-starr').live('click', function() {
        var email_id = $(this).data('id');

        if ($(this).hasClass('email-starr-empty')) {
            $(this).removeClass('email-starr-empty').addClass('email-starr');
            var starred = true;
        } else {
            $(this).removeClass('email-starr').addClass('email-starr-empty');
            var starred = false;
        }

        setStarred(email_id, starred);
    });

    $('label.tree-toggler').live('click', function() {
        $(this).parent().children('ul.tree').toggle(300);
    });

    $(".input-biz-title").live('focus', function() {
        var value = $(this).val();
        $(".input-biz-title").val('');
        $(this).val(value);
    });
    $(".input-biz-title").live('keydown', function() {
        var id = $(this).attr('id');
        console.log(id);
        bind_biz_autocomplete_emailmanager('.input-biz-title');
    });
    $(".add-biz-object").live('click', function() {
        var selector_biz_id = $(this).data('selector_biz_id');
        var email_id = $(this).data('email_id');

        var biz_id = $(selector_biz_id).val();
        console.log(biz_id);
        console.log(email_id);
        var obj = $(this).parents('td[aria-describedby="jsonmap_object_alias_html"]');
        add_email_object('biz', biz_id, email_id, obj);
    });

    $('.email-row, td[aria-describedby="jsonmap_object_alias_html"]').live('click', function() {
        if ($(this).find('.search-biz:visible').length > 0) {
            return;
        }
        //переделать. синглтон
        $('.search-biz:visible').hide('100');
        $(this).find('.search-biz').show('300');

    });

    $('.search-biz:visible').live('click', function(e) {
        e.stopPropagation();
    });

    $('.remove-object-alias').live('click', function(e) {
        e.stopPropagation();

        var biz_id = $(this).data('object-id');
        var email_id = $(this).data('email-id');
        var obj = $(this).parents('td[aria-describedby="jsonmap_object_alias_html"]');

        remove_email_object('biz', biz_id, email_id, obj);
    });



    $('.close-edit-form').live('click', function() {
        getFilters();
        $('.row-edit-form').hide('100');
        $('.row-filter-table').show('300');
    });

    $('.apply-edit', '#filterModal').live('click', function() {
        var formData = $("form", '#filterModal').serialize();

        console.log(formData);
    });
    $('.row-form').live('click', function() {
        if ($(this).find('.explanation:visible').length > 0) {
            return;
        }
        $('.explanation:visible').hide('100');
        $(this).find('.explanation').show('300');
    });
    $('#filterModal').live('show.bs.modal', function(e) {
        //получаем информацию о фильтрах
        getFilters();
    });
    $('.btn-filter-edit').live('click', function() {
        var filter_id = $(this).data('filter-id');

        //получаем информацию о фильтре
        getFilter(filter_id);

        //заполняем форму данными

        $('.row-filter-table').hide('100');
        $('.row-edit-form').show('300');
    });
    $('.btn-biz-add', $('#filterModal')).live('click', function() {
        editFilter();
    });
    $('.apply-edit', $('#filterModal')).live('click', function(e){
        editFilter();
    });
    $('.apply-edit', $('#filterModal')).live('click', function(e){
        editFilter();
    });
    $('.apply-add', $('#filterModal')).live('click', function(e){
        addFilter();
    });
    
    $('.remove-filter-alias', $('#filterModal')).live('click', function(e){
        var preventTag = $('.add-tags', $('#filterModal')).val();
        var deleteBizId = $(this).data('objectid');
        var deleteTag = 'biz:'+deleteBizId+';';
        var updatedTag = preventTag.replace(new RegExp(deleteTag, 'g'), "");
        console.log(deleteBizId);
        console.log(deleteTag);
        console.log(updatedTag);
        $('.add-tags', $('#filterModal')).val(updatedTag);
        editFilter();
        
    });
    
    $('.add-new-filter', $('#filterModal')).live('click', function(e){
        getFilter(0);
        $('.row-filter-table').hide('100');
        $('.row-edit-form').show('300');        
    });
    $('.btn-filter-delete', $('#filterModal')).live('click', function(e){
       var filter_id = $(this).data('filter-id');
       var result = confirm('Are you shure?');
       if(result) {
            deleteFilter(filter_id);
       }
       console.log(filter_id);
    });
    

});

var remove_email_object = function(obj_alias, obj_id, email_id, obj) {
    $.ajax({
        url: '/emailmanager/delobject',
        data: {
            email_id: email_id,
            object_id: obj_id,
            object_alias: obj_alias
        },
        success: function(json) {
            var row_id = "#" + email_id + "email-row";
            $(obj).html(json.html);
        },
    });
}

var add_email_object = function(obj_alias, obj_id, email_id, obj) {
    $.ajax({
        url: '/emailmanager/addobject',
        data: {
            email_id: email_id,
            object_id: obj_id,
            object_alias: obj_alias
        },
        success: function(json) {
            var row_id = "#" + email_id + "email-row";
            $(obj).html(json.html);
        },
    });
}

/**
 * Назначает элементу вода бизнеса с классом biz-autocomplete функцию автозаполнения
 * для работы должно быть два поля: {text id="biz-title" class="biz-autocomplete"} и {hidden id="biz-title-id"}
 * у hidden поля должен быть id такой же как у text с суффиксом "-id"
 * 
 * @version 20120722, zharkov
 */
var bind_biz_autocomplete_emailmanager = function(biz_selector, callback_function)
{
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

                obj_id = $(this).attr('id');
                $('#' + obj_id + '-id').val(0);

                $.ajax({
                    url: "/biz/getlistbytitle",
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
                }
                else
                {
                    $('#' + obj_id + '-id').val(0);
                }

                if (callback_function)
                    callback_function(ui.item.value);

                return false;

            },
            open: function() {

                if ($('.ui-autocomplete > li').length > 20)
                {
                    $(this).autocomplete('widget').css('z-index', 999999).css('height', '200px').css('overflow-y', 'scroll');
                }
                else
                {
                    $(this).autocomplete('widget').css('z-index', 999999);
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

var getFilters = function() {
    $.ajax({
        url: '/emailmanager/getfilters',
        success: function(json) {
            //var row_id = "#" + email_id + "email-row";
            $('.row-filter-table').html(json.html);
            console.log(json);
        },
    });
}
var deleteFilter = function(filter_id) {
    $.ajax({
        url: '/emailmanager/deletefilter',
        data : {
            filter_id : filter_id
        },
        success: function(json) {
            //var row_id = "#" + email_id + "email-row";
            $('.row-filter-table').html(json.html);
            console.log(json);
        },
    });    
}

var getFilter = function(filter_id) {
    $.ajax({
        url: '/emailmanager/getfilter',
        data: {
            filter_id: filter_id
        },
        success: function(json) {
            $('.row-edit-form').html(json.html);
            console.log(json);
        },
    });
}

var editFilter = function() {
    var filterId = $('.add-filter-id', $('#filterModal')).val();
    var bizId = $('.add-biz-id', $('#filterModal')).val();

    var tagsPrevent = $('.add-tags', $('#filterModal')).val(); 

    var bizTag = (tagsPrevent.indexOf('biz:' + bizId + ';') + 1) ? '' : 'biz:' + bizId + ';';
    var tags = (bizId > 0) ? tagsPrevent + bizTag : tagsPrevent;

    var params = '';
    var from = $('.add-from', $('#filterModal')).val();
    var to = $('.add-to', $('#filterModal')).val();
    var keyword = $('.add-keyword', $('#filterModal')).val();
    var isSheduled = $(".is-sheduled", $('#filterModal')).is(':checked') ? 0 : 1;
    var addAtachments = $(".add-attachments", $('#filterModal')).is(':checked') ? 1 : 0;

    (from !== '') ? params += "from:" + from + ";" : params += '';
    (to !== '') ? params += "to:" + to + ";" : params += '';
    (keyword !== '') ? params += "subject:" + keyword + ";text:" + keyword + ";" : params += '';
    (addAtachments > 0) ? params += "attachment:yes;" : params += "";

    $.ajax({
        url: '/emailmanager/editfilter',
        data: {
            filter_id: filterId,
            tags: tags,
            params: params,
            is_sheduled: isSheduled
        },
        success: function(json) {
            $('.row-edit-form', $('#filterModal')).html(json.html);
            //console.log(json);
        },
    });
}

var addFilter = function() {
    var filterId = 0;
    var bizId = $('.add-biz-id', $('#filterModal')).val();

    //var bizTag = (tagsPrevent.indexOf('biz:' + bizId + ';') + 1) ? '' : 'biz:' + bizId + ';';
    var tags = (bizId > 0) ? 'biz:' + bizId + ';' : '';

    var params = '';
    var from = $('.add-from', $('#filterModal')).val();
    var to = $('.add-to', $('#filterModal')).val();
    var keyword = $('.add-keyword', $('#filterModal')).val();
    var isSheduled = $(".is-sheduled", $('#filterModal')).is(':checked') ? 0 : 1;
    var addAtachments = $(".add-attachments", $('#filterModal')).is(':checked') ? 1 : 0;

    (from !== '') ? params += "from:" + from + ";" : params += '';
    (to !== '') ? params += "to:" + to + ";" : params += '';
    (keyword !== '') ? params += "subject:" + keyword + ";text:" + keyword + ";" : params += '';
    (addAtachments > 0) ? params += "attachment:yes;" : params += "";

    $.ajax({
        url: '/emailmanager/addfilter',
        data: {
            filter_id: filterId,
            tags: tags,
            params: params,
            is_sheduled: isSheduled
        },
        success: function(json) {
            $('.row-edit-form', $('#filterModal')).html(json.html);
            console.log(json);
        },
    });
}

/**
 * При создании нового сообщения удаляет ключи из сессии и аттачи из БД
 * 
 * @author Sergey Uskov
 */
var delete_new_email_objects = function()
{
    $.ajax({
            type:"POST",
            url: "/emailmanager/deletenewemailobjects",
            data: {
                //
            },
            success: function(json) {
                //
            }
        });
};