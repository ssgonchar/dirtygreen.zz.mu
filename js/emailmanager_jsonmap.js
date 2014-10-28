/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//json data in the format of type:
//{ total: xxx, page: yyy, records: zzz, rows: [ 
//{name1:”Row01″,name2:”Row 11″,name3:”Row 12″,name4:”Row 13″,name5:”Row 14″}, 
//...
$(function ()
{
    var pageWidth = $("#ui-jqgrid").width() - 80;
    var pageHeight = $("window").height() - 390;

    jQuery("#jsonmap").jqGrid({
        url: '/tableeditor/emailmanagercreatetable?table=Email',
        mtype: 'POST',
        datatype: "json",
        colNames: ['Sender', 'Subject', 'Type', 'BIZes', 'Date', ''],
        colModel: [
            {name: 'sender_address', index: 'sender_address', width: (pageWidth * (15 / 100)), searchoptions: {sopt: ['cn', 'eq']}, search: true},
            {name: 'doc_no', index: 'title', width: (pageWidth * (10 / 100)), searchoptions: {sopt: ['cn', 'eq']}},
            {name: 'type_id_html', index: 'type_id', align: "left", width: (pageWidth * (5 / 100)), search: true, searchoptions: {sopt: ['eq'], value: ":All;0:Not set;1:Inbox;2:Sent;3:Draft;4:Error;5:Spam"}, stype: 'select'},
            {name: 'object_alias_html', index: 'object_alias', width: (pageWidth * (17 / 100)), sortable: false, search: false, searchoptions: {sopt: ['eq', 'ne', 'bw', 'cn']}},
            {name: 'date_mail', index: 'date_mail', align: "right", width: (pageWidth * (10 / 100)), search: false, searchoptions: {sopt: ['eq', 'ne', 'bw', 'cn']}, formatter: 'date', formatoptions: {srcformat: 'Y-m-d H:i:s', newformat: 'd/m/Y H:i:s'}},
            {name: 'action_html', align: "left", width: (pageWidth * (6 / 100)), search: false}
        ],
        rowNum: 50,
        rowList: [20, 50, 100],
        pager: '#pjmap',
        //loadonce:true,
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        jsonReader: {
            repeatitems: false,
            id: "0"
        },
        caption: "Emails",
        height: pageHeight * 1,
        autowidth: true,
        shrinktofit: true
    });

    table_biz = 0;

    //Обработчик для ссылок на биз
    $('a.tag-biz').live('click', function (e) {
        e.preventDefault();
        var biz_id = $(this).data('biz-id');
        var current_url = jQuery('#jsonmap').jqGrid('getGridParam', 'url');
        var new_url = current_url + '&biz_id=' + biz_id;
        //jQuery("#jsonmap").jqGrid('setGridParam', {url: new_url}).trigger("reloadGrid");
        jQuery("#jsonmap").jqGrid('setGridParam', {postData: {biz_id: biz_id}}).trigger("reloadGrid");
        $('.clear-biz-filter').show();
        console.log(new_url);
    });

    //автоматическое изменение ширины таблицы при 
    //изменении ширины окна браузера
    $(window).bind('resize', function () {
        if (grid = $('.ui-jqgrid-btable:visible')) {
            grid.each(function (index) {
                gridId = $(this).attr('id');
                gridParentWidth = $('#gbox_' + gridId).parent().width();
                $('#' + gridId).setGridWidth(gridParentWidth - 20);
            });
        }
    }).trigger('resize');

    //обработчик для типов имейлов
    //в боковой панельке
    $('.select-email-type').live('click', function (e) {
        var type_ids = '';

        $('.select-email-type:checked').each(function () {
            type_ids += $(this).data('type-id') + ', ';
        });
        //type_ids = type_ids.substr(type_ids.length - 1);
        if (type_ids.length > 0) {
            type_ids = type_ids.substring(0, type_ids.length - 2);
        }

        jQuery("#jsonmap").jqGrid('setGridParam', {postData: {email_type_ids: type_ids}}).trigger("reloadGrid");

        console.log(type_ids);
    });
    $('.btn-find-keyword').live('click', function () {
        var keyword = '';

        keyword = $('.find-keyword').val();

        //if (keyword.length > 0) {
        jQuery("#jsonmap").jqGrid('setGridParam', {postData: {email_keyword: keyword}}).trigger("reloadGrid");
        //}
    });
    $('.find-biz-team').live('change', function (e) {
        bind_products(this.value, true);
    });

    $('.find-biz-for-nav').live('click', function (e) {
        console.log('find_biz_for_nav');
        find_biz_for_nav();
        $('.find-biz-for-nav-save').show();
    });
    $('.find-biz-check').live('click', function (e) {
        var biz_id = $(this).data('biz-id');
        console.log(biz_id);
    });
    $('.find-biz-check-all').live('click', function (e) {
        $('input[type="checkbox"]', $('.find-biz-search-result')).attr('checked', true);
        console.log('check all');
    });
    $('.find-biz-uncheck-all').live('click', function (e) {
        $('input[type="checkbox"]', $('.find-biz-search-result')).attr('checked', false);
        console.log('uncheck all');
    });

    var find_biz_ids = "";
    $('.find-biz-for-nav-save').live('click', function (e) {
        find_biz_ids = "";
        var checked = $("input:checked", $('.find-biz-search-result')).length;
        var bizs = $("input:checked", $('.find-biz-search-result'));
        var biz_ids = "";
        bizs.each(function (obj) {
            biz_ids += $(this).data('biz-id') + ', ';
        });
        find_biz_ids = biz_ids;
        $('.find-biz-search-form').hide();
        $('.find-biz-find-btn-row').hide();
        $('.find-biz-search-result-row').hide();
        $('.find-biz-manage-group').show('fast');
        $('.find-biz-search-footer').hide();
        $('.find-biz-add-footer').show();
        console.log(biz_ids);
    });
    $('.find-biz-btn-back').live('click', function (e) {
        $('.find-biz-manage-group').hide('fast');
        $('.find-biz-search-form').show();
        $('.find-biz-find-btn-row').show();
        $('.find-biz-search-result-row').show();
        $('.find-biz-add-footer').hide();
        $('.find-biz-search-footer').show();
        find_biz_ids = "";
    });

    $('.find-biz-menu-save').live('click', function (e) {
        if (find_biz_ids.length > 0) {
            var group_id = $('.find-biz-group-id').val();
            var new_group_title = $('.find-biz-new-group-title').val();
            bind_save_biz_menu(group_id, new_group_title, find_biz_ids);
        }
    });
    $('.find-biz-new-group-title').live('keyup', function (e) {
        if ($('.find-biz-new-group-title').val() !== "") {
            $('.find-biz-group-id').attr('disabled', 'true');
        } else {
            $('.find-biz-group-id').removeAttr('disabled');
        }
    });
    $('.find-biz-group-link').live('click', function (e) {

        var group_biz_id = $(this).data('group-id');
        bind_group_biz_select(group_biz_id);
        $('.clear-biz-filter').show();
    });
    $('.find-biz-remove-group').live('click', function (e) {
        var group_id = $(this).data('group-id');
        bind_group_biz_delete(group_id);
    });
    $('.find-biz-remove-biz').live('click', function (e) {
        var biz_id = $(this).data('biz-id');
        var group_id = $(this).data('group-id');
        bind_menu_biz_delete(biz_id, group_id);
    });
    $('.clear-biz-filter').live('click', function (e) {
        jQuery("#jsonmap").jqGrid('setGridParam', {postData: {biz_id: ''}}).trigger("reloadGrid");
        $('.clear-biz-filter').hide();
    });
    $('.select-email-trash').live('click', function (e) {
        if ($(this).hasClass('btn-default')) {
            $(this).removeClass('btn-default').addClass('btn-primary');
            jQuery("#jsonmap").jqGrid('setGridParam', {postData: {is_deleted: 1}}).trigger("reloadGrid");
        } else {
            $(this).removeClass('btn-primary').addClass('btn-default');
            jQuery("#jsonmap").jqGrid('setGridParam', {postData: {is_deleted: 0}}).trigger("reloadGrid");
        }
    });
});
var bind_menu_biz_delete = function (biz_id, group_id) {
    $.ajax({
        url: '/biz/deletemenubizs',
        data: {
            group_id: group_id,
            biz_id: biz_id
        },
        success: function (json) {
            //jQuery("#jsonmap").jqGrid('setGridParam', {postData: {biz_id: json.biz_ids}}).trigger("reloadGrid");
            $('.biz-menu-place').html(json.html_menu);
            $('.find-biz-group-select-place').html(json.html_menu_group_select);
            $('.find-biz-group-link[data-group-id="' + group_id + '"]').trigger('click');
        }
    });
}
var bind_group_biz_delete = function (group_id) {
    $.ajax({
        url: '/biz/deletegroupbizs',
        data: {
            group_id: group_id
        },
        success: function (json) {
            //jQuery("#jsonmap").jqGrid('setGridParam', {postData: {biz_id: json.biz_ids}}).trigger("reloadGrid");
            $('.biz-menu-place').html(json.html_menu);
            $('.find-biz-group-select-place').html(json.html_menu_group_select);
        }
    });
}

