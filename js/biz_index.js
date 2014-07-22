$(function()
{
   
    //меняем текст Toolbox на Search Tools
    $('button.icon-hide').text("");
    $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Hide Search Tools");
    //Меняем текст кнопки Search Tools
    $("button.icon-hide").live("click", function()
    {
        if($('.column-side-hidden').length>0){
            $('button.icon-hide').text("");
            $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Show Search Tools");
        }else{
            $('button.icon-hide').text("");
            $('button.icon-hide').append("<i class='glyphicon glyphicon-th'></i>&nbsp;Hide Search Tools");
        }
    });
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
});
/**
 * @version 20130130, zharkov
 * @version 20130515, sasha
 */
var company_list = function(element){
  
    $(".supinv_company").autocomplete({
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
            
            return false;
            
        },
        open: function() { },
        close: function() { },
        focus: function(event, ui) 
        { 
            return false;
        }        
    });
}

/**
 * @version 20131405 Sasha
 * Populates the drop down list of products
 */
var bind_products = function(team_id, in_biz)
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

/*@version 20130515 Sasha remove company and role from filter*/

$(document).on("click", "span.icon.add.company-role-add", function(){
    $('.company-role').append($(this).parent().parent().clone());
	
	if ($('.company-role-input').length < 6)
	{	
		$(this).toggleClass('add company-role-add delete company-role-remove');
	}
	else
	{
		$('.company-role-add').toggleClass('add company-role-add delete company-role-remove');
                
	}	
	
	$('.company-role-input').last().val('');
	$('.supinv_company_id').last().val(0);
	$('.biz-co-role :nth-child(1)').last().attr("selected", "selected"); 
        $(".chosen-select").trigger("chosen:updated");
       
});

/*@version 20130515 Sasha remove company and role from filter*/
$(document).on("click", "span.icon.delete.company-role-remove", function(){
	
	$(this).parent().parent().remove();
	
	if ($('.company-role-add').length == 0)
	{
            $('.company-role-remove').last().toggleClass('add company-role-add delete company-role-remove');
            $(".chosen-select").trigger("chosen:updated");
    }	
});

/**
 * link for orders in bizes
 * @version 20130723, sasha
 */
$('.biz-order-href').on('click', function(){
	
	location.href = $(this).data('href');
});
$(".chosen-select").chosen({
    no_results_text: "Oops, nothing found!",
    disable_search_threshold: 10
}); 


 
/*
$(document).on('load', function(){
    $('.company-role-add').live('click', function(){
	$('.company-role').append($(this).parent().parent().clone());
	
	if ($('.company-role-input').length < 6)
	{	
		$(this).toggleClass('add company-role-add delete company-role-remove');
	}
	else
	{
		$('.company-role-add').toggleClass('add company-role-add delete company-role-remove');
	}	
	
	$('.company-role-input').last().val('');
	$('.supinv_company_id').last().val(0);
	$('.biz-co-role :nth-child(1)').last().attr("selected", "selected");            
    });
});
*/