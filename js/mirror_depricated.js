var prev_location_option_eur;
var prev_location_option_usa;
//document.ready function
$(function(){
    //обработчик нажатия current id в модальном окне mirror
    $("#current-id").live("click", function()
    {
        $(this).children().first().css("display", "none");
        $(this).children().last().css("display", "block").focus();
        $(this).tooltip('hide');
    });
    
    //обработчик клика input-id в модальном окне mirror
    $("#input-id").live("keypress", function(event)
    {
        if(event.keyCode == 13)
        {
            // id текущей позиции
            var position_id = $(this).prev().text().replace(/\D+/g,"");
            // введенный id
            var current_id = $(this).val();
            // получаем список id позиций на странице
            var positions_id = get_position_ids();
            // проверяем есть ли в массиве позиций введенный current_id
            // правильно сравнивать со значением -1 :
            if ( $.inArray ( current_id, positions_id ) > -1 ) {
                $('#myModal' + position_id).modal('hide');
                $('#myModal' + position_id).on('hidden.bs.modal', function (event) {
                    delete_mirror_width_null_price(position_id);
                    create_mirror_from_selected(current_id);
                });
            } else {
                alert ( "Position #" + current_id + " was not found on this page!");
                return;
            }
        }
    });
    
    //обработчик нажатия кнопки next mirror в модальном окне mirror
    $(".mirror_next_position").live("click", function()
    {
        var position_id = $(this).prev().text().replace(/\D+/g,"");
        //получаем список id позиций на странице
        var positions_id = get_position_ids();
        //находим номер текущего id в массиве
        var position_index = positions_id.indexOf(position_id);
        //получаем id следующей позиции
        var next_id = positions_id[position_index + 1];
        $('#myModal' + position_id).modal('hide')
        $('#myModal' + position_id).on('hidden.bs.modal', function (event) {
            delete_mirror_width_null_price(position_id);
            create_mirror_from_selected(next_id);
        });
    });
    
    //обработчик нажатия кнопки prev mirror в модальном окне mirror
    $(".mirror_prev_position").live("click", function()
    {
        var position_id = $(this).next().text().replace(/\D+/g,"");
        //получаем список id позиций на странице
        var positions_id = get_position_ids();
        //находим номер текущего id в массиве
        var position_index = positions_id.indexOf(position_id);
        var prev_id = positions_id[position_index - 1];
        //console.log(prev_id);
        $('#myModal' + position_id).modal('hide')
        $('#myModal' + position_id).on('hidden.bs.modal', function (event) {
            delete_mirror_width_null_price(position_id);
            create_mirror_from_selected(prev_id);
        });
    });
    
    $('.btn-mirror').removeAttr('disabled');
    
    /* 
     * заполняет select списки в модальном окне mirrors
     */
    $(".select-stock").live("change", function(event){
        event.preventDefault();
        var stock_tr = $(this).parent().parent(); 
        var stock_id = $(this).val();
        //console.log(stock_id);
        var row_data = add_row(stock_id);
        
        //при смене склада мы фактически удаляем этот ряд и добавляем новый с новым складом, только все это в текущей строке
        //ловим удаляемые значения
        var deleted_stock;
        if(stock_id == "1"){deleted_stock = "2"}
        if(stock_id == "2"){deleted_stock = "1"}
        var deleted_location = $(stock_tr).find(".select-location").val();
        var deleted_option   = $(stock_tr).find(".select-location option[value='"+deleted_location+"']").clone().removeAttr("selected");
        //ДОБАВЛЕНИЕ УДАЛЕННОГО LOCATION В ОСТАЛЬНЫЕ SELECT-LOCATIONS
        if(deleted_stock == "1"){
            //нахожу строки в которых выбран stock eur и в их select-location добавляю удаленный location
            $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr").find(".select-location").append(deleted_option);
        }
        if(deleted_stock == "2"){
            //нахожу строки в которых выбран stock usa и в их select-location добавляю удаленный location
            $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr").find(".select-location").append(deleted_option);
        }
        
        $(stock_tr).find('.td-location select').empty();
        $(stock_tr).find('.td-deliverytime select').empty();
        
        for(var i = 0; i < row_data.locations.length; i++)
        {
            var obj = row_data.locations[i];
            var id = obj.location.id;
            var title = obj.location.title;
            
            $(stock_tr).find('.td-location select').append($('<option value="'+id+'">'+title+'</option>'));
        }
       
        for(var i = 0; i < row_data.deliverytimes.length; i++)
        {
            var obj = row_data.deliverytimes[i];
            var id = obj.deliverytime.id;
            var title = obj.deliverytime.title;
            
            $(stock_tr).find('.td-deliverytime select').append($('<option value="'+id+'">'+title+'</option>'));
        }
        //УДАЛЕНИЕ УЖЕ ВЫБРАНЫХ В ДРУГИХ СТРОКАХ LOCATION-OPTIONS ИЗ ДОБАВЛЕНОГО СПИСКА LOCATION
        //нахожу строки в которых выбран stock european
        if(stock_id == "1"){
            var mirror_tr_eur = $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr");
            if(mirror_tr_eur.length>0){
                var selected_eur_locations_values = Array();    
                mirror_tr_eur.each(function()
                {   
                    //выбраные options запихиваю в массив
                    selected_eur_locations_values.push($(this).find(".select-location option:selected").val());
                });
                //проходим locations в текущем ряду
                var location_options = stock_tr.find(".select-location option"); //array
                location_options.not(":selected").each(function()
                {
                    if($.inArray($(this).val(), selected_eur_locations_values) !== -1){
                        $(this).remove();
                    }
                });
            }
        }
        if(stock_id == "2"){
            var mirror_tr_usa = $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr");
            if(mirror_tr_usa.length>0){
                var selected_usa_locations_values = Array();    
                mirror_tr_usa.each(function()
                {   
                    //выбраные options запихиваю в массив
                    selected_usa_locations_values.push($(this).find(".select-location option:selected").val());
                });
                //проходим locations (в текущем ряду)
                var location_options = stock_tr.find(".select-location option"); //array
                location_options.each(function()
                {
                    if($.inArray($(this).val(), selected_usa_locations_values) !== -1){
                        $(this).remove();
                    }
                });
            }
        }
        //ВЫБРАНЫЙ OPTION УДАЛЯЮ ИЗ СПИСКОВ SELECT-LOCATION В ДРУГИХ СТРОКАХ
        var current_location = stock_tr.find(".select-location").val();
        //выбраный option сохраняю в span c id=prev-location
        stock_tr.find("span.prev-location").text(current_location);
        if(stock_id == "1"){
            //нахожу строки в которых выбран stock eur и в их select-location удаляю добавленный location (кроме той, в которой он уже выбран)
            $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr").find(".select-location option[value='"+current_location+"']").not(":selected").remove();
        }
        if(stock_id == "2"){
            //нахожу строки в которых выбран stock usa и в их select-location удаляю добавленный location (кроме той, в которой он уже выбран)
            $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr").find(".select-location option[value='"+current_location+"']").not(":selected").remove();
        }
    });
    
    $(".select-location").live("click", function(event)
    {
        var stock_id = $(this).parent().parent().find(".select-stock").val();
        if(stock_id == "1"){
            prev_location_val = $(this).val();
            prev_location_option_eur = $(this).find("option[value='"+prev_location_val+"']").removeAttr("selected");
        }
        if(stock_id == "2"){
            prev_location_val = $(this).val();
            prev_location_option_usa = $(this).find("option[value='"+prev_location_val+"']").removeAttr("selected");
        }
    });
    /*
     * Обработчик события смены Location в модальном окне Mirror
     */
    $(".select-location").live("change", function(event)
    {
        var stock_id = $(this).parent().parent().find(".select-stock").val();
        //console.log(prev_location_option);
        //ДОБАВЛЕНИЕ УДАЛЕННОГО LOCATION В ОСТАЛЬНЫЕ SELECT-LOCATIONS
        if(stock_id == "1"){
            //нахожу строки в которых выбран stock eur и в их select-location добавляю удаленный location
            $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr").find(".select-location").append(prev_location_option_eur);
        }
        if(stock_id == "2"){
            //нахожу строки в которых выбран stock usa и в их select-location добавляю удаленный location
            $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr").find(".select-location").append(prev_location_option_usa);
        }
        
        //ВЫБРАНЫЙ OPTION УДАЛЯЮ ИЗ СПИСКОВ SELECT-LOCATION В ДРУГИХ СТРОКАХ
        var current_location = $(this).val();
        if(stock_id == "1"){
            //нахожу строки в которых выбран stock eur и в их select-location удаляю добавленный location (кроме той, в которой он уже выбран)
            $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr").find(".select-location option[value='"+current_location+"']").not(":selected").remove();
        }
        if(stock_id == "2"){
            //нахожу строки в которых выбран stock usa и в их select-location удаляю добавленный location (кроме той, в которой он уже выбран)
            $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr").find(".select-location option[value='"+current_location+"']").not(":selected").remove();
        }
    });
    
    /*
     * Обработчик события изменения значения select в модальном окне mirror
     */
    $("#mirrors-edit select").live("change", function(event)
    {
        //получаем параметры для сохранения:
        //текущая строка-родитель:
        var stock_tr = $(this).parent().parent();
        //параметры для таблицы mirrors:
        var mirror_id       = $(stock_tr).find('span.mirror-id').text();
        var position_id     = $(stock_tr).find('span.position-id').text();
        var stock_id        = $(stock_tr).find('.select-stock option:selected').val();
        var location_id     = $(stock_tr).find('.select-location option:selected').val();
        var deliverytime_id = $(stock_tr).find('.select-deliverytime option:selected').val();
        var price           = parseFloat($(stock_tr).find('input.mirror-price').val());
        //сохраняем
        save_mirror(mirror_id, position_id, stock_id, location_id, deliverytime_id, price);
        //console.log($(this).val());
    });
    
    /* 
     * Обработчик события изменения значения price в модальном окне mirror
     */
    $('input.mirror-price').live("keyup", function() {
        //разрешаем ввод только цифр и точки
        if (this.value.match(/[^0-9^.]/g)) 
        {   //все кроме точки и цифр удаляем
            this.value = this.value.replace(/[^0-9^.]/g, "");
        }
        if($(this).val() == "") 
        {
            return;
        }
        else
        {
            //получаем параметры для сохранения:
            //текущая строка-родитель:
            var stock_tr = $(this).parent().parent();
            //параметры для таблицы mirrors:
            var mirror_id = $(stock_tr).find('span.mirror-id').text();
            var position_id = $(stock_tr).find('span.position-id').text();
            var stock_id = $(stock_tr).find('.select-stock option:selected').val();
            var location_id = $(stock_tr).find('.select-location option:selected').val();
            var deliverytime_id = $(stock_tr).find('.select-deliverytime option:selected').val();
            var price = parseFloat($(stock_tr).find('input.mirror-price').val());
            
            var empty_input = $("#mirrors-edit tr").find("input.mirror-price").val() == ""; 
            
            //сохраняем
            save_mirror(mirror_id, position_id, stock_id, location_id, deliverytime_id, price);
            
            //если нет пустых price активируем add row
            //пустым может быть только последний Price
            $("#mirrors-edit tr").each(function()
            {
                var empty_input = $("#mirrors-edit tr").find("input.mirror-price").val() == ""; 
                //если нет пустых price активируем del row
                if(empty_input == false){
                    //console.log("add mirror disabled");
                    $('button#add-mirror').removeAttr("disabled");
                    $('button#del-mirror').removeAttr("disabled");
                }else{
                    $('button#add-mirror').attr("disabled", "true");
                }
            })
        }
        
    });
});