var bind_group_biz_select = function (group_id) {
    $.ajax({
        url: '/biz/getgroupbizs',
        data: {
            group_id: group_id
        },
        success: function (json) {
            jQuery("#jsonmap").jqGrid('setGridParam', {postData: {biz_id: json.biz_ids}}).trigger("reloadGrid");
        }
    });
};

/**
 * @version 20141019 SG
 * Передает на сервер id группы, название новой группы и строку с id бизов для сохранения
 */
var bind_save_biz_menu = function (group_id, group_title, biz_ids) {
    $.ajax({
        url: '/biz/savemenu',
        data: {
            group_id: group_id,
            group_title: group_title,
            biz_ids: biz_ids
        },
        success: function (json) {
            $('.biz-menu-place').html(json.html_menu);
            $('.find-biz-group-select-place').html(json.html_menu_group_select);
            $('.find-biz-new-group-title').val('');
            $('#myModalSearchBiz').modal('hide');
            $('.find-biz-manage-group').hide('fast');
            $('.find-biz-search-form').show();
            $('.find-biz-find-btn-row').show();
            $('.find-biz-search-result-row').show();
            $('.find-biz-add-footer').hide();
            $('.find-biz-search-footer').show();
            //find_biz_ids = "";            
            console.log(json);
        }
    });
}

