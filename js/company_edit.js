/**
 * ������� ������� ��������
 * @version: 20120530, zharkov
 */
var remove_feedstock = function(index)
{
    if (!confirm('Remove feedstock ?')) return;
    $('#co-feedstock-remove-' + index).remove();
};

/**
 * ��������� ������� � ��������
 * @version: 20120530, zharkov
 * @version: 20130801, sasha
 */
var add_feedstock = function()
{
    var index               = parseInt($('#co-feedstock-index').val()) + 1;
    var feedstock_group     = $('.co-feedstock-group-0');
    var new_feedstock_group = feedstock_group.clone();
    var new_feedstock       =   '<select id="co-feedstock-' + index+ '" class="max co-feedstock" data-id="'+ index +'">' +
                                    '<option value="0">--</option>' +
                                '</select>';
    
    new_feedstock_group.show(); 
    new_feedstock_group.attr('data-id', index);
    new_feedstock_group.attr('id', 'co-feedstock-group-' + index);
    new_feedstock_group.removeClass('co-feedstock-group-0');
    new_feedstock_group.addClass('co-feedstock-group');
    
    tr      =   '<tr id="co-feedstock-remove-'+ index +'">' + 
                    '<td width="45%"><input type="hidden" class="co-feedstocks" value="">' + 
                        '<input type="hidden" name="feedstocks[' + index + '][id]" value="">' + 
                        '<input type="hidden" id="feedstock-value-'+ index +'" name="feedstocks[' + index + '][product_id]" value="">' +
                        new_feedstock_group[0].outerHTML +
                    '</td>' + 
                    '<td width="45%">'+ new_feedstock +'</td>' + 
                    '<td width="10%"><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_feedstock(' + index + ');"></td>' + 
                '</tr>';

    $('#co-feedstocks > tbody tr:last').before(tr);
    $('#co-feedstock-index').val(index); 
};


/**
 * ������� ������� ��������
 * @version: 20120530, zharkov
 */
var remove_product = function(index)
{
    if (!confirm('Remove product ?')) return;
    $('#co-product-remove-' + index).remove();
};

/**
 * ��������� ������� � ��������
 * @version: 20120530, zharkov
 * @version: 20130731, sasha
 */
var add_product = function()
{
    var index               = parseInt($('#co-product-index').val()) + 1;
    var product_group       = $('.co-product-group-0');
    var new_product_group   = product_group .clone();
    var new_product         =   '<select id="co-product-' + index+ '" class="max co-product" data-id="'+ index +'">' +
                                    '<option value="0">--</option>' +
                                '</select>';
    
    new_product_group.show(); 
    new_product_group.attr('data-id', index);
    new_product_group.attr('id', 'co-product-group-' + index);
    new_product_group.removeClass('co-product-group-0');
    new_product_group.addClass('co-product-group');
    
    tr      =   '<tr id="co-product-remove-'+ index +'">' + 
                    '<td width="45%"><input type="hidden" class="co-products" value="">' + 
                        '<input type="hidden" name="products[' + index + '][id]" value="">' + 
                        '<input type="hidden" id="product-value-'+ index +'" name="products[' + index + '][product_id]" value="">' +
                        new_product_group[0].outerHTML +
                    '</td>' + 
                    '<td width="45%">'+ new_product +'</td>' + 
                    '<td width="10%"><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_product(' + index + ');"></td>' + 
                '</tr>';

    $('#co-products > tbody tr:last').before(tr);
    $('#co-product-index').val(index); 
};

/**
 * ������� activity ��������
 * @version: 20120530, zharkov
 */
var remove_activity = function(index)
{
    if (!confirm('Remove activity ?')) return;
    $('#co-activity-' + index).remove();
};

/**
 * ��������� activity ��������
 * 
 * @version: 20120530, zharkov
 * @version: 20130729, sasha
 */
