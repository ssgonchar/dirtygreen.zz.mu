//onload
$(function() 
{
    $('.datepickers').datepicker({
        showWeek    : true,
	minDate     : 0,    //минимальная дата - текущая
        dateFormat  : 'yy-mm-dd'
    });
    
    $('.manual-datepickers').datepicker({
        showWeek    : true,
        dateFormat  : 'yy-mm-dd'
    });
    
    //Подсчитывает количество аттачей в письме, количество получателей(добавленных из системы) и выводит их
    attachments_counter();
    recipients_counter();
    
    //обработчик кнопки HTML preview
    $('#show-parent-email').live('click', function()
    {
        if($(this).text() == 'Show parent email'){
            $(this).text('Hide parent email'); 
        }
        else{
            $(this).text('Show parent email');
        }
        $('#parent-email-block').toggle(); 
    });
    
    //обработчик клика по input approve deadline
    $('#approve-deadline-input').live('click', function()
    {
        $('#ui-datepicker-div').css('z-index', 55555);
    });
    
    //обработчик изменения выпадающего списка approve by
    if($('#modal-approve #approve-by-select').val() !== '0') {
        $('#approve-deadline-input').removeAttr('disabled');
    }
    $('#modal-approve #approve-by-select').live("change", function()
    {
        if($(this).val() !== '0'){
            $('#approve-deadline-input').removeAttr('disabled');
        }
        else{
            $('#approve-deadline-input').attr('disabled', 'true').val('');
        }
    });
    
    //обработчик нажатия кнопки Save DFA в модальном окне Approve options
    $('#save-dfa').live('click', function()
    {
        if(($('#modal-approve #approve-by-select').val() == '0' && $('#modal-approve #approve-deadline-input').val() == '') || ($('#modal-approve #approve-by-select').val() !== '0' && $('#modal-approve #approve-deadline-input').val() !== '')){
            document.getElementById('mainform').submit();
        }
        else {
            alert('If I want to email has been approved - both options must be specified!');
        }
    });
    
    //меняет паддинги в заголовке "Attachments"
    $('.panel-heading').css('padding-top', '5px').css('padding-bottom', '5px');
    
    //скрывает форму поиска в модальном окне поиска email адресов
    if($('.row-content table').length > 0){
        $('.search-tools-div').hide();
    }
    
    //обработчик удаления аттача, добавленного из регистра в окне редактирования письма (удаляет span из списка и id из сессии)
    $('#remove-shared-doc').live('click', function()
    {
        var attach = $(this).parent();
        remove_shared_doc(attach);
        attachments_counter();
    });
    
    //обработчик удаления получателя, добавленного из регистра в окне редактирования письма (удаляет li из списка и id из БД)
    $('#remove-recipient').live('click', function()
    {
        var recipient = $(this).parent();
        var recipient_full_adress = $(this).prev().text();
        //вырезаю адрес из общей строки
        from = $(this).prev().text().indexOf('<') + 1;  
        var recipient_adress = $(this).prev().text().slice(from, -1); 
        
        remove_recipient(recipient, recipient_full_adress, recipient_adress);
        recipients_counter();
    });
    
    //обработчик нажатия кнопки Find в модальном окне поиска адресов 
    $('#find_company').live('click', function()
    {
        find_company();
    });
    
    //обработчик нажатия кнопки Apply в модальном окне поиска компании
    $('#add_company').live('click', function()
    {
        find_emails();
        recipients_counter();
        $('#filters').modal('hide');
    });
    
    //выбор чекбокса при клике по элементу списка (поиск email адресов)
    $('#emails-search-modal-body li').live('click', function()
    {
        var checkbox = $(this).find('input');
        if(typeof(checkbox.attr('checked')) == 'undefined') {
            checkbox.attr('checked', 'checked')
        }
        else{
            checkbox.removeAttr('checked');
        }
    });
    
    //останавливаю "всплытие" события нажатия чекбокса
    $('#emails-search-modal-body li input').live('click', function(e)
    {
        e.stopPropagation();
    });
    
    //выбор чекбокса при клике по элементу списка (поиск документов)
    $('#shared-docs-modal-body nobr').live('click', function()
    {
        var checkbox = $(this).find('input');
        if(typeof(checkbox.attr('checked')) == 'undefined') {
            checkbox.attr('checked', 'checked')
        }
        else{
            checkbox.removeAttr('checked');
        }
    });
    
    //останавливаю "всплытие" события нажатия чекбокса
    $('#shared-docs-modal-body nobr input').live('click', function(e)
    {
        e.stopPropagation();
    })
    
    //обработчик нажатия кнопки Select all в модальном окне поиска компании
    $('#emails-search-modal-body .select-all').live('click', function()
    {
        $('#emails-search-modal-body input[type="checkbox"]').attr('checked', true);
    });
    
    //обработчик нажатия кнопки Unselect all в модальном окне поиска компании
    $('#emails-search-modal-body .unselect-all').live('click', function()
    {
        $('#emails-search-modal-body input[type="checkbox"]').attr('checked', false);
    });
    
    //обработчик нажатия кнопки Select all в модальном окне поиска документов
    $('#shared-docs-modal-body .select-all').live('click', function()
    {
        $('#shared-docs-modal-body input[type="checkbox"]').attr('checked', true);
    });
    
    //обработчик нажатия кнопки Unselect all в модальном окне поиска документов
    $('#shared-docs-modal-body .unselect-all').live('click', function()
    {
        $('#shared-docs-modal-body input[type="checkbox"]').attr('checked', false);
    });
        
    //обработчик нажатия кнопки Apply в модальном окне поиска Shared docs
    $('#add-doc').live('click', function()
    {
        get_shared_docs_links();
        $('#shared-docs-modal').modal('hide');
        attachments_counter();
    });
    
    //авто-поиск BIZ по id 
    bind_biz_autocomplete_su('#task-biz', empty_function);
    
    //обработчик нажатия кнопки "Find" при поиске документов по RA
    $('#find-ra-files').live('click', function()
    {
        var id = $('#shared-docs-modal #ra').val();
        var alias = 'ra';
        var text = $('#shared-docs-modal #ra').val();
        get_shared_docs(id, alias, text);
    });
    
    //обработчик нажатия кнопки "Find" при поиске документов по Order
    $('#find-order-files').live('click', function()
    {
        var id = $('#shared-docs-modal #order').val();
        var alias = 'order';
        var text = $('#shared-docs-modal #order').val();
        get_shared_docs(id, alias, text);
    });
    
    //обработчик изменения выпадающего списка в модальном окне shared docs (переключаем по какому параметру будем искать)
    $('#docs-select-from').live('change', function()
    {
        var alias = $(this).val();
        $('#shared-docs-modal-body input.shared-files-input').hide().val('');   //прячу инпуты
        $('#shared-docs-modal-body .find-btn').hide();  //прячу кнопки find
        $('#'+alias+'').show();                         //показываю инпут для выбранного елемента списка
        //желательно заменить на switch/case
        if(alias == 'ra'){
            $('#shared-docs-modal-body #find-ra-files').show();
        } 
        if(alias == 'order'){
            $('#shared-docs-modal-body #find-order-files').show();
        }
    });
    
    //обработчик изменения выпадающего списка в модальном окне Get emails (переключаем по какому параметру будем искать)
    $('#emails-select-from').live('change', function()
    {
        //обнуляю значение скрытого input хранящего company_id
        $('#emails-search-modal-body .emails-company-id').val('');
        var alias = $(this).val();
        $('#emails-search-modal-body input.emails-search-input').hide().val('');
        $('#'+alias+'').show();
        //прячу кнопку "Find" при поиске персоны
        if(alias !== 'emails-company') $('#find_company').hide();
        else $('#find_company').show();
    });
            
    //Обработчик клика по input модального окна поиска shared docs
    $('.shared-files-input').live('click', function()
    {
        $(this).select();
    });
            
    //Обработчик клика по input модального окна поиска emails
    $('.emails-search-input').live('click', function()
    {
        $(this).select();
    });
    
    //открывает модально окно выбора документов из регистра
    $('#add-shared-docs').live('click', function()
    {
        $('#shared-docs-modal').modal('show');
    });
        
    //открывает модально окно выбора адресов
    $('#add-emails').live('click', function()
    {
        $('#filters').modal('show');
    });
    
    //autocomplete для поиска email адресов по персонажу.
    $(".email-person").autocomplete({
        source: function(request, response) {

            keyword = request.term;

            if (keyword.indexOf(",") != -1)
            {
                keyword = keyword.replace(/.*,/gi, "");
            }

            if (keyword.indexOf("\"") != -1 || keyword.indexOf("<") != -1 || keyword.indexOf(">") != -1)
                return;


            $.ajax({
                url: "/email/getrecipients",
                data: {
                    maxrows: 250,
                    keyword: keyword
                },
                success: function(data) {
                    response($.map(data.list, function(item) {
                        return {
                            label: item.title,
                            value: item.id,
                            company: (item.company ? item.company.title : ''),
                            person: (item.person ? item.person.full_name : '')
                        }
                    }));
                }
            });

        },
        minLength: 3,
        select: function(event, ui) {

            if (ui.item)
            {
                text = $(this).val();

                if (text.indexOf(",") == -1)
                {
                    text = ui.item.label + ', ';

                    if ($(this).attr('id') == 'emails-person')  //для добавления адреса из контакта
                    {
                        //из результата выбираю email adress
                        var from = ui.item.label.indexOf('<') + 1;
                        var person_email = ui.item.label.slice(from, -1);

                        //запрос в ajax контроллер для формирования массива и заполнения таблицы
                        $.ajax({
                            url: "/emailmanager/getrecipient",
                            data: {
                                person_name  : ui.item.person,
                                company_name : ui.item.company,
                                person_email : person_email
                            },
                            success: function(json) {
                                if(json.result == 'okay'){
                                    //if($('.sr-items').length > 0)  $('.sr-items').remove();
                                    $('.row-content').append(json.companies_list);
                                    $('.company-name').last().text('You checked: '+ui.item.label+'');
                                    console.log($('#emails-search-modal-body span.email-search-keyword').last());
                                    $('#emails-search-modal-body span.email-search-keyword').last().text(ui.item.label);
                                    if($('#emails-search-modal-body .select-all, #emails-search-modal-body .unselect-all').length > 2){
                                        $('#emails-search-modal-body .unselect-all').last().hide();
                                        $('#emails-search-modal-body .select-all').last().hide();
                                    }
                                }
                            }
                        });
                    }
                }
                else
                {
                    subtext = text.substr(text.lastIndexOf(','));
                    text = text.replace(subtext, ', ' + ui.item.label + ', ');
                }
            }

            return false;

        },
        open: function() {
            if ($('.ui-autocomplete > li').length > 20){
                $(this).autocomplete('widget').css('height', '200px').css('overflow-y', 'scroll');
            }
            $('ul.ui-autocomplete ').css('z-index', '5555555');
        },
        close: function() {

        },
        focus: function(event, ui) {

            return false;
        }
    });
});