/* Получаем список Id позиций для текущей страницы (для Mirrors)
 * @version 20140614, Uskov
 */
var get_position_ids = function()
{
    //получаем значения фильтра из адресной строки
    var filter = window.location.href.slice(46);
    //если нет параметров фильтра - утанавливаем stock=1 (по умолчанию для positions)
    if(!filter){
        filter = "stock:1;thickness:;";
    }
    //console.log(filter);
    var json_data = $.ajax({
        url     : '/position/getpositionsids',

        data    : {
            filter : filter
        },
        success: function(json){
            //
        }
    });
    var parsed_json_data = jQuery.parseJSON(json_data.responseText);
    //создаю массив для position ids
    var positions_id = new Array();

    $.each(parsed_json_data.content["0"], function(){
       positions_id.push($(this)["0"]["steelposition_id"]);
    });
    return positions_id;
}
   
/* Получает данные о mirror для позиции
 * 
 * @param {type} position_id
 * @returns {undefined}
 */
var get_mirrior_info = function(position_id)
{
    $.ajax ({
        url: '/mirror/getmirror',
        data : {
            position_id : position_id
        },
        type: 'POST',               
        success: function(json)
        {
            console.log(json);
        }
    });    
}

var add_row = function(stock_id){
    var json_result = $.ajax ({
        url: '/mirror/addrow',
        data : {
            stock_id : stock_id
        },
        type: 'POST',               
        success: function(json)
        {
            return json;
        }
    });
    
    var json_data = jQuery.parseJSON(json_result.responseText);
    
    return json_data;
    //return json_result;
};