var add_activity = function()
{
    var index					= parseInt($('#co-activity-index').val());
	var select_industry			= $('.sel_industry-0'); 
	var new_select_industry		= select_industry.clone();
	var new_select_activity		=	'<select class="max sel_activity" id="sel_activity-'+ index +
									'" onchange="fill_activities(this,' + "'" + 'sel_speciality' + "'" + ');" data-id="'+ index +'">' +
										'<option value="0">--</option>' +
									'</select>';
	var new_select_speciality	=	'<select id="sel_speciality-'+ index +'" class="max sel_speciality" data-id="'+ index +'">' + 
										'<option value="0">--</option>' +
									'</select>';						
	
	new_select_industry.show();
	new_select_industry.attr('data-id', index);
    new_select_industry.attr('id', 'sel_industry-' + index);
    new_select_industry.removeClass('sel_industry-0');
    new_select_industry.addClass('sel_industry');
	
    tr      =	'<tr id="co-activity-' + index + '">' + 
					'<td width=30%>'+ new_select_industry[0].outerHTML +'<input type="hidden" class="co-activities" value="">' + 
					'<input type="hidden" name="activities[' + index + '][id]" value="0">' + 
					'<input type="hidden" id="activities-value-'+ index +'"name="activities[' + index + '][activity_id]" value="">' + $('#sel_industry > option:selected').text() + '</td>' + 
					'<td width=30%>'+ new_select_activity +'</td>' + 
					'<td width=30%>'+ new_select_speciality +'</td>' + 
					'<td width=10%><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_activity(' + index + ');"></td>' + 
				'</tr>';

    $('#co-activities > tbody tr:last').before(tr);
    $('#co-activity-index').val(index+1);
};

/**
 * ������� ���������� ������
 */
var remove_contactdata = function(index)
{
    if (!confirm('Remove contact data ?')) return;    
    $('#cd-' + index).remove();    
}


/**
 * check titles in Contact Data
 * 
 * @version 20130729, sasha
 */
$(document).on("keyup", ".dc-titles", function(){
	
	var title				= this;
    var dc_titles			= $('.dc-titles');
	var cd_title_cleared	= title.value.replace(/\s+/g, '');
	var exist				= false;
    var index               = parseInt($(this).data('id'));
    var key                 = 0; 

    if (dc_titles.length > 0) 
    {
        dc_titles.each(function()
		{ 
            if (this.value.replace(/\s+/g, '') == cd_title_cleared && this != title && $('#cd-contactdata-' + key).val() == $('#cd-contactdata-' + index).val())
            {	
				exist = true;
            }	
            
            key++;
        });
		
		if (exist)
		{
			$(this).addClass('error');
		}
		else
		{   
            key = 0; 
			dc_titles.each(function()
			{	
                if (this.value.replace(/\s+/g, '') == cd_title_cleared)
                {
                    if (this.value.replace(/\s+/g, '') == cd_title_cleared && $('#cd-contactdata-' + key).val() == $('#cd-contactdata-' + index).val())
                    {    
                        $(this).removeClass('error'); 	
                    }
                } 
                
                key++;
			});
		}	
			
    }

});

/**
 * check product
 * 
 * @version 20130731, sasha
 */
$(document).on("change", ".co-product", function(){
    
    var object      = this; 
    var parent_id   = object.value;
    var object_list = $('.co-product');
    var exist       = false;
    var index       = parseInt($(this).data('id'));
    
    if (parent_id == 0)
    {
         $(this).removeClass('error'); 
         return;
    }    
    
    if (object_list.length > 0) 
    {
        object_list.each(function()
		{ 
            if (this.value == parent_id && this != object) 
            {	
				exist = true;
            }	
        });
		
		if (exist)
		{
			$(this).addClass('error');
            $('#co-product-group-' + index).addClass('error');
            $('#product-value-' + index).val('0');
		}
		else
		{
            $('#co-product-group-' + index).removeClass('error'); 
            $('#product-value-' + index).val(parent_id);
            
            object_list.each(function()
			{	
                if (this.value == parent_id) 
                {
                    $(this).removeClass('error');
                }    
			});
		}	
			
    }
});

/**
 * check contactdata
 * 
 * @version 20130805, sasha
 */
$(document).on("change", ".contactdata-select", function(){
    
    var object          = $(this); 
    var object_list     = $('.dc-titles');
    var exist           = false;
    var index           = parseInt(object.data('id'));
    var input_object    = $('#dc-titles-' + index);

   object_list.each(function()
    {  
        if (this.value == input_object.val() && parseInt($(this).data('id')) != index) 
        {	
            if (object.val() !=  $('#cd-contactdata-' + $(this).data('id')).val()) 
            {
                $('#dc-titles-' + object.data('id')).removeClass('error'); 
            }
            else 
            {
                exist = true;
            }    
        }	
    });
        
    if (exist)
    {
        input_object.addClass('error'); 
    }    
});

/**
 * check industry
 * 
 * @version 20130731, sasha
 */
