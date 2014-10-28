
//document.ready function
$(function(){
    $('.datepickers').datepicker({
        showWeek    : true,
	minDate     : 0,
        dateFormat  : 'yy-mm-dd'
    });
    
    $('.manual-datepickers').datepicker({
        showWeek    : true,
        dateFormat  : 'yy-mm-dd'
    });
    
    //Переключатель on / off
    $('.switch').css('background', 'url("css/switch.png") 100% 50%');
    $('.on_off').css('display','none');
    $('.on, .off').css('text-indent','-10000px');
    //обработчик события переключателя
    $("input[name=on_off]").change(function() {
        var button = $(this).val();
        
        if(button == 'off'){ 
            $('.switch').css('background-position', 'right');
            var task_id = $('#task-id').val();
            if(task_id !== ''){
                console.log('Automatic counting: OFF');
                //при выключении счетчика заданию присваиваю status_id = 2
                var status_id = 2;
                change_status(task_id, status_id);
                $('#manual-start-data').removeAttr('disabled');
                $('#manual-start-time').removeAttr('disabled');
                $('#manual-finish-data').removeAttr('disabled');
                $('#manual-finish-time').removeAttr('disabled');
                $('#manual-save-time').removeAttr('disabled');
                $('#select-change-status').removeAttr('disabled');
                $('#task-id').removeAttr('disabled');

                $('#del-task-button').removeAttr('disabled');
                $('#upd-task-button').removeAttr('disabled');
                $('#auto-used-time').text('--');
            }
        }
        if(button == 'on'){ 
            $('.switch').css('background-position', 'left');
            var task_id = $('#task-id').val();
            var used_time = $("#task-"+task_id+"").parent().find('.td-used-time nobr').text();
            if(task_id !== ''){
                console.log("Automatic counting: ON");
                //при включении счетчика заданию присваиваю status_id = 4
                var status_id = 4;
                change_status(task_id, status_id);
                $('#manual-start-data').attr('disabled', 'true');
                $('#manual-start-time').attr('disabled', 'true');
                $('#manual-finish-data').attr('disabled', 'true');
                $('#manual-finish-time').attr('disabled', 'true');
                $('#manual-save-time').attr('disabled', 'true');
                $('#select-change-status').attr('disabled', 'true');
                $('#task-id').attr('disabled', 'true');
                
                $('#del-task-button').attr('disabled', 'true');
                $('#upd-task-button').attr('disabled', 'true');
                $('.td-task-id').tooltip('hide');
                //показываю used_time
                $('#auto-used-time').text(used_time);
            }
        }
                
         $('.result span').html(button); 
         $('.result').fadeIn();
    });
   
   //активация / дезактивация кнопок управления заданием при изменении в поле ввода task id
   $('#task-id').live('keyup', function()
   {
       if($(this).val() !== ''){
            $('#del-task-button').removeAttr('disabled');
            $('#upd-task-button').removeAttr('disabled');
       }else{
            $('#del-task-button').attr('disabled', 'true');
            $('#upd-task-button').attr('disabled', 'true');
       }
   });
   
    //устанавливаю z-index для datepicker, иначе его не видно за модальным окном
    $('.datepickers').live('click', function()
    {
        $('#ui-datepicker-div').css('z-index', '99999');
    });
    
    //Добавление нового задания
    $('#add-task-button').live('click', function()
    {
        add_task();
    });
    
    //Нажатие кнопки сохранения used time вручную
    $('#manual-save-time').live('click', function()
    {
        manual_save_used_time();
    });
     
    //Tooltip
    $('.td-task-id').tooltip();
    $('.td-user-name').tooltip();
    $('#th-status').tooltip();
    $('#task-id').tooltip();
    
    //Добавление id задания в окно task id
    $('.td-task-id').live('click', function()
    {
        if($('#task-id').attr('disabled') !== 'disabled'){
            var task_id = $(this).text();
            $('#task-id').val(task_id);
            //активирую кнопки Delete и Update
            $('#del-task-button').removeAttr('disabled');
            $('#upd-task-button').removeAttr('disabled');
            $('#change-status-button').removeAttr('disabled');
        }
        
    });
    
    //Обработчик нажатия кнопки Save в модальном окне
    $('#save-button').live('click', function()
    {
        save_task();
    });
    
    //Расчитует запланированное время для каждой строки таблицы
    each_tr();
    
    //Обработчик кнопки Del
    $('#del-task-button').live('click', function()
    {
        var task_id = $('#task-id').val();
        if (confirm("Are you sure you want to delete this task?")) {
            delete_task(task_id);
        }
    });
    
    //Обработчик кнопки Upd
    $('#upd-task-button').live('click', function()
    {
        var task_id = $('#task-id').val();
        var user_id = $('#hidden-user-id').text();
        update_task(task_id, user_id);
    });
    
    //Обработчик события Change status
    $('#select-change-status').live('change', function()
    {
        var task_id = $('#task-id').val();
        var user_id = $('#hidden-user-id').text();
        var status_id = $(this).val();
        if(task_id !== ''){
            change_status(task_id, status_id);
        }else{
            Message('I forgot to specify Task ID!', 'warning');
        }
        $("#select-change-status :first").attr("selected", "selected");
    });
    
    //переключение заданий по статусу
    $('.info').hide();
    $('#th-status').live('click', function()
    {
        view_by_status();
    });
    
    //подключаю текстовый HTML редактор tinyMCE
    tinymce_init();
    
    //закрытие модального окна
    $('#my-modal').live('hidden.bs.modal', function (event) 
    {
        //меняю обратно заголовок на New (необходимо, если до этого было редактирование)
        $('.modal-title').text('New task');
        //необходимо обнулить все инпуты
        $('#my-modal textarea').val('');
        $('#my-modal input').val('');
        //обнуляю редакторы
        tinyMCE.get('task-definition').setContent('');
        tinyMCE.get('personal-notes').setContent('');
    });
    
    //после выбора даты фокусирую на выбор времени
    $('#manual-start-data, #manual-finish-data, #start-data, #finish-data').live('change', function()
    {
        $(this).next().focus();
    });
    
    //авто-поиск BIZ по id
    bind_biz_autocomplete('#task-biz', empty_function);
    
    //прячу общий заголовок окна (тот где page_name)
    $('div#heading').hide();
    
    //обработчик клика по картинке юзера
    $( ".users-container > img" ).live("click", function() 
    {
        //получаю user_id из названия картинок юзеров
        var user_id   = parseInt($(this).attr('id'));
        //получаю из контроллера при открытии страницы
        var my_user_id = parseInt($('#my-user-id').text());
        var user_name = ' - '+$(this).next().next().text();
        get_user_task_list(user_id, user_name);
        //обнуляю поле task_id если он не disabled (т.е. не включен авто счетчик)
        if($('#task-id').attr('disabled') !== 'disabled'){
            $('#task-id').val('');
        }
        //при выборе других юзеров деактивирую инструменты управления временем
        if(user_id !== my_user_id){
            console.log(user_id);
            console.log(my_user_id);
            $('#manual-start-data').attr('disabled', 'true');
            $('#manual-start-time').attr('disabled', 'true');
            $('#manual-finish-data').attr('disabled', 'true');
            $('#manual-finish-time').attr('disabled', 'true');
            $('#manual-save-time').attr('disabled', 'true');
            $('#select-change-status').attr('disabled', 'true');
            $('.on_off').attr('disabled', 'true');
        }
        //при выборе себя активирую счетчик
        else if(user_id == my_user_id) {
            $('.on_off').removeAttr('disabled');
            //если счетчик не был включен активирую инструменты управления временем
            if($('#task-id').attr('disabled') !== 'disabled'){
                $('#manual-start-data').removeAttr('disabled');
                $('#manual-start-time').removeAttr('disabled');
                $('#manual-finish-data').removeAttr('disabled');
                $('#manual-finish-time').removeAttr('disabled');
                $('#manual-save-time').removeAttr('disabled');
                $('#select-change-status').removeAttr('disabled');
            }
        }
        if($('#users-active-tasks-tbody').length > 0) $('#users-active-tasks-tbody').remove();
        $('#active-tasks-thead').hide();
        $('#organizer').show();
    });
    
    //кнопка Show active tasks
    $('#show-active-tasks').live('click', function()
    {
        get_users_active_tasks();
        //обнуляю поле task_id если он не disabled (т.е. не включен авто счетчик)
        if($('#task-id').attr('disabled') !== 'disabled'){
            $('#task-id').val('');
            $('#del-task-button').attr('disabled', 'true');
            $('#upd-task-button').attr('disabled', 'true');
        }
    });
    
    //кнопка My tasks
    $('#my-active-tasks').live('click', function()
    {
        if($('#users-active-tasks-tbody').length > 0) $('#users-active-tasks-tbody').remove();
        $('#active-tasks-thead').hide();
        $('#organizer').show();
        var my_user_id = parseInt($('#my-user-id').text());
        var user_name = '';
        get_user_task_list(my_user_id, user_name);
        //обнуляю поле task_id если он не disabled (т.е. не включен авто счетчик)
        if($('#task-id').attr('disabled') !== 'disabled'){
            $('#task-id').val('');
            $('#del-task-button').attr('disabled', 'true');
            $('#upd-task-button').attr('disabled', 'true');
        }
        
        $('.on_off').removeAttr('disabled');
        //если счетчик не был включен активирую инструменты управления временем
        if($('#task-id').attr('disabled') !== 'disabled'){
            $('#manual-start-data').removeAttr('disabled');
            $('#manual-start-time').removeAttr('disabled');
            $('#manual-finish-data').removeAttr('disabled');
            $('#manual-finish-time').removeAttr('disabled');
            $('#manual-save-time').removeAttr('disabled');
            $('#select-change-status').removeAttr('disabled');
        }
    });
    
    //
    $('.td-user-name').live('click', function()
    {
        $(this).find('span').toggle();
    });
});