/**
 *Зеркало
 *
 *Позволяет создавать и редактировать зеркало (зеркало - отображение позиции на нескольких складах с подменой Location)
 *@param {integer} position_id id позиции
 *@version 20140521
 *@author Gonchar
 *
 *@version 20140625
 *@modified 20140620, Uskov
 *
 */
var create_mirror_from_selected = function(position_id)
{
    //получаем список id позиций на странице
    var positions_id = get_position_ids();
    //console.log(position_ids);
    $.ajax({
        url     : '/mirror/getmirror',
        
        data    : {
            position_id : position_id
        },
    
        success: function(json){if (json.result === 'okay') {
                //console.log(json);
                $('body').append(json.content);
                $('#myModal'+position_id).modal();
            }
        }
    });
    //выполняется после открытия модального окна
    $('#myModal' + position_id).live('shown.bs.modal', function (event) 
    {
        //находим номер текущего id в массиве
        var position_index = positions_id.indexOf(position_id);
        //получаем id следующей и предыдущей позиций
        var next_id = positions_id[position_index + 1];
        var prev_id = positions_id[position_index - 1];
        //деактивируем кнопку next если находимся на последней позиции
        if(!next_id){
            $(".mirror_next_position").attr("disabled", true);
        }
        //деактивируем кнопку prev если находимся на первой позиции
        if(!prev_id){
            $(".mirror_prev_position").attr("disabled", true);
        }
        //активируем tooltip для кнопок next/prev
        $(".btn-default").tooltip();
        //деактивируем кнопки add mirror и delete
        $("#del-mirror").attr('disabled', 'true');
        $("#add-mirror").attr('disabled', 'true');
        //деактивируем add row и проверяем значение price для активации delete
        var mirror_tr = $("#mirrors-edit tr");
        mirror_tr.each(function()
        {
            var empty_input = $(this).find("input.mirror-price").val() == ""; 
            if(empty_input == true){
                console.log("Creating new mirror");
            }else{
                $('#del-mirror').removeAttr("disabled");
                $('#add-mirror').removeAttr("disabled");
                console.log("Editing a mirror");
            }
        });
        //УДАЛЕНИЕ ВЫБРАНЫХ OPTION ИЗ СПИСКА LOCATION
        //нахожу строки в которых выбран stock european
        var mirror_tr_eur = $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr");
        if(mirror_tr_eur.length>0){
            var selected_eur_locations_values = Array();    
            mirror_tr_eur.each(function()
            {   
                //выбраные options запихиваю в массив
                selected_eur_locations_values.push($(this).find(".select-location option:selected").val());
            });
            //проходим строки в которых выбран stock european
            mirror_tr_eur.each(function()
            {
                var location_options = $(this).find(".select-location option"); //array
                //проверка каждого option кроме выбранного в каждом location
                location_options.not(":selected").each(function()
                {
                    //если такой уже выбран - удаляем
                    if($.inArray($(this).val(), selected_eur_locations_values) !== -1){
                        $(this).remove();
                    }
                });
            });
        }
        //нахожу строки в которых выбран stock usa
        var mirror_tr_usa = $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr");
        if(mirror_tr_usa.length>0){
            var selected_usa_locations_values = Array();    
            mirror_tr_usa.each(function()
            {   
                //выбраные options запихиваю в массив
                selected_usa_locations_values.push($(this).find(".select-location option:selected").val());
            });
            //проходим строки в которых выбран stock usa
            mirror_tr_usa.each(function()
            {
                var location_options = $(this).find(".select-location option"); //array
                //проверка каждого option кроме выбранного в каждом location
                location_options.not(":selected").each(function()
                {
                    //если такой уже выбран - удаляем
                    if($.inArray($(this).val(), selected_usa_locations_values) !== -1){
                        $(this).remove();
                    }
                });
            });
        }
    });
    //выполняется после закрытия модального окна
    $('#myModal' + position_id).live('hidden.bs.modal', function (event) 
    {
        delete_mirror_width_null_price(position_id);
    });
};