/**
 * Назначает элементу вода бизнеса с классом biz-autocomplete функцию автозаполнения
 * для работы должно быть два поля: {text id="biz-title" class="biz-autocomplete"} и {hidden id="biz-title-id"}
 * у hidden поля должен быть id такой же как у text с суффиксом "-id"
 * 
 * @version 20120722, zharkov
 * @version 20141030, Uskov Sergey (при выборе результата автокомплита для него ищются вложенные документы)
 */
var bind_biz_autocomplete_su = function(biz_selector, callback_function)
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
                //автоматический поиск shared docs
                var id = ui.item.value;
                var text = ui.item.label;
                var alias = 'task-biz';
                get_shared_docs(id, alias, text);
                
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

var empty_function = function(){};

/**
 * Передает данные из формы поиска Shared docs в ajax контроллер. Выводит список найденных документов.
 * 
 * @param {string} id
 * @param {string} alias
 * @param {string} text
 * 
 * @author SU
 */
var get_shared_docs = function(id, alias, text)
{
    if(id !== '0' && alias !== ''){
        //если файлы по текущему запросу еще не были найдены
        if($('h3:contains('+text+')').length < 1){
            $.ajax({
                url: '/emailmanager/getshareddocs',
                data : {
                    id    : id,
                    alias : alias
                },
                success: function(json){
                    if (json.result == 'okay'){
                        if($('.shared-files-div span.no-uploaded-files').length > 0) $('.shared-files-div').text('');
                        //меняю шапку для результата в зависимости от источника поиска
                        text = (json.alias == 'ra' ? 'RA '+text+'' : text);
                        text = (json.alias == 'order' ? 'Order '+text+'' : text);
                        
                        $('.shared-files-div').append(json.attachments_list)
                                              .find('h3').last().html(text+'<span> :</span>')
                                              .css('font-size', '1.2em').css('text-decoration', 'underline');
                        //
                        if($('.shared-files-div h3').length == 1){
                            $('.shared-files-div h3').append('<span class="select-all btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-ok"></i></span><span class="unselect-all btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-remove"></i></span>');
                        }
                    }
                    if (json.result == 'error'){
                        Message(json.code, 'warning');
                    }
                }
            });  
        }else{
            Message('Files for "'+text+'" was already found!', 'warning');
        }
    }
};