/**
 * @version 20141016 SG
 * Populates the drop down list of products
 */
var bind_products = function (team_id, in_biz)
{
    if (team_id > 0)
    {
        $('.find-biz-product').prepend($('<option selected="" value="0">Loading...</option>'));
        error = false;

        $.ajax({
            url: '/team/getproducts',
            data: {
                team_id: team_id,
                full_branch: true,
                in_biz: true
            },
            success: function (json) {
                if (json.result == 'okay')
                {
                    console.log(json);

                    $('.find-biz-product').empty();
                    /*
                     $(id).prepend($('<option value="' + first_option.value + '">' + first_option.name + '</option>'));
                     
                     start_index = 0;
                     for (i = start_index; i < json_arr.length; i++)
                     {
                     el = json_arr[i];
                     $(id).append($('<option value="' + el.id + '">' + el.name + '</option>'));
                     }  
                     */

                    //fill_select("#products", json.products, {'value' : 0, 'name' : "--"});
                    fill_select(".find-biz-product", json.products, {'value': 0, 'name': "--"});
                    $(".chosen-select").trigger("chosen:updated");
                }
                else
                {
                    error = true;
                }
            }
        });
    }

    if (team_id == 0 || error)
    {
        $('#products').empty();
        $('#products').prepend($('<option value="0">--</option>'));
    }
};

/**
 * @version 20141016, SG
 */
var company_list = function (element) {

    $(".supinv_company").autocomplete({
        source: function (request, response) {

            element.next().val(0);

            $.ajax({
                url: "/company/getlistbytitle",
                data: {
                    maxrows: 6,
                    title: request.term
                },
                success: function (data) {
                    response($.map(data.list, function (item) {
                        return {
                            label: item.company.title,
                            value: item.company.id
                        }
                    }));
                }
            });

        },
        minLength: 3,
        select: function (event, ui) {

            if (ui.item)
            {
                element.val(ui.item.label);
                $('.find-biz-company-id').val(ui.item.value);
            }
            else
            {
                $('.find-biz-company-id').val(0);
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
}

var find_biz_for_nav = function () {
    var objective_id = $('.find-biz-objective').val();
    var team_id = $('.find-biz-team').val();
    var product_id = $('.find-biz-product').val();
    var status = $('.find-biz-status').val();
    var market_id = $('.find-biz-market').val();
    var driver_id = $('.find-biz-user').val();
    var keyword = $('.find-biz-keyword').val();
    var company_id = $('.find-biz-company-id').val();

    $.ajax({
        url: "/biz/search",
        data: {
            objective_id: objective_id,
            team_id: team_id,
            product_id: product_id,
            status: status,
            market_id: market_id,
            driver_id: driver_id,
            keyword: keyword,
            company_id: company_id
        },
        success: function (json) {
            console.log(json)
            $('.find-biz-search-result').html(json.html);
        }
    });
    //var role_id = ;    
};