//пустышка для авто-поиска BIZ по id (bind_biz_autocomplete в app.js) 
var empty_function = function(){};

/*
 * 
 */
var get_users_active_tasks = function()
{
    $.ajax({
        url     : "/iamido/getusersactivetasks",
        data    : {
            
        },
        type: 'POST',	              
        success: function(json)
        {
            if (json.result == 'okay'){
                //прячу основную таблицу
                $('#organizer').hide();
                //удаляю тело предыдущей таблицы если оно есть
                if($('#users-active-tasks-tbody').length > 0) $('#users-active-tasks-tbody').remove();
                //рисую тело таблицы
                $('#active-tasks-thead').show().after("<tbody id='users-active-tasks-tbody'>"+json.active_tasks_list+"</tbody>");
                each_tr();
            }
        }
    });
};

/*
 * Возвращает список заданий для указанного пользователя
 */
var get_user_task_list = function(user_id, user_name)
{
    //console.log(user_name);
    $.ajax({
        url     : "/iamido/getuserlist",
        data    : {
            user_id : user_id
        },
        type: 'POST',	              
        success: function(json)
        {
            if (json.result == 'okay'){
                $('#tasks-thead').next().remove();
                //tbody заполняется с помощью fetch
                $('#tasks-thead').after('<tbody>' +  json.user_task_list + '</tbody>');
                each_tr();
                $('.info').hide();
                $('#team-members-div > h4').text('Our team'+user_name);
                $('#hidden-user-id').text(user_id);
            }
            if (json.result == 'empty'){
                Message(user_name+' don`t have a tasks!', 'warning');
            }
        }
    });
};

