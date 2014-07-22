//������� document.ready
$(function(){
                     
    //���������� ����� �� ��������� (toggle)
    $('.panel-heading').on('click', function(event) {
        $(this).next().collapse('toggle');
    });
    //������� ������� ��������� ���� ��� ������ �� ��������� (��. ����)
    $('.category-link').on('click', function(event){
        event.stopPropagation();
        event.preventDefault();
    });
    //��� �������� �������� ������� ������ ��������� Trade
    //show_nomenclature(event, 1)
    
    //$('#heading h1').after("<span>test</span>");
    $('#test-popover').popover();
});

/////////////////////////////////////////////////////////////
/**
* �������� � ������� � ������� ������ ����������� �� URL
*
* @version 20140517, uskov
*/ 
var show_nomenclature = function (event, category_url){     //�������� �������� {$row.category.url} ��� {$sub_row.category.url}
            
    $.ajax	({                                          //ajax ������
        url: '/nomenclature/getlistbycategoryid',	    //������ ������ �� ������
        data : {                                            //�������� ������ (���� : ��������)
            category_url : category_url
        },
        type: 'POST',	              
        success: function(json){
            if (json.result == 'okay'){
                $('#nomenclature-thead').next().remove();   //������� ���������� ���������� (��, ��� ����� ���������)
                //������ tbody � ������� � main_index.tpl � ��������� � ���� ������ ��� ������� control_nomenclature.tpl
                $('#nomenclature-thead').after('<tbody>' +  json.content + '</tbody>');
            }
            if (json.result == 'error'){
                $('#nomenclature-thead').next().remove();
                $('#nomenclature-thead').after('<tbody><tr><td>No titles</td><td>Selected category has no positions. Use button "Add" if you want to add new ones.</td><td>&nbsp</td><td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr></tbody>');
            }
        }
    });
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
    $.ajax	({                       //ajax запрос
        url: '/nomenclature/edit',	 //������ ������ �� ������
        data : {                         //�������� ������ (���� : ��������)
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