/**
 * get_shared_docs_links
 * 
 * Передает данные в ajax контроллер  для сохранения в сессию строки со ссылками на выбраные shared docs
 * 
 * @author SU
 */        
var get_shared_docs_links = function()
{
    var doc_links_arr           = new Array();
    var doc_ids_arr             = new Array();
    var not_checked_doc_ids_arr = new Array();
    //прохожу циклом выбранные чекбоксы
    $("#shared-docs-modal input:checked").each(function()
    {
        //ищу ссылку на выбранный документ и записываю в массив ссылок
        var checked_doc_link = $(this).parent().find('a.attachment-pdf').attr('href');
        doc_links_arr.push(checked_doc_link);
        //ищу id выбранного документа и записываю в массив
        var checked_doc_id = $(this).parent().data('id');
        doc_ids_arr.push(checked_doc_id);
    });
    //прохожу циклом невыбранные чекбоксы для того чтобы удалить из сессии документы, которые не выбраны
    $("#shared-docs-modal input:checkbox:not(:checked)").each(function()
    {
        //ищу id документов которые не выбраны
        var checked_doc_id = $(this).parent().data('id');
        not_checked_doc_ids_arr.push(checked_doc_id);
    });
    //удаляю из списка на странице радактирования документы, которые не выбраны
    for (var i = 0; i < not_checked_doc_ids_arr.length; i++) {
        $('span#attachment-'+not_checked_doc_ids_arr[i]+'').remove();
    }
    //сохраняю массив в сессию и вывожу список аттачей на страницу редактирования письма.
    var uploader_object_alias = $('#uploader_object_alias').val();
    var uploader_object_id = $('#uploader_object_id').val();
    $.ajax({
        url: "/emailmanager/saveshareddocs",
        data: {
            attached_ids          : doc_ids_arr,
            not_attached_ids      : not_checked_doc_ids_arr,
            uploader_object_alias : uploader_object_alias,
            uploader_object_id    : uploader_object_id
        },
        success: function(json){
            $('#attachments').append(json.content);
        }
    });
};