$(document).on("change", ".sel_industry", function(){
    
    var object      = this; 
    var parent_id   = object.value;
    var object_list = $('.sel_industry');
    var exist       = false;
    var index       = parseInt($(this).data('id'));
       
    $('#sel_activity-' + index).removeClass('error');
    $('#sel_speciality-' + index).removeClass('error');
    
    if (parent_id == 0)
    {
        $('#activities-value-' + index).val(parent_id); 
        $(this).removeClass('error'); 
        return;
    }    
    
    if (object_list.length > 0) 
    {
        object_list.each(function()
		{ 
            if (this.value == parent_id && this != object && parent_id == $('#activities-value-' + $(this).data('id')).val()) 
            {	
				exist = true;
            }	
        });
		
		if (exist)
		{
			$(this).addClass('error');
		}
		else
		{ 
            $('#activities-value-' + index).val(parent_id);
            object_list.each(function()
			{	
                if (this.value == parent_id) 
                {
                    $(this).removeClass('error'); 	
                }    
			});
		}	
			
    }
});

/**
 * check activity
 * 
 * @version 20130731, sasha
 */
$(document).on("change", ".sel_activity", function(){
    
    var object      = this; 
    var parent_id   = object.value;
    var object_list = $('.sel_activity');
    var exist       = false;
    var index       = parseInt($(this).data('id'));

    if (parent_id == 0)
    {
        $('#sel_speciality-' + index).removeClass('error'); 
        $(this).removeClass('error'); 
        return;
    }    
    
    if (object_list.length > 0) 
    {
        object_list.each(function()
		{ 
            if (this.value == parent_id && this != object && parent_id == $('#activities-value-' + $(this).data('id')).val()) 
            {	
				exist = true;
            }	
        });
		
		if (exist)
		{
			$(this).addClass('error');
            $('#sel_industry-' + index).addClass('error');
		}
		else
		{ 
            $('#activities-value-' + index).val(parent_id);
            object_list.each(function()
			{	
                if (this.value == parent_id) 
                {
                    $(this).removeClass('error');
                    $('#sel_industry-' + index).removeClass('error');
                }    
			});
            
            $('#sel_speciality-' + index).removeClass('error');
		}	
			
    }
});

/**
 * check speciality
 * 
 * @version 20130731, sasha
 */
$(document).on("change", ".sel_speciality", function(){
    
    var object      = this; 
    var parent_id   = object.value;
    var object_list = $('.sel_speciality');
    var exist       = false;
    var index       = parseInt($(this).data('id'));

    if (parent_id == 0)
    {
         $(this).removeClass('error'); 
         return;
    }    
    
    if (object_list.length > 0) 
    {
        object_list.each(function()
		{ 
            if (this.value == parent_id && this != object) 
            {	
				exist = true;
            }	
        });
		
		if (exist)
		{
			$(this).addClass('error');
            $('#sel_industry-' + index).addClass('error');
            $('#sel_activity-' + index).addClass('error');
		}
		else
		{ 
            $('#activities-value-' + index).val(parent_id);
            object_list.each(function()
			{	
                if (this.value == parent_id) 
                {
                    $(this).removeClass('error');
                    $('#sel_industry-' + index).removeClass('error');
                    $('#sel_activity-' + index).removeClass('error');
                }    
			});
		}	
			
    }
});

/**
 * check feedstock
 * 
 * @version 20130801, sasha
 */
$(document).on("change", ".co-feedstock", function(){
    
    var object      = this; 
    var parent_id   = object.value;
    var object_list = $('.co-feedstock');
    var exist       = false;
    var index       = parseInt($(this).data('id'));
    
    if (object_list.length > 0) 
    {
        object_list.each(function()
		{ 
            if (this.value == parent_id && this != object) 
            {	
				exist = true;
            }	
        });
		
		if (exist)
		{
			$(this).addClass('error');
            $('#co-feedstock-group-' + index).addClass('error');
             $('#feedstock-value-' + index).val('0');
		}
		else
		{
            $('#co-feedstock-group-' + index).removeClass('error'); 
            $('#feedstock-value-' + index).val(parent_id);
            
            object_list.each(function()
			{	
                if (this.value == parent_id) 
                {
                    $(this).removeClass('error'); 	
                }    
			});
		}	
			
    }
});

/**
 * ��������� �������� ��� � ������
 *
 * @version: 20130729, sasha
 */
