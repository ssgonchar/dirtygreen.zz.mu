$(function()
{
    $("#customLink").live("click", function(event)
    {
        event.preventDefault();
    });
});

/**
 * Заполняет выпадающий список товаров
 */
var bind_products = function(team_id)
{
    if (team_id > 0)
    {
        $('#products').prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/team/getproducts',
            data : {
                team_id : team_id,
                full_branch : true,
				in_biz :	  true
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    console.log(json);
                    
                    $('#products').empty();
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
                    fill_select("#products", json.products, {'value' : 0, 'name' : "--"});
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
* Добавляет найденные компании в бизнес
*/
var add_biz_company = function()
{
    var role = $('#biz-co-role').val();
    if (role.replace(/\s+/g, '').length == 0) return;

    $('#biz-co-search-result > option:selected').each(function()
    {  
        if ($(this).val() > 0){
            company_id      = $(this).val();
            company_title   = $(this).text();
            
            new_row         = '<td><input type="hidden" name="' + role + 's[' + company_id + '][company_id]" class="'  + role + '_id" value="' + company_id + '"><a id="' + role + '-' + company_id + '" class="btn btn-primary btn-xs" href="/company/' + company_id + '">' + company_title + '&nbsp;<span id="customLink" class="glyphicon glyphicon-remove" onclick="remove_biz_company(\'' + role + '\', ' + company_id + ');"></span></a></td>';
            $('#' + role + 's tr:last').before('<tr id="' + role + '-' + company_id + '">' + new_row + '</tr>');
            $('#' + role + '-not-set').hide();            
        }        
    });    
    
};

/**
 * Ищет компании
 */
var find_biz_company = function(title)
{
    if (title.replace(/\s+/g, '').length < 3) return;

    $('#biz-co-search-result').empty();
    $('#biz-co-search-result').append($('<option value="0" style="font-style: italic;">loading...</option>'));
    
    $.ajax({
        url: '/company/getlistbytitle',
        data : {
            title : title
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#biz-co-search-result').empty();
                if (json.list.length > 0)
                {
                    $.each(json.list, function(key, item){
                        $('#biz-co-search-result').append($('<option value="' + item.company.id + '">' + item.company.title + '</option>'));
                    });
                }                
                else
                {
                    $('#biz-co-search-result').append($('<option value="0" style="font-style: italic;">nothing was found</option>'));
                }
            }
            else
            {
                error = true;
            }                
        }
    });    
};

/**
/**
 * Показывает окно поиска и выбора компаний для бизнеса
 */
var show_biz_modal = function()
{
    $('#myModal').modal('show');
};

/**
 * Прячет окно выбора компаний для бизнеса
 */
var close_biz_modal = function()
{
    $('#myModal').modal('hide');
       
};
/**
/**
 * Показывает окно поиска и выбора компаний для бизнеса
 */
var show_biz_co_list = function()
{
    $('#biz-co-select').show();
};

/**
 * Прячет окно выбора компаний для бизнеса
 */
var close_biz_co_list = function()
{
    $('#biz-co-select').hide();
};
/**
 * Удаляет компанию из списка компаний 
 */
var remove_biz_company = function(object_alias, object_id)
{
    var title = object_alias;
    
    if (object_alias == 'pproducer') title = 'potential producer';
    if (object_alias == 'pbuyer') title = 'potential buyer';

    if (!confirm('Remove ' + title + ' ?')) return;

    $('#' + object_alias + '-' + object_id).remove();    
    
    if ($('#' + object_alias + 's tr').length == 1)
    {
           
        // $('#' + object_alias + 's tbody').html("<i>not defined</i>");
              $('#' + object_alias + '-not-set').show();
          
    }

};
$(".chosen-select").chosen({
    no_results_text: "Oops, nothing found!",
    disable_search_threshold: 10
    
    
});
  tinymce.init({
  selector : '#description',
  plugins: [
      "advlist autolink lists link image charmap print preview anchor",
      "searchreplace visualblocks code fullscreen",
      "insertdatetime media table contextmenu paste textcolor"
  ],
  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontselect | fontsizeselect | forecolor | backcolor"
 });
 
 

/*
 * do not need now
 *
 * version 2013022, sasha
$(function(){
    $('.biz-driver').on('change', function(){
        var driver_id   = $(this).val();
        var cur_uid     = $('.cur-user-id').val();
        
        if (driver_id == cur_uid)
        {
            $('.biz-favourite').parents('tr:first').show();
        }
        else
        {
            if ($('#navigator-' + cur_uid).attr('checked') != 'checked')
            {
                $('.biz-favourite')
                    .removeAttr('checked')
                    .parents('tr:first').hide();
            }
        }
    });

    $('.biz-navigators').on('click', function(){
        var navigator_id= $(this).val();
        var cur_uid     = $('.cur-user-id').val();
        
        if (cur_uid != navigator_id) return;
        
        if ($(this).attr('checked') == 'checked')
        {
            $('.biz-favourite').parents('tr:first').show();
        }
        else
        {
            if ($('.biz-driver').val() != cur_uid)
            {
                $('.biz-favourite')
                    .removeAttr('checked')
                    .parents('tr:first').hide();
            }
        }
    });
});
*/