/*
 * Проверяет по position_id наличие в БД mirrors с price = 0.00 и удаляет их
 * @param $position_id
 * 
 * @version 20140622
 * @author Uskov
 */
var delete_mirror_width_null_price = function(position_id)
{
    //находим в базе mirror c price = 0.00 и возвращаем их id
    $.ajax ({
        url: '/mirror/getnullpricemirrors',
        data : {
            position_id : position_id
        },
        type: 'POST',               
        success: function(json)
        {
            if (json.result === 'okay') 
            {
                console.log("deleted mirrors id: " + json.content);
            }
        }
    });
    //разрушаем модальное окно
    $(this).data('bs.modal', null);
    $('#myModal' + position_id).remove();
};

/**
 *Добавляет строку в таблицу редактирования Mirror
 *
 *Позволяет показывать и прятать позиции на складе. Под складом здесь следует понимать пользовательскую часть системы (myroom.platesahead.com/stock, myroom.steelemotion.com/stock)
 *
 *@param object obj объект, который вызвал событие
 *@param int position_id id позиции
 *@param bool hidden скрыть, если true
 *
 *@version 20140521
 *@author Gonchar
 *
 *@version 20140608
 *@author Uskov
 */
var mirror_add_row = function()
{
    var mirror_tr = $("#mirrors-edit tr");
    var number_rows = mirror_tr.size();
    var index_prev_row = number_rows-1;
    var prev_stock_val = $(".select-stock:eq("+index_prev_row+")").val();
    //location копируемого ряда
    var prev_location = $("#mirrors-edit tr:last .select-location").val();
    //копирую последний ряд
    $("#mirrors-edit tr:last").clone().appendTo("#mirrors-edit");
    //в добавленной строке удаляю текущий location (потому что он дублирует предыдущий)
    $("#mirrors-edit tr:last .select-location option[value='"+prev_location+"']").remove();
    //добавляю атрибут selected складу в новом ряду
    $(".select-stock:last [value='"+prev_stock_val+"']").attr("selected", "selected");
    //есть необходимость сохранять новый mirror при создании
    //получаем параметры для сохранения у последнего mirror:
    var mirror_id       = "";
    var position_id     = $("#mirrors-edit tr:last span.position-id").text();
    var location_id     = $("#mirrors-edit tr:last .select-location option:selected").val();
    var deliverytime_id = $("#mirrors-edit tr:last .select-deliverytime option:selected").val();
    var price           = $("#mirrors-edit tr:last input.mirror-price").val();
    //сохраняем mirror
    save_mirror(mirror_id, position_id, location_id, deliverytime_id, price);
    
    //ВЫБРАНЫЙ OPTION УДАЛЯЮ ИЗ СПИСКОВ SELECT-LOCATION
    var added_stock_id         = $("#mirrors-edit tr:last .select-stock").val();
    var current_location = $("#mirrors-edit tr:last .select-location").val();
    if(added_stock_id == "1"){
        //нахожу строки в которых выбран stock eur и в их select-location удаляю добавленный location (кроме той, в которой он уже выбран)
        $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr").find(".select-location option[value='"+current_location+"']").not(":selected").remove();
    }
    if(added_stock_id == "2"){
        //нахожу строки в которых выбран stock usa и в их select-location удаляю добавленный location (кроме той, в которой он уже выбран)
        $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr").find(".select-location option[value='"+current_location+"']").not(":selected").remove();
    }
    
    //деактивируем add row и проверяем значение price для активации delete
    mirror_tr.each(function()
    {
        //активируем кнопки delete
        $('button#del-mirror').removeAttr("disabled");
        
        var empty_input = mirror_tr.find("input.mirror-price").val() == ""; 
        if(empty_input){
            $('#add-mirror').attr("disabled", "true");
        }else{
            $('#del-mirror').removeAttr("disabled");
        }
    })
};