var add_contactdata = function()
{
	var cd_index        = $('#cd-index').val();
	
	cd_index    = parseInt(cd_index);
    new_row     = '<td><select name="contactdata[' + cd_index + '][type]" class="max contactdata-select" id="cd-contactdata-'+ cd_index +'" data-id="'+ cd_index +'">' + 
                    '<option value="aim">AIM</option>' + 
                    '<option value="bbm">BBM</option>' + 
                    '<option value="cell">Cell Phone</option>' + 
                    '<option value="email">Email</option>' + 
                    '<option value="fax">Fax</option>' + 
                    '<option value="fb">Facebook</option>' + 
                    '<option value="gt">Google Talk</option>' + 
                    '<option value="icq">ICQ</option>' + 
                    '<option value="msn">MSN</option>' + 
/*
                    '<option value="pfax"' + (cd_type == 'pfax' ? ' selected="selected"' : '') + '>Phone / Fax</option>' +                     
*/  
         
                    '<option value="phone">Phone</option>' + 
                    '<option value="qq">QQ</option>' + 
                    '<option value="skype">Skype</option>' + 
	
/*                    
                    '<option value="telex"' + (cd_type == 'telex' ? ' selected="selected"' : '') + '>Telex</option>' + 
                    '<option value="ttype"' + (cd_type == 'ttype' ? ' selected="selected"' : '') + '>Teletype</option>' + 
*/ 

                    '<option value="www">Website</option>' + 
                    '</select><input type="hidden" name="contactdata[' + cd_index + '][id]" value="0"></td>' + 
                    '<td><input type="text" class="max dc-titles" name="contactdata[' + cd_index + '][title]" value="" data-id="'+ cd_index +'" id="dc-titles-'+ cd_index +'"></td>' + 
                    '<td><input type="text" class="max cd-descriptions" name="contactdata[' + cd_index + '][description]" value=""></td>' + 
                    '<td><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_contactdata(' + cd_index + ');"></td>';
	
	$('#cd-list > tbody tr:last').before("<tr id=\"cd-" + cd_index + "\">" + new_row + "</tr>");    
    
    $('#cd-index').val(cd_index+1);

}


/**
 * OnLoad
 * @version 20120501, zharkov
 */