/**
 * find_company
 * 
 * Отправляет данные формы поиска компаний в ajax контроллер. Выводит список найденных компаний.
 * 
 * @author SU
 */
var find_company = function()
{
    var keyword = $('#emails-company').val();
    $.ajax({
        url     : '/emailmanager/findcompany',
        data    : {
            keyword         : $('#emails-company').val(),
            company_id      : $('#emails-search-modal-body .emails-company-id').val(),
            country_id      : $('#country').val(),
            region_id       : $('#region').val(),
            city_id         : $('#city').val(),
            industry_id     : $('#industry').val(),
            activity_id     : $('#activity').val(),
            pspeciality_id  : $('#speciality').val(),
            product_id      : $('#product').val(),
            feedstock_id    : $('#feedstock').val(),
            relation_id     : $('#relation').val(),
            status_id       : $('#status').val()
        },
        success: function(json){
            if(json.result == 'error'){
                Message(json.code, 'warning');
            }
            if(json.result == 'okay'){
                if($('#emails-search-modal-body span.email-search-keyword:contains('+keyword+')').length > 0){
                    Message('Emails for "'+keyword+'" was already found!', 'warning');
                }else{
                    //if($('.sr-items').length > 0)  $('.sr-items').remove();
                    $('.row-content').append(json.companies_list);
                    $('.company-name').last().text('You entered: "'+keyword+'"');
                    $('#emails-search-modal-body span.email-search-keyword').last().text(keyword);
                    if($('#emails-search-modal-body .select-all, #emails-search-modal-body .unselect-all').length > 2){
                        $('#emails-search-modal-body .unselect-all').last().hide();
                        $('#emails-search-modal-body .select-all').last().hide();
                    }
                }
            }
            //обнуляю значение скрытого input хранящего company_id
            $('#emails-search-modal-body .emails-company-id').val('');
        }
    });
};