/*
 * Сохранение mirror при изменении в любом поле формы. Сохраняет 1 mirror, select которого изменен
 * Поле input price должно быть заполнено.
 * Вызывается при изменении в select и price
 * @param mixed $mirror_id
 * @param mixed $position_id
 * @param mixed $location_id
 * @param mixed $deliverytime_id
 * @param mixed $price
 * //@param mixed $status_id
 * 
 * @version 20140606
 * @author Uskov
 */
var save_mirror = function(mirror_id, position_id, stock_id, location_id, deliverytime_id, price)
{
        $.ajax ({
            url: '/mirror/savemirror',
            data : {
                mirror_id : mirror_id,
                position_id : position_id,
                stock_id : stock_id,
                location_id : location_id,
                deliverytime_id : deliverytime_id,
                price: price
            },
            type: 'POST',
            success: function(json)
            {
                if (json.result === 'okay') 
                {
                    //получаю id нового mirror
                    var saved_id = json.object.id;
                    //если mirror новый присваиваю mirror_id
                    if(mirror_id !== saved_id)
                    {
                        $("#mirrors-edit tr:last").find('span.mirror-id').text(saved_id);
                        console.log("new mirror saved, id = " + saved_id);
                    }
                    else
                    {
                        console.log("mirror saved, id = " + saved_id);
                    }
                }
            }
        });
};