$(function(){
   
    $("#co-title").focus();
    
    $("#parent_title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });
    
    $( "#parent_title" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url     : "/company/getlistbytitle",
                data    : {
                    maxrows : 25,
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
                $('#parent_link').attr('href', '/company/' + ui.item.value);
                $('#parent_link').text(ui.item.label);
                $('#parent_title').val(ui.item.label);
                $('#parent_id').val(ui.item.value);
                
                $('#parent_link').show();
                $('#img_reload').show();
                
                $('#parent_title').hide();
            }
            else
            {
                $('#parent_id').val(0);                
            }
            
            return false;
            
        },
        open: function() {

        },
        close: function() {

        }
    });

    
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
 * ���������� ������� �� ������ ��������
 * 
 * @version 20120501, zharkov
 */
var find_company = function()
{
    $('#parent_link').hide();
    $('#img_reload').hide();
    $('#parent_id').val(0);
    $('#parent_title').val('');    
    $('#parent_title').show();
};


/**
 * ��������� ���������� ������ activities
 * @version: 20120525, zharkov
 * @version: 20130730, sasha
 */
var fill_activities = function(object, list_object)
{
	var parent_id	= object.value;
	var id			= parseInt($(object).data('id'));
   
    // ������� ����� activities ��� ��������� industry
    if (list_object == 'sel_activity')
    {
        $('#sel_speciality-' + id).empty();
        $('#sel_speciality-' + id).prepend($('<option value="0">--</option>'));        
    }
	
    if (parent_id > 0)
    {
        $('#' + list_object + '-' + id).prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
       
        $.ajax({
            url: '/activity/getlist',
            data : {
                parent_id : parent_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
					$('#' + list_object + '-' + id).empty();
					$('#' + list_object + '-' + id).prepend($('<option value="0">--</option>'));
					
					start_index = 0;
					for (i = start_index; i < json.list.length; i++)
					{
						el = json.list[i];
						$('#' + list_object + '-' + id).append($('<option value="' + el.id + '">' + el.name + '</option>'));
					}    
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
		$('#' + list_object + '-' + id).empty();
		$('#' + list_object + '-' + id).prepend($('<option value="0">--</option>'));
    } 
	
	id = id+1;
	$('#activities-value-' + id).val(parent_id);
};


/**
 * ��������� ������ ���������
 * @version: 20120529, zharkov
 * @version: 20130731, sasha
 */
var fill_products = function(object, list_object, target_object)
{
    var select_list = $('.' + list_object);
    var parent_id   = parseInt(object.value);
    var exist       = false;
    var index       = parseInt($(object).data('id'));
    var error       = false;
   
    $('#co-'+ target_object +'-' + index).removeClass('error'); 
   
    if (parent_id > 0)
    {
        if (select_list.length > 0)
        {
            select_list.each(function()
            {
                if (parseInt(this.value) == parent_id && this != object && $(object).val() == $('#co-'+ target_object +'-value-' + $(this).data('id')).val()) 
                {
                    exist = true; 
                }
            }); 
        }    
        
		if (exist)
		{
			$(object).addClass('error');
            $('#'+ target_object +'-value-' + index).val('0');
		}
		else
		{
			select_list.each(function()
			{	
				 if (parseInt(this.value) == parent_id) 
                 {
                    $(this).removeClass('error'); 
                    $('#co-'+ target_object +'-' + index).removeClass('error'); 
                 }   
			});
            
            $('#'+ target_object +'-value-' + index).val(parent_id);
		}	
        
        $('#co-'+ target_object +'-' + index).empty();
        $('#co-'+ target_object +'-' + index).prepend($('<option selected="" value="0">Loading...</option>'));        

        $.ajax({
            url: '/product/getlist',
            data : {
                parent_id : parent_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {  
                    $('#co-'+ target_object +'-' + index).empty();
                    $('#co-'+ target_object +'-' + index).prepend($('<option value="0">--</option>'));
                    
                    var start_index = 0;
                    var i           = 0;
                    var el          = 0;
					for (i = start_index; i < json.list.length; i++)
					{
						el = json.list[i];
						$('#co-'+ target_object +'-' + index).append($('<option value="' + el.id + '">' + el.name + '</option>'));
					}
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
        $('#co-'+ target_object +'-' + index).empty();
        $('#co-'+ target_object +'-' + index).prepend($('<option value="0">--</option>'));
        $('#'+ target_object +'-value-' + index).val('0');
        $(object).removeClass('error');
    } 
};


$(function(){
    $('.company-relation').on('change', function(){
        var relation_id = $(this).val();
        
        if (relation_id == 6 || relation_id == 7 || relation_id == 8)
        {
            $('.company-rel-stock-agent').show();
        }
        else
        {
            $('.company-rel-stock-agent').hide();
            $('.company-rel-stock-agent input').val('');
            $('.company-rel-stock-agent select option').removeAttr('selected');
            $('.company-rel-stock-agent select option:first').attr('selected', 'selected');
        }
    });
});

/*
* Гончар. Показать окно добавления региона
*/

var show_win_add_city = function()
{
	//$("#add-country").chosen({no_results_text: "Oops, nothing found!"});
	//$( "body" ).css({'overflow' : 'hidden'});
	$( "#dialog-city-region" ).dialog({modal: true});
}

/*
* Гончар. Отправка формы добавления города и региона на сервер
*/

var add_city_region = function()
{
	var region   = $('#add-region').val();
	var city   = $('#add-city').val();
	var country   = $('#add-country').val();
	var dialcode   = $('#add-dial-code').val();

	if(region == '' ||
		city == '' ||
		dialcode == ''
	){
		$('#dialog-city-region > .error').html('<p>Please fill in required fields!</p><p>Required fields marked with *.</p>');
	}else{	
		var pars = "method=addCity&region="+region+"&city="+city+"&country="+country+"&dialcode="+dialcode;	
		//console.log(pars);
		
		//save region
		//сохраняем регион
		$.ajax({
			url     : "/region/save",
			data    : {
				country_id : country,
				title : region
			},
			success : function( data ) {
			
				//save city
				//сохраняем город
				$.ajax({
					url     : "/city/save",
					data    : {
						country_id : country,
						region_id : data.region_id,
						title : city,
						dialcode : dialcode
					},
					success : function( data_city ) {						
						console.log(data_city);
						
						//добавляем город в конец списка
						$("#city").append( $('<option value="'+data_city.city_id+'" selected>'+city+'</option>'));							
					}
				});	
				
				console.log(data);
				
				//добавляем регион в конец списка
				$("#region").append( $('<option value="'+data.region_id+'" selected>'+region+'</option>'));				
			}
		});
		
		$("#country [value='"+country+"']").attr("selected", "selected");
		
		$( "#dialog-city-region" ).dialog( "close" );
	}
}