/**
 * Получает список компаний для строки поиска компаний
 * @version 20130130, zharkov
 * @version 20130515, sasha
 */
var company_autocomplete_for_emails_search = function(element){
  
    $(".emails-company").autocomplete({
        source: function( request, response ) {
            
             element.next().val(0);
            
            $.ajax({
                url     : "/company/getlistbytitle",
                data    : {
                    maxrows : 6,
                    title   : request.term
                },
                success : function( data ) {
                    response( $.map( data.list, function( item ) {
                        return {
                            label: item.company.title,
                            value: item.company.id
                        }
                    }));
                }
            });
            
        },
        minLength: 3,
        select: function( event, ui ) {
            
            if (ui.item)
            {
		element.val(ui.item.label);
                element.next().val(ui.item.value);
            }
            else
            {
                element.next().val(0);
            }
            $(this).autocomplete('widget').css('display', 'none');
            return false;
            
        },
        open: function() {
            if ($('.ui-autocomplete > li').length > 20)
            {
                $(this).autocomplete('widget').css('z-index', 55555).css('height', '200px').css('overflow-y', 'scroll');
            }
            else
            {
                $(this).autocomplete('widget').css('z-index', 55555);
            }

            return false;
        },
        close: function() { },
        focus: function(event, ui) 
        { 
            return false;
        }        
    });
}

/**
 * Формирует массивы из выбранных и невыбранных адресов (email_adress => company_id) и передает его в ajax контроллер.
 * 
 * @author SU
 */