/*
 * подключаю текстовый HTML редактор tinyMCE
 */
var tinymce_init = function()
{
    console.log('tinyMCE init');
    tinymce.init({
        selector : "textarea",
        inline: false,
        setup: function(editor) {
            //editor.on('GetContent', function(e) {
                //
            //});
        },
        plugins: [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste textcolor"
            ],
        toolbar: "insertfile undo redo | styleselect | fontselect | fontsizeselect | forecolor | backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | bold italic | link image"
    });
};

/*
 * Меняет статус задания
 * @param task_id
 */
var change_status = function(task_id, status_id)
{
    $.ajax({
        url     : "/iamido/changestatus",
        data    : {
            task_id   : task_id,
            status_id : status_id
        },
        type: 'POST',	              
        success: function(json)
        {
            if(json.result == 'okay'){
                //переменные для изменения параметров строки
                var updated_task_id = json.updated_task['param_task_id'];
                var updated_task_tr = $("#task-"+updated_task_id+"").parent();
                var tr_class;
                if(status_id == '1'){tr_class = 'active'}else if(status_id == '2' || status_id == '4'){tr_class = 'success'}else if(status_id == '3'){tr_class = 'info'}
                var task_status;
                if(status_id == '1'){task_status = 'Waiting'}else if(status_id == '2' || status_id == '4'){task_status = 'In process'}else if(status_id == '3'){task_status = 'Completed'}
                //Изменяю параметры строки
                updated_task_tr.removeAttr('class').addClass(tr_class).find('td.td-status-id').text(task_status);
                //если задание завершили - прячу строку
                if(status_id == '3'){
                    updated_task_tr.hide();
                }
                //если меняли статус на 'In process' и задание с таким статусом уже было в таблице мы меняем его статус на 'Waiting'
                //по полученному id меняем параметры измененной строки
                if(json.changed_task !== 'notask'){
                    //console.log(json.changed_task);
                    //переменные для изменения параметров строки
                    updated_task_id = json.changed_task;
                    updated_task_tr = $("#task-"+updated_task_id+"").parent();
                    tr_class = 'active';
                    task_status = 'Waiting';
                    //Изменяю параметры строки
                    updated_task_tr.removeAttr('class').addClass(tr_class).find('td.td-status-id').text(task_status);
                }
            }
            if(json.result == 'permissions'){
                Message(json.code, 'warning');
            }
            if(json.result == 'wrong_task'){
                Message(json.code, 'warning');
            }
            if(json.result == 'wrong_status'){
                Message(json.code, 'warning');
            }
        }
    });
};

