/**
 * ��������� �������� ��� ��������� ���� ������������
 * @version 20120618, zharkov
 */
var select_role = function(role_id, ROLE_USER)
{
    if (role_id >= ROLE_USER)
    {
        $('.sites').show();
    }
    else
    {
        $('.sites').hide();
    }
    
    if (role_id < ROLE_USER && role_id > 0)
    {
        $('#mailboxes').show();
        $('#last_email_number').show();
    }
    else
    {
        $('#mailboxes').hide();
        $('#last_email_number').hide();
    }
    
    if (role_id > 0)
    {
        $('#chat_icon_park').show();
    }
    else
    {
        $('#chat_icon_park').hide();
    }
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
 * ��������� �������� ��� � ������
 */
var add_contactdata = function()
{
    var cd_index    = $('#cd-index').val();
    var cd_type     = $('#cd-type').val();
    var cd_title    = $('#cd-title').val();
    
    var cd_title_cleared = cd_title.replace(/\s+/g, '');

    
    // ������ �������� �� ���������
    if (cd_title_cleared == '') return;
    
    // �� ��������� ��������, ������� ��� ����
    var title_exists    = false;
    var cd_titles       = $('.cd-titles');
    if (cd_titles.length > 0) 
    {
        cd_titles.each(function(){ 
            if ($(this).val().replace(/\s+/g, '') == cd_title_cleared) 
            {
                title_exists = true;
                Message('Such contact data already exists!', 'warning');
                
                return;
            }
        });
    }
    
    if (title_exists) return;
    
    cd_index    = parseInt(cd_index) + 1;
    new_row     = '<td><select name="contactdata[' + cd_index + '][type]" class="max">' + 
                    '<option value="aim"' + (cd_type == 'aim' ? ' selected="selected"' : '') + '>AIM</option>' + 
                    '<option value="bbm"' + (cd_type == 'bbm' ? ' selected="selected"' : '') + '>BBM</option>' + 
                    '<option value="cell"' + (cd_type == 'cell' ? ' selected="selected"' : '') + '>Cell Phone</option>' + 
                    '<option value="email"' + (cd_type == 'email' ? ' selected="selected"' : '') + '>Email</option>' + 
                    '<option value="fax"' + (cd_type == 'fax' ? ' selected="selected"' : '') + '>Fax</option>' + 
                    '<option value="fb"' + (cd_type == 'fb' ? ' selected="selected"' : '') + '>Facebook</option>' + 
                    '<option value="gt"' + (cd_type == 'gt' ? ' selected="selected"' : '') + '>Google Talk</option>' + 
                    '<option value="icq"' + (cd_type == 'icq' ? ' selected="selected"' : '') + '>ICQ</option>' + 
                    '<option value="msn"' + (cd_type == 'msn' ? ' selected="selected"' : '') + '>MSN</option>' + 
/*
                    '<option value="pfax"' + (cd_type == 'pfax' ? ' selected="selected"' : '') + '>Phone / Fax</option>' +                     
*/                    
                    '<option value="phone"' + (cd_type == 'phone' ? ' selected="selected"' : '') + '>Phone</option>' + 
                    '<option value="qq"' + (cd_type == 'qq' ? ' selected="selected"' : '') + '>QQ</option>' + 
                    '<option value="skype"' + (cd_type == 'skype' ? ' selected="selected"' : '') + '>Skype</option>' + 
/*                    
                    '<option value="telex"' + (cd_type == 'telex' ? ' selected="selected"' : '') + '>Telex</option>' + 
                    '<option value="ttype"' + (cd_type == 'ttype' ? ' selected="selected"' : '') + '>Teletype</option>' + 
*/                    
                    '<option value="www"' + (cd_type == 'www' ? ' selected="selected"' : '') + '>Website</option>' + 
                    '</select><input type="hidden" name="contactdata[' + cd_index + '][id]" value="0"></td><td><input type="text" class="max cd-titles" name="contactdata[' + cd_index + '][title]" value="' + cd_title + '"></td><td><img src="/img/icons/cross-circle.png" style="cursor: pointer;" onclick="remove_contactdata(' + cd_index + ');"></td>';

    $('#cd-list > tbody tr:last').before("<tr id=\"cd-" + cd_index + "\">" + new_row + "</tr>");    
    
    $('#cd-title').val('');
    $('#cd-index').val(cd_index);    
}


/**
 * OnLoad
 * @version 20120501, zharkov
 */
$(function(){
   
    $("#company_title").keypress(function(event){
        if(event.keyCode == 13) 
        {
            return false;
        }
    });


    $( "#company_title" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url     : "/company/getlistbytitle",
                data    : {
                    maxrows : 25,
                    title   : request.term
                },
                success : function( data ) {
                    response( $.map( data.list, function( item ) {
                       var full_title = item.company.title+" ("+item.country+")";
                        // console.log(item);
						// var full_title = item.company.title;
						return {
                            //label: item.company.title,
                            label: full_title,
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
                $('#company_link').attr('href', '/company/' + ui.item.value);
                $('#company_link').text(ui.item.label);
                $('#company_id').val(ui.item.value);
                
                $('#company_link').show();
                $('#img_reload').show();
                
                $('#company_title').hide();
                
                //$('#company_title').val(ui.item.label);
                //$('#company_id').val(ui.item.value);
            }
            else
            {
                $('#company_id').val(0);                
            }
            
            return false;
            
        },
        open: function() {

        },
        close: function() {

        }
    });

    
    // birthday
    $('#birthday').datepicker({
        showWeek: true
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
	
$('#picker').colpick({
	layout:'hex',
	submit:0,
	colorScheme:'dark',
	onChange:function(hsb,hex,rgb,el,bySetColor) {
		$(el).css('border-color','#'+hex);
		// Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
		if(!bySetColor) $(el).val('#'+hex);
	}
}).keyup(function(){
	$(this).colpickSetColor(this.value);
});	
	
	$('#cd-title').on("focusout", function(){
		add_contactdata();
	});
    
});

/**
 * ���������� ������� �� ������ ��������
 * 
 * @version 20120501, zharkov
 */
var find_company = function()
{
    $('#company_link').hide();
    $('#img_reload').hide();
    $('#company_id').val(0);
    $('#company_title').val('');
    
    $('#company_title').show();
};

/*
*	Select all mailboxes
*/
var togle_mailboxes = function()
{
	//console.log($(".mailbox"));
	var status = "checked";
	
	$(".mailboxes").each( function() {
		$(this).attr("checked",status);
	});

	/*
	$(".mailbox").each( function() {
	$(this).attr("checked",status);
	});*/
}

/**
 * Отменяет изменение картинки
 * @version: 20120928, zharkov
 */
var person_change_pic_cancel = function(event)
{
    $('#person-pic-new').hide();
    $('#person-pic').show();
    event.preventDefault()    
};

/**
 * Показывает блок изменения картинки
 * @version: 20120928, zharkov
 */
var person_change_pic = function()
{
    $('#person-pic-new').show();
    $('#person-pic').hide();
};