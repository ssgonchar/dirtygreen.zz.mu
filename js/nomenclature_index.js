//document.ready function
$(function(){ 
    //
    $('.panel-heading').on('click', function(event) {
        $(this).next().collapse('toggle');
    });
    //
    $('.category-link').on('click', function(event){
        event.stopPropagation();
        event.preventDefault();
    });
    //включает popover
    $('#test-popover').popover();
    
    $("input[value=Save]").live("click", function()
    {
        //console.log("drherthr");
        save(); 
    });
    
    //обработчик нажатия кнопки Save
    /*$( "form" ).submit(function( event )
    {
        event.preventDefault();
        event.stopPropagation();
    });*/
    
    
    tinymce_init();
});

//подключаю текстовый HTML редактор (setup - срабатывает при сохранении)
var tinymce_init = function()
{
    tinymce.init({
        selector : "#description-input",
        inline: false,
        setup: function(editor) {
            editor.on('GetContent', function(e) {
                //
            });
        },
        plugins: [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste textcolor"
            ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontselect | fontsizeselect | forecolor | backcolor"
    });
};

var save = function()
{
    var title = $("#title");
    //чтоб не сохранял при пустом title
    //console.log(title);
    if(title.text() !== ""){
        var id = $("#nomenclature-id").val();
        var description_textarea = $("#description-input");
        description_textarea.text(tinyMCE.activeEditor.getContent());
        var description = description_textarea.text();
        //console.log(description.text());
        $.ajax	({
            url: '/nomenclature/save',
            data : {
                id : id,
                description : description
            },
            type: 'POST',	              
            success: function(json)
            {
                $("#displayer").text("Nomenclature was successfully saved");
                setTimeout("$('#displayer').text('');",3000);

            }
        });
    }
};

/*
 * 
 * @returns {undefined}
 */
var search = function()
{
    var search_form = new Array();
    //получаем данные для поиска
    var title = $("#title-input").val();
    var description = $("#description-input").val();
    //Ajax запрос
    $.ajax	({
        url: '/nomenclature/search',
        data : {
            title       : title,
            description : description
        },
        type: 'POST',	              
        success: function(json){
            /*if (json.result == 'okay'){
                //
            }
            if (json.result == 'error'){
                //
            }*/
        }
    });
    //console.log(title);
};

/////////////////////////////////////////////////////////////
/**
* Показывает список номенклатур по искомой категории
* @param {int} $URL ссылка на категорию
* @version 20140517, uskov
*/ 
var show_nomenclature = function (event, id){
    //console.log(id);
    $.ajax	({
        url: '/nomenclature/getbyid',
        data : {
            id : id
        },
        type: 'POST',	              
        success: function(json){
            if (json.result == 'okay'){
                //получаю данные
                var title = $("#title");
                title.text(json.content["nomenclature"]["title"]);
                //записываю id в скрытый Input
                var id = json.content["nomenclature"]["id"];
                $("#nomenclature-id").val(id);
                
                var description = $("#description-input");
                description.text(json.content["nomenclature"]["description"]);
                //обновляю содержимое обьекта tinymce
                tinyMCE.activeEditor.setContent(description.text());
            }
            if (json.result == 'error'){
                //
            }
        }
    });
    /*
    $.ajax	({
        url: '/nomenclature/getlistbycategoryid',
        data : {
            category_url : category_url
        },
        type: 'POST',	              
        success: function(json){
            if (json.result == 'okay'){
                $('#nomenclature-thead').next().remove();
                $('#nomenclature-thead').after('<tbody>' +  json.content + '</tbody>');
            }
            if (json.result == 'error'){
                $('#nomenclature-thead').next().remove();
                $('#nomenclature-thead').after('<tbody><tr><td>No titles</td><td>Selected category has no positions. Use button "Add" if you want to add new ones.</td><td>&nbsp</td><td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr></tbody>');
            }
        }
    });
    */
};
/////////////////////////////////////////////////////////////
/**
 * Показывает модальное окно добавления номенклатуры
 *
 * @version 20140517, uskov
 */
var add_nomenclature = function(event)
{
    $('#modal-add').modal();
    
    tinymce.remove('textarea');       
    tinymce.init({
        selector : "textarea",
        plugins: [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste textcolor"
            ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontselect | fontsizeselect | forecolor | backcolor"
    });
}
/////////////////////////////////////////////////////////////
/**
 * Показывает модальное окно редактирования номенклатуры
 *
 * @version 20140518, uskov
 */
var edit_nomenclature = function(event, nomenclature_id)
{
    $.ajax	({
        url: '/nomenclature/edit',
        data : {
            nomenclature_id : nomenclature_id
        },
        type: 'POST',	              
        success: function(json)
        {
            if (json.result == 'okay') 
            {
                tinymce.remove('textarea'); //удаляю предыдущий обьект tinymce
                $('#modal-edit .control_edit').remove(); //удаляет предыдущее содержимое (если оно есть)
                $('#modal-edit .edit-category').before('<li class="control_edit">' +  json.content + '</li>');
                $('#modal-edit').modal();

//consol.log('run');
                //подключаю текстовый HTML редактор
                       
                tinymce.init({
                    selector : "textarea",
                    plugins: [
                        "advlist autolink lists link charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste textcolor"
                        ],
                        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontselect | fontsizeselect | forecolor | backcolor"
                });

            }

        }
    });
}
/////////////////////////////////////////////////////////////