var find_emails = function()
{
    var object_alias = $('#uploader_object_alias').val();
    var object_id = $('#uploader_object_id').val();
    
    var checked_emails   = new Object();
    var unchecked_emails = new Object();
    //для БД приходится экранировать "<", поэтому для удаления со страницы редактирования в этот массив записываю только адреса
    var unchecked_emails_for_edit = new Object();
    
    //проверяю все выбранные чекбоксы
    $("#emails-search-modal-body input:checked").each(function()
    {
        //ищу адреса компаний в Company Contacts
        var li = $(this).parent();
        var email_adress = li.find('span.email-adress').text();
        var company_id   = li.find('span.company-id').text();
        //var company_name = $(this).parent().parent().parent().prev().find('a').text();
        var company_name = $.trim($(this).parents('td').prev().find('a').text());
        
        //получаю название списка, по нему определяю какого типа контакт. Рзделитель - ":"
        var ul = $(this).parent().parent();
        two_points = ul.text().indexOf(':');        
        name = ul.text().slice(0, two_points);     
        
        //формирую массив из выбранных адресов в формате gmail
        if(name !== "Company emails") {
            adress = '"'+name+'" &lt;' +email_adress+'&gt;';
            checked_emails[adress] = company_id;
        }
        else{
            adress = '"'+company_name+'" &lt;' +email_adress+'&gt;';
            checked_emails[adress] = company_id;
        }
    });
    //проверяю все невыбранные чекбоксы
    $("#emails-search-modal-body input:checkbox:not(:checked)").each(function()
    {
        var li = $(this).parent();
        
        var email_adress = li.find('span.email-adress').text();
        var company_id   = li.find('span.company-id').text();
        //формирую массив из невыбранных адресов ДЛЯ УДАЛЕНИЯ СО ТРАНИЦЫ РЕДАКТИРОВАНИЯ
        unchecked_emails_for_edit[email_adress] = company_id;
        
        //ищу адреса компаний в Company Contacts
        var li = $(this).parent();
        var email_adress = li.find('span.email-adress').text();
        var company_id   = li.find('span.company-id').text();
        var company_name = $.trim($(this).parents('td').prev().find('a').text());
        
        //получаю название списка, по нему определяю какого типа контакт. Рзделитель - ":"
        var ul = $(this).parent().parent();
        two_points = ul.text().indexOf(':');        
        name = ul.text().slice(0, two_points);     
        
        //формирую массив из невыбранных адресов ДЛЯ УДАЛЕНИЯ ИЗ БД
        //формирую массив из выбранных адресов в формате gmail
        if(name !== "Company emails") {
            adress = '"'+name+'" &lt;' +email_adress+'&gt;';
            unchecked_emails[adress] = company_id;
        }
        else{
            adress = '"'+company_name+'" &lt;' +email_adress+'&gt;';
            unchecked_emails[adress] = company_id;
        }
    });
    
    //удаляю из списка на странице радактирования документы, которые не выбраны 
    for (var val in unchecked_emails_for_edit) {
        $('#emails-from-system span:contains('+val+')').parent().remove();
    }
    
    $.ajax({
        url     : "/emailmanager/saverecipients",
        data    : {
            checked_emails   : checked_emails,
            unchecked_emails : unchecked_emails,
            object_alias     : object_alias,
            object_id        : object_id
        },
        success: function(json){
            $('#emails-from-system').append(json.content);
        }
    });
};

/**
 * Удаляет получателя (добавленного из системы) со страницы и из БД, снимаю чекбокс у данного получателя в модальном окне !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * 
 * @param {object} recipient элемент i (иконка удаления)
 * @param {string} recipient_adress  удаляемый адресс получателя
 * 
 * @author Sergey Uskov <archimba8578@gmail.com>
 */
var remove_recipient = function(recipient, recipient_full_adress, recipient_adress)
{
    var uploader_object_alias = $('#uploader_object_alias').val();
    var uploader_object_id    = $('#uploader_object_id').val();
    //необходимо заменить "<" и ">" на "&lt;" и "&gt;" для нахождения адреса в БД
    recipient_full_adress = recipient_full_adress.replace('<','&lt;');
    recipient_full_adress = recipient_full_adress.replace('>','&gt;');
    
    if (confirm("Remove recipient?")){
        $.ajax({
            url     : '/emailmanager/removerecipient',
            data    : {
                recipient_adress      : recipient_full_adress,
                uploader_object_alias : uploader_object_alias,
                uploader_object_id    : uploader_object_id
            },
            success : function(json){
                if(json.result == 'okay'){
                    //снимаю чекбокс у данного получателя в модальном окне
                    $('#emails-search-modal-body ul span.email-adress:contains('+recipient_adress+')').next().removeAttr('checked');
                    //удаляю получателя из списка в окне редактирования письма
                    recipient.remove();
                }
            }
        });
    }
};

/**
 * 
 
var html_preview = function()
{
    var description = tinyMCE.get('email_text').getContent();
    $('#html-view-block').text(description);
    //alert(description);
    $('#description-block').toggle();
    $('#html-view-block').toggle();
    
};*/