/**
 *Удаляет строку из таблицы редактирования Mirror
 *
 *Позволяет показывать и прятать позиции на складе. Под складом здесь следует понимать пользовательскую часть системы (myroom.platesahead.com/stock, myroom.steelemotion.com/stock)
 *
 *@param object obj объект, который вызвал событие
 *@param int position_id id позиции
 *@param bool hidden скрыть, если true
 *
 *@version 20140521
 *@author Gonchar
 */
var mirror_del_row = function(obj)
{
    var stock_tr = $(obj).parent().parent();
    var mirror_id = $(stock_tr).find('span.mirror-id').text();
    
    var number_rows = $("#mirrors-edit tr").size();
    if(number_rows == 1){
        //получаем параметры для сохранения:
        var mirror_id       = $("span.mirror-id").text();
        var position_id     = $("span.position-id").text();
        var location_id     = $(".select-location option:selected").val();
        var deliverytime_id = $(".select-deliverytime option:selected").val();
        //var price           = $("input.mirror-price").val();
        var price           = '';
        //сохраняем mirror
        save_mirror(mirror_id, position_id, location_id, deliverytime_id, price);
        $("input.mirror-price").val('');
        $("#del-mirror").attr('disabled', 'true');
        $("#add-mirror").attr('disabled', 'true');
        
    }else{
        $.ajax ({
            url: '/mirror/deletemirror',
            data : {
                mirror_id : mirror_id
            },
            type: 'POST',               
            success: function(json)
            {
                if (json.result === 'okay') 
                {
                    var deleted_id = json.object.id;
                    console.log("mirror deleted, id = " + deleted_id);
                }
            }
        });
        //ловим значение удаляемого Location
        var deleted_stock    = $(stock_tr).find(".select-stock").val();
        var deleted_location = $(stock_tr).find(".select-location").val();
        var deleted_option   = $(stock_tr).find(".select-location option[value='"+deleted_location+"']").clone().removeAttr("selected");
        
        //ДОБАВЛЕНИЕ УДАЛЕННОГО LOCATION В ОСТАЛЬНЫЕ SELECT-LOCATIONS
        if(deleted_stock == "1"){
            //нахожу строки в которых выбран stock eur и в их select-location добавляю удаленный location
            $("#mirrors-edit .select-stock option[value='1']:selected").parents("tr").find(".select-location").append(deleted_option);
        }
        if(deleted_stock == "2"){
            //нахожу строки в которых выбран stock usa и в их select-location добавляю удаленный location
            $("#mirrors-edit .select-stock option[value='2']:selected").parents("tr").find(".select-location").append(deleted_option);
        }
        //удаляем ряд
        var row = $(obj).parent().parent().remove();
        //активируем кнопки delete
        $("#mirrors-edit tr").each(function()
        {
            var empty_input = $("#mirrors-edit tr").find("input.mirror-price").val() == ""; 
            //если нет пустых price активируем del row
            if(empty_input == false){
                $('button#del-mirror').removeAttr("disabled");
            }
        })
    }
};

//удаляет все mirror кроме первого
var mirror_del_all = function()
{
    $("#mirrors-edit tr").each(function()
    {
        var button_del = $(this).find("#del-mirror");
        mirror_del_row(button_del);
    });
};