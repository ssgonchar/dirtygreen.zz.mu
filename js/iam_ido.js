
//document.ready function
$(function(){
    $('.datepickers').datepicker({
        showWeek: true,
	minDate: 0,
        dateFormat  : 'yy-mm-dd'
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
    
    //Tooltip для ячеек с task id в таблице
    $('.td-task-id').tooltip();
    
    //Отменяю переключение если не выбран task id
    $('#myonoffswitch').live("click", function(event)
    {
        if($('#task-id').val() == ''){
            event.preventDefault();
        }
    });
    
    //Ловлю изменение переключателя on/off
    $('#myonoffswitch').live("change", function()
    {
        if($(this).attr('checked') == 'checked'){
            if($('#task-id').val() !== ''){
                console.log("ON");
                $('#auto-start-time').attr('disabled', 'true');
                $('#auto-finish-time').attr('disabled', 'true');
                $('#task-id').attr('disabled', 'true');
                $('.td-task-id').tooltip('hide');
            }
        }
        else{
            console.log('OFF');
            $('#auto-start-time').removeAttr('disabled');
            $('#auto-finish-time').removeAttr('disabled');
            $('#task-id').removeAttr('disabled');
        }
    });
    
    //Добавление id задания в окно task id
    $('.td-task-id').live('click', function()
    {
        if($('#myonoffswitch').attr('checked') !== 'checked'){
            var task_id = $(this).text();
            $('#task-id').val(task_id);
            //активирую кнопки Delete и Update
            $('#del-task-button').removeAttr('disabled');
            $('#upd-task-button').removeAttr('disabled');
        }
        
    });
    
    //Обработчик нажатия кнопки Save
    $('#save-button').live('click', function()
    {
        save_task();
    });
    
    //Расчитует запланированное время для каждой строки таблицы
    calc_time();
    
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
        update_task(task_id);
    });
});

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
                alert("Task with ID = "+json.deleted_task.id+" was successfully deleted.");
            }
            if(json.result == 'error'){
                alert("You don`t have task with ID = "+task_id+"");
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
var update_task = function(task_id)
{
    $.ajax({
        url     : "/iamido/updatetask",
        data    : {
            task_id : task_id
        },
        type: 'POST',	              
        success: function(json)
        {
            if(json.result == 'okay'){
                console.log(json.current_task);
                //данные:
                var start               = json.current_task['0']['data_start'].split(" "); //разделяю по пробелу на дату и время
                var start_data  = start['0'];
                var start_time  = start['1'].slice(0,5);
                
                var finish              = json.current_task['0']['data_finish'].split(" "); //разделяю по пробелу на дату и время
                var finish_data = finish['0'];
                var finish_time = finish['1'].slice(0,5);
                
                var title               = json.current_task['0']['task']['title'];
                var description         = json.current_task['0']['task']['description'];
                var personal_notices    = json.current_task['0']['personal_notices'];
                var status_id           = json.current_task['0']['status_id'];
                var task_id             = json.current_task['0']['task_id'];
                var used_time           = json.current_task['0']['used_time'];
                var biz_id              = json.current_task['0']['task']['biz_id'];
                
                //console.log(finish_data);
                $('#my-modal').modal();
                $('.modal-title').text('Editing a task');
                $('#hidden-task-id').val(task_id);          //скрытый input для хранения task_id
                $('#start-data').val(start_data);           //дата начала задания
                $('#start-time').val(start_time);           //время начала задания
                $('#finish-data').val(finish_data);         //дата окончания задания
                $('#finish-time').val(finish_time);         //время окончания задания
                $('#task-title').val(title);
                $('#biz-id').val(biz_id);
                $('#task-definition').val(description);
                $('#personal-notes').val(personal_notices);
            }
        }
    });
};

var calc_time = function()
{
    $('tbody tr').each(function()
    {
        var budget_time = $(this).find('.td-budget-data nobr').text();
        var days = Math.floor(budget_time/1440);
        var hours = Math.floor(budget_time/60 - days*24);
        var minutes = budget_time - days*1440 - hours*60;
        $(this).find('.td-budget-data nobr').text((days > 0 ? days+'d ' : '') + (hours > 0 ? hours+'h ' : '') + (minutes > 0 ? minutes+'m ' : ''));
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
    var task_id = ($('#hidden-task-id').val() !== '' ? $('#hidden-task-id').val() : '');
    var status_id = '1';
    var start_data = $('#start-data').val() + ' ' + $('#start-time').val() + ':00';
    var finish_data = $('#finish-data').val() + ' ' + $('#finish-time').val() + ':00';
    var title = $('#task-title').val();
    var biz_id = $('#biz-id').val();
    var description = $('#task-definition').val();
    var personal_notes = $('#personal-notes').val();
        
    $.ajax({
        url     : "/iamido/savetask",
        data    : {
            task_id         : task_id,
            status_id       : status_id,
            start_data      : start_data,
            finish_data     : finish_data,
            title           : title,
            biz_id          : biz_id,
            description     : description,
            personal_notes  : personal_notes
        },
        type: 'POST',	              
        success: function(json)
        {
            //
        }
    });
};