/*
 * Прячет активные и ожидающие выполнения задания и показывает выполненные и наоборот.
 */
var view_by_status = function()
{
    $( ".info" ).toggle();
    $( ".active").toggle();
    $( ".success").toggle();
};

/* Сохраняет введенное вручную время выполнения задания в формате Timestamp (сек)
 * 
 * @param task_id
 */
var manual_save_used_time = function()
{
    //получаю данные из input`ов
    var task_id     = $('#task-id').val();
    if(task_id !== ''){
        var start_data  = $('#manual-start-data').val() + ' ' + $('#manual-start-time').val() + ':00';
        var finish_data = $('#manual-finish-data').val() + ' ' + $('#manual-finish-time').val() + ':00';
        $.ajax({
            url     : "/iamido/manualsaveusedtime",
            data    : {
                start_data  : start_data,
                finish_data : finish_data,
                task_id     : task_id
            },
            type: 'POST',	              
            success: function(json)
            {
                if(json.result == 'okay'){
                    var current_task_id = json.updated_task['param_task_id'];
                    var current_used_time = json.updated_task['current_used_time'];
                    //трансформирую использованное время из формата Timestamp в понятное для человеческого глаза (-d -h -m)
                    //теперь время в секундах - необходимо пересчитать
                    var used_time   = $(this).find('.td-used-time nobr').text();
                    var days        = Math.floor(current_used_time/86400);
                    var hours       = Math.floor((current_used_time - days*86400)/3600);
                    var minutes     = Math.floor((current_used_time - days*86400 - hours*3600)/60);
                    //обновляю время в таблице
                    $("#task-"+current_task_id+"").parent().find('.td-used-time nobr').text((days > 0 ? days+'d ' : '') + (hours > 0 ? hours+'h ' : '') + (minutes > 0 ? minutes+'m ' : ''));
                    //обнуляю input`ы
                    $('#manual-start-data').val('');
                    $('#manual-start-time').val('');
                    $('#manual-finish-data').val('');
                    $('#manual-finish-time').val('');
                }
                if(json.result == 'wrong_task'){
                    Message("You don`t have task with ID = "+task_id+"!", 'warning');
                }
                if(json.result == 'wrong_status'){
                    Message("You can`t update time of completed tasks!", 'warning');
                }
                if(json.result == 'wrong_time'){
                    Message("Date of finish time must be more then the date of start!", 'warning');
                }
                if(json.result == 'date_not_exist'){
                    Message("Please enter the dates correctly! (date and time)", 'warning');
                }
            }
        });
        
    }else{ Message('I forgot to specify Task ID!', 'warning'); }
};

