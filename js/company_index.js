/**
 * Заполняет список активностей
 * @version 20120608, zharkov
 */
var fill_activities = function(parent_id, list_object)
{
    // очистка спика activities при изменении industry
    if (list_object == 'sel_activity')
    {
        $('#sel_speciality').empty();
        $('#sel_speciality').prepend($('<option value="0">--</option>'));        
    }
    
    if (parent_id > 0)
    {
        $('#' + list_object).prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/activity/getlist',
            data : {
                parent_id : parent_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select('#' + list_object, json.list, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (parent_id == 0 || error)
    {
        $('#' + list_object).empty();
        $('#' + list_object).prepend($('<option value="0">--</option>'));
    }    
};


/**
 * OnLoad
 * @version 20120608, zharkov
 */
//Поиск по нажатию Enter
$(".find-parametr").live("keyup", function()
{
    $(this).keypress(function(event){
    if(event.keyCode == 13) 
    {
        $("input[value=Find]").click();
        return false;
        //console.log("key press");
    }
    });
});
$(function(){    
    
    // фокус
    if ($('#keyword').length > 0) 
    {
        $('#keyword').focus();
    }
    
    // country change
    $('#country').change(function(){
        
        country_id = $('#country').val();

        if (country_id > 0)
        {
            $('#region').prepend($('<option selected="" value="0">Loading...</option>'));        
            error = false;
            
            $.ajax({
                url: '/region/getlist',
                data : {
                    country_id  : country_id
                },
                success: function(json){
                    if (json.result == 'okay') 
                    {
                        fill_select("#region", json.list, {'value' : 0, 'name' : "--"});
                    }
                    else
                    {
                        error = true;
                    }                
                }
            });        
        }
        
        if (country_id == 0 || error)
        {
            $('#region').empty();
            $('#region').prepend($('<option value="0">--</option>'));
        }    
        
    });
    
    // region change
    $('#region').change(function(){

        region_id = $('#region').val();

        if (region_id > 0)
        {
            $('#city').prepend($('<option selected="" value="0">Loading...</option>'));        
            error = false;
            
            $.ajax({
                url: '/city/getlist',
                data : {
                    region_id  : region_id
                },
                success: function(json){
                    if (json.result == 'okay') 
                    {
                        fill_select("#city", json.list, {'value' : 0, 'name' : "--"});
                    }
                    else
                    {
                        error = true;
                    }                
                }
            });        
        }
        
        if (region_id == 0 || error)
        {
            $('#city').empty();
            $('#city').prepend($('<option value="0">--</option>'));
        }
        
    });
    
});

/**
 * link for orders in companies
 * 
 * @version 20130829, sasha
 */
$('.biz-order-href').on('click', function(){
	
	location.href = $(this).data('href');
});
