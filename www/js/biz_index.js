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
                    fill_select("#products", json.products, {'value' : 0, 'name' : "--"});
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
}

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
});

/*@version 20130515 Sasha remove company and role from filter*/
$(document).on("click", "span.icon.delete.company-role-remove", function(){
	
	$(this).parent().parent().remove();
	
	if ($('.company-role-add').length == 0)
	{
		$('.company-role-remove').last().toggleClass('add company-role-add delete company-role-remove');
	}	
});

/**
 * link for orders in bizes
 * @version 20130723, sasha
 */
$('.biz-order-href').on('click', function(){
	
	location.href = $(this).data('href');
});