/* Удаляет задание
 * 
 * @param task_id
 * @returns deleted_task
 */
var delete_task = function(task_id)
{
    $.ajax({
        url     : "/iamido/deletetask",
        data    : {
            task_id : task_id
        },
        type: 'POST',	              
        success: function(json)
        {
            if(json.result == 'okay'){
                //удаляю строку
                $("#task-"+json.deleted_task.id+"").parent().remove();
                Message("Task with #"+json.deleted_task.id+" was successfully deleted.", "okay");
            }
            if(json.result == 'error'){
                Message("You don`t have task with ID = "+task_id+"", "warning");
            }
            if(json.result == 'permissions'){
                Message(json.code, "warning");
            }
        }
    });
};

/* Возвращает инфо о задании
 * 
 * @param {type} task_id
 * @returns {undefined}
 * 
 */
var update_task = function(task_id, user_id)
{
    $.ajax({
        url     : "/iamido/updatetask",
        data    : {
            task_id : task_id,
            user_id : user_id
        },
        type: 'POST',	              
        success: function(json)
        {
            if(json.result == 'permissions'){
                Message(json.code, "warning");
            }
            if(json.result == 'wrong_task'){
                Message("You don`t have task with this id!", "warning");
            }
            if(json.result == 'wrong_status'){
                Message("You can`t edit complited tasks!", "warning");
            }
            if(json.result == 'okay'){
                //console.log(json.result); 
                //данные:
                var start               = json.current_task['data_start'].split(" "); //разделяю по пробелу на дату и время
                var start_data  = start['0'];
                var start_time  = start['1'].slice(0,5);
                
                var finish              = json.current_task['data_finish'].split(" "); //разделяю по пробелу на дату и время
                var finish_data = finish['0'];
                var finish_time = finish['1'].slice(0,5);
                var title               = json.current_task.task['title'];
                var description         = json.current_task.task['description'];
                var personal_notices    = json.current_task['personal_notices'];
                var status_id           = json.current_task['status_id'];
                var task_id             = json.current_task['task_id'];
                //var used_time           = json.current_task['used_time'];   //пока не используется
                var biz_id              = json.current_task.task['biz_id'];
                var biz_title           = json.current_task.task['biz_title'];
                
                //заполняю модальное окно
                $('#my-modal').modal();
                //$('.modal-title').text('Editing a task').append('<i class="pull-right hidden glyphicon glyphicon-floppy-saved" style="margin-right: 15px;"></i>');
                $('.modal-title').text('Editing task');   //меняю заголовок при редактировании
                $('#task-title').val(title);                //Title
                $('#task-definition').val(description);     //Definition
                $('#personal-notes').val(personal_notices); //Personal notes
                $('#hidden-task-id').val(task_id);          //скрытый input для хранения task_id
                $('#hidden-status-id').val(status_id);      //скрытый input для хранения status_id
                $('#start-data').val(start_data);           //дата начала задания
                $('#start-time').val(start_time);           //время начала задания
                $('#finish-data').val(finish_data);         //дата окончания задания
                $('#finish-time').val(finish_time);         //время окончания задания
                $('#task-biz-id').val(biz_id);              //скрытый input - хранит biz_id
                $('#task-biz').val(biz_title);              //название biz
                
                //обновляю содержимое обьекта tinymce
                tinyMCE.get('task-definition').setContent(description);
                tinyMCE.get('personal-notes').setContent(personal_notices);
            }
        }
    });
};

/* 
 * Перебирает строки в таблице.
 * 
 * Пересчитывает время из формата Timestamp в формат -d -h -m
 * @param budget_time - запланированное время в минутах в формате Timestamp (рассчитывается в модели)
 * @param used_time - использованное время в секундах в формате Timestamp (рассчитывается в модели)
 * Ищет в таблице задания с включенным счетчиком, если находит - ставит переключатель в правильное положение
 */
var each_tr = function()
{
    //для каждой строки таблицы:
    $('tbody tr').each(function()
    {
        //трансформирую запланированное время из формата Timestamp в понятное для человеческого глаза (-d -h -m)
        var budget_time = $(this).find('.td-budget-data nobr').text();
        var days        = Math.floor(budget_time/1440);
        var hours       = Math.floor(budget_time/60 - days*24);
        var minutes     = budget_time - days*1440 - hours*60;
        $(this).find('.td-budget-data nobr').text((days > 0 ? days+'d ' : '') + (hours > 0 ? hours+'h ' : '') + (minutes > 0 ? minutes+'m ' : ''));
        //трансформирую использованное время из формата Timestamp в понятное для человеческого глаза (-d -h -m)
        //теперь время в секундах - необходимо пересчитать
        var used_time   = $(this).find('.td-used-time nobr').text();
        var days        = Math.floor(used_time/86400);
        var hours       = Math.floor((used_time - days*86400)/3600);
        var minutes     = Math.floor((used_time - days*86400 - hours*3600)/60);
        $(this).find('.td-used-time nobr').text((days > 0 ? days+'d ' : '') + (hours > 0 ? hours+'h ' : '') + (minutes > 0 ? minutes+'m ' : ''));
        
        //проверяю есть ли задания с включенным счетчиком (со status_id = 4)
        //для того, чтобы поставить переключатель в правильное положение
        var status_id = $(this).find('.hidden-status-id');
        if(status_id.text() == '4'){
            var task_id = $(this).find('.td-task-id').text();
            var used_time = $("#task-"+task_id+"").parent().find('.td-used-time nobr').text();
            $('#task-id').val(task_id);
            
            $('#manual-start-data').attr('disabled', 'true');
            $('#manual-start-time').attr('disabled', 'true');
            $('#manual-finish-data').attr('disabled', 'true');
            $('#manual-finish-time').attr('disabled', 'true');
            $('#manual-save-time').attr('disabled', 'true');
            $('#select-change-status').attr('disabled', 'true');
            $('#task-id').attr('disabled', 'true');
            $('#del-task-button').attr('disabled', 'true');
            $('#upd-task-button').attr('disabled', 'true');
            $('.td-task-id').tooltip('hide');
            
            $('.switch').css('background', 'url("css/switch.png") 0% 50%');
            $('#auto-used-time').text(used_time);
            //console.log($(this));
        }
    });
    
    //remained-time
};

/*
 * Открывает модальное окно добавления нового задания
 */
var add_task = function()
{
    $('#my-modal').modal();
};

/*
 * Сохраняет новое задание
 */
var save_task = function()
{
    //получаем данные для сохранения
    var task_id         = ($('#hidden-task-id').val() !== '' ? $('#hidden-task-id').val() : '');
    var status_id       = ($('#hidden-status-id').val() !== '' ? $('#hidden-status-id').val() : '1');
    //если не указана дата, отсутствует/неправильно указано время - присваиваю ноль
    var start_data      = ($('#start-data').val() !== '' && $('#start-time').val().length == 5 ? $('#start-data').val()+' '+$('#start-time').val()+':00' : '0');
    var finish_data     = ($('#finish-data').val() !== '' && $('#finish-time').val().length == 5 ? $('#finish-data').val()+' '+$('#finish-time').val()+':00' : '0');
    var title           = $('#task-title').val();
    var biz_id          = $('#task-biz-id').val();
    var biz_title       = $('#task-biz').val();
    var description     = tinyMCE.get('task-definition').getContent();
    var personal_notes  = tinyMCE.get('personal-notes').getContent();
    //alert(description);
    $.ajax({
        url     : "/iamido/savetask",
        data    : {
            task_id         : task_id,
            status_id       : status_id,
            start_data      : start_data,
            finish_data     : finish_data,
            title           : title,
            biz_id          : biz_id,
            biz_title       : biz_title,
            description     : description,
            personal_notes  : personal_notes
        },
        type: 'POST',	              
        success: function(json)
        {
            if(json.result == 'date_not_exists'){ Message('Please enter the dates correctly! (date and time)', 'warning'); }
            if(json.result == 'wrong_time'){ Message('Date of finish time must be more then the date of start', 'warning'); }
            if(json.result == 'empty_fields'){ Message("Title and definition must be specified!", 'warning'); }
            if(json.result == 'okay'){
                //необходимо обновить строку в таблице - получаю данные
                var saved_task_id = json.saved_task['param_task_id'];
                //budget_time
                var budget_time = json.saved_task.budget_time;
                var days        = Math.floor(budget_time/1440);
                var hours       = Math.floor(budget_time/60 - days*24);
                var minutes     = budget_time - days*1440 - hours*60;
                
            //если редактировали задание, то находим его строку в таблице
                if(task_id == saved_task_id){
                    var saved_tr = $("#task-"+saved_task_id+"").parent();
                    //обновляю строку в таблице
                    saved_tr.find('.td-title').text(title);                             //Title
                    saved_tr.find('.td-description').html(description);                 //Definition
                    if(personal_notes){                                                 //Personal notes
                        saved_tr.find('.td-description').append("<div class='personal-notices' style='border-top: solid 1px #ddd;'><i>Personal notes:</i><br>"+personal_notes+"</div>");
                    }
                    saved_tr.find('.td-start-data').text($('#start-data').val() + ' ' + $('#start-time').val());    //Start Date
                    saved_tr.find('.td-finish-data').text($('#finish-data').val() + ' ' + $('#finish-time').val()); //Deadline
                    saved_tr.find('.td-biz-id').text('').append("<a href='/biz/"+biz_id+"/blog'>"+biz_title+"</a><br>");  
                    saved_tr.find('.td-budget-data nobr').text((days > 0 ? days+'d ' : '') + (hours > 0 ? hours+'h ' : '') + (minutes > 0 ? minutes+'m ' : ''));
                    
                }
            //если task_id заранее не определен, значит мы создаем новое заданее
                if(task_id == ''){
                    //При создании нового задания обновляю страницу.
                    window.location.replace("/iamido");
                }
                //закрываю модальное окно
                $('#my-modal').modal('hide');
                
                //console.log(switcher);
                Message('Task was succesfully saved!', 'okay');
            }
        }
    });
};

//alert
var alert_function = function()
{
    alert